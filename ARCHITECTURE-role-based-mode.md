# Role-Based Membership Mode — Architecture (WPS-7875)

## Summary

Membership For WooCommerce now supports two mutually-exclusive membership flows:

- **Purchase-based** (existing, default): customers buy a membership plan/product.
- **Role-based** (new): access is granted by WordPress role/capability, no checkout required.

The admin picks a mode once (activation-time screen) and can switch later from settings.
Switching is **non-destructive**: it only changes an option and which admin menus are
visible — it never deletes or migrates data from either flow. An existing purchase-mode
customer's access is **silently preserved** even while role mode is the active mode.

## Why this design

The purchase-based flow is effectively the entire pre-existing plugin (~200KB admin class,
large public/common classes). Physically restructuring all of it in one pass, on a shipped
plugin with no automated test suite, was judged too risky. Instead, the existing code is
**completely untouched** and the new flow is **fully additive**. See
`includes/legacy/README.md` for the full reasoning and the deferred physical-relocation
follow-up.

## Integration points into the existing codebase

Exactly one existing file was touched for the feature work, by exactly two lines, in the
`Membership_For_Woocommerce` constructor:

```php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/mode/class-mfw-mode-controller.php';
Mfw_Mode_Controller::init();
```

Everything else new is required from within `Mfw_Mode_Controller::load_files()`. (WPS-7885
additionally touches the version literals in `membership-for-woocommerce.php` and the
changelog in `README.txt` — an administrative release task, not a flow-isolation concern.)

## Mode controller

`includes/mode/class-mfw-mode-controller.php` (`Mfw_Mode_Controller`) is the single source of
truth for the active mode:

- Option: `wps_membership_mode` — `'purchase'` (default) or `'role'`. Absent option (existing
  installs upgrading to this version) resolves to `'purchase'`, so nothing changes for them.
- `get_mode()` / `is_purchase_mode()` / `is_role_mode()` / `is_mode_selected()`.
- `set_mode( $mode )` — updates the option only, fires `mfw_membership_mode_switched`.
- `init()` requires every new file and boots the mode-aware pieces; called once, from the one
  integration point above.

## Admin menu visibility ("no hybrid mode")

`includes/mode/class-mfw-admin-menu-visibility.php` hides the *inactive* mode's admin
configuration surfaces via a late (`admin_menu`, priority 999) `remove_menu_page()` /
`remove_submenu_page()` call:

- Role mode active → hides the purchase flow's own settings/dashboard submenu
  (`membership_for_woocommerce_menu`, under `wps-plugins`) and its "Membership Plans" CPT
  menu (`edit.php?post_type=wps_cpt_membership`, which also carries the "Members" CPT
  submenu nested under it).
- Purchase mode active (default) → the role-based module simply never registers its own menu
  item in the first place (`Mfw_Role_Based_Admin::register_menu()` is self-gated), so there is
  nothing to hide on that side.

This is a navigation-only concern. It never disables the hidden mode's underlying hooks,
data, or customer-facing access — that's what makes "silently preserved" true by construction
rather than by extra logic.

## Onboarding / mode selection (WPS-7878)

The existing setup wizard is a React app (`/src`) with no step-registration architecture, so
inserting a step would mean editing it. Instead, `includes/mode/class-mfw-onboarding-mode-screen.php`
adds a brand-new, standalone "Membership Mode" admin page (registered via the plugin's existing
`wps_add_plugins_menus_array` filter extension point — no existing file edited) plus a
first-run admin notice linking to it. Choosing **purchase** falls through to the existing
dashboard/React wizard exactly as before (untouched). Choosing **role** just sets the option;
the admin then configures levels from the new Role-Based Membership page.

Once a mode is chosen, the same "Membership Mode" page renders the switch UI instead (via the
`mfw_render_mode_switch_ui` action), owned by `Mfw_Role_Based_Mode_Switch` (WPS-7883).

## Mode switching (WPS-7883)

`includes/role-based/class-mfw-role-based-mode-switch.php`: a toggle button + JS confirmation
modal + AJAX handler (`mfw_switch_membership_mode`) that calls `Mfw_Mode_Controller::set_mode()`.
**Behavior spec** (the epic's flagged open decision, resolved): switching changes only the mode
option and admin-menu visibility. No user meta, post meta, or role-based DB rows are ever
touched by a switch.

## Role-based data layer (WPS-7879)

New tables (own schema, `dbDelta`, versioned via option `mfw_role_based_db_version`, currently
schema `1.1.0`):

| Table | Purpose |
|---|---|
| `{prefix}mfw_role_membership_levels` | Level definitions: name, mapped `wp_role`, extra capabilities (JSON), numeric `rank`, status |
| `{prefix}mfw_role_membership_restrictions` | Which object (`post`/`page`/`product`/`term` + id) requires which explicit level ids, OR a minimum rank (a row with `level_id = 0` and `min_rank` set; mutually exclusive with explicit level rows) |
| `{prefix}mfw_role_membership_user_log` | Audit trail of assign/revoke events |

`includes/role-based/data/class-mfw-role-based-repository.php` (`Mfw_Role_Based_Repository`)
is the only data-access layer for this module. Its access-check primitive
(`user_has_role_access()`) is **intentionally separate** from
`Membership_For_Woocommerce_Global_Functions::wps_mfw_check_user_has_active_membership()` —
per the epic, no restriction logic is shared between the two flows.

**Multiple levels per user**: a user's assigned level ids live in a single array-valued user
meta key (`mfw_role_membership_level_ids`). Assigning/revoking a level only adds/removes the
mapped WP role and capabilities if no *other* level still held by the user also grants them
(`revoke_level_from_user()` computes the remaining-levels' union first).

**Escape hatches** (`user_bypasses_restrictions()`, mirrors the reference "Members" plugin):
a site admin, the post/page/product's author, anyone who can `edit_post` that specific object,
anyone who can `manage_categories` (for term restrictions), and anyone holding the
`mfw_restrict_content` capability always see restricted content regardless of level.

**Role creation**: `save_level()` calls `add_role()` on the fly if the chosen `wp_role` slug
isn't already a registered WordPress role — our equivalent of a dedicated "create custom role"
screen, folded into the existing Add Level form instead of a separate role editor.

**Existing-role capability preview**: `Mfw_Role_Based_Admin::role_capabilities_map()` sends
every registered role's current capabilities (restricted to the ones our picker shows) to the
browser. Selecting an existing role in the "WordPress Role" dropdown ticks the matching
checkboxes live, so the admin can see exactly what that role already grants before mapping a
level onto it — rather than guessing. Typing a new role slug doesn't trigger this (nothing to
preview for a role that doesn't exist yet), and it clears the new-role field to avoid the
save logic silently preferring a stale typed slug over the just-selected existing role.

**Export/import**: `Mfw_Role_Based_Repository::export_levels()`/`import_levels()` — our own
simple JSON shape (not a copy of any other plugin's export format), wired to
`admin-post.php?action=mfw_role_based_export_levels` / `..._import_levels` in
`Mfw_Role_Based_Admin`.

## Admin UI (WPS-7880)

`includes/role-based/admin/class-mfw-role-based-admin.php`: own "Role-Based Membership" page
with:
- a **Private Site** toggle (`mfw_role_based_private_site` option) — see Frontend below;
- the levels manager: **add, edit, and delete**. Editing repopulates the same form (name, role,
  rank, status, capabilities) from a `data-level` JSON attribute on the row and updates in
  place, rather than only ever creating new rows;
- a **rank** field, a **status** (active/inactive) field, and a full, grouped **capability
  checklist** (`capability_groups()` — every WordPress core capability plus every real
  WooCommerce capability, grouped for readability, plus an auto-discovered "Other" group for
  any capability already present on a role that isn't in the curated list — a custom post
  type's capability, another plugin's, etc. — so nothing on the site is invisible to it; see
  `discover_other_capabilities()`). Saving a level makes the mapped WP role's **actual**
  capabilities match what's checked (`Mfw_Role_Based_Repository::sync_role_capabilities()`):
  unchecking a capability really removes it from the role, matching the reference "Members"
  plugin's real role-editing behavior, not just a top-up. This is scoped to only the
  capabilities the picker knows about, so a capability the role already had from WordPress
  core or another plugin outside that list is never touched — mapping a level onto an existing
  role like `editor` won't strip its normal abilities;
- export/import;
- a restriction metabox on `post`/`page`/`product` edit screens, offering either an explicit
  level checklist or a **minimum-rank** threshold (mutually exclusive);
- a matching restriction field on **every public taxonomy's** term-edit screen
  (`{taxonomy}_edit_form_fields`), via `includes/role-based/admin/partials/role-based-term-restriction.php`.

`includes/role-based/admin/class-mfw-role-based-user-assignment.php` closes the "how does a
user actually get a level" gap identified after the initial delivery, two ways:
- a **Role-Based Membership** checkbox panel on every user's profile edit screen
  (`show_user_profile`/`edit_user_profile`), saved via `personal_options_update`/
  `edit_user_profile_update`;
- **bulk actions** on the Users list screen — one "Assign: <level>" and one "Revoke: <level>"
  entry per level (WordPress bulk-action dropdowns can't take a runtime parameter, so each
  level gets its own fixed entry, the same trick the reference plugin doesn't need since it
  bulk-assigns raw roles, but we do since levels are our own abstraction on top of roles).

## Frontend (WPS-7881)

`includes/role-based/public/class-mfw-role-based-public.php`:

- My Account "Membership" tab (new endpoint `role-membership`) listing all of the user's
  current levels (a user can hold more than one).
- **Content restriction via `the_content`/`the_excerpt` filtering** (priority 8) that swaps in
  a message instead of blocking the whole page — matches the reference plugin's friendlier UX.
  The message is a per-post override (post meta `mfw_role_membership_restricted_message`) or a
  site-wide default (option `mfw_role_based_default_restricted_message`).
- **Taxonomy term archive restriction** via `template_redirect` (term archives have no single
  "content" to filter, so this blocks the page with a 403, unlike post/page restriction).
- **Private Site**: `template_redirect` (priority 0) redirects logged-out visitors to the
  **Membership Login page** (see below) for every front-end request except the
  login/registration screens and the login page itself, while role mode and the Private Site
  setting are both on. The originally-requested URL is carried through as `?redirect_to=`, so
  a successful login lands the visitor back where they were headed.
- Its own `woocommerce_is_purchasable` filter for products, independent of the purchase-flow's
  filters of the same hook.
- **REST hiding**: `rest_request_before_callbacks` returns a 403 `WP_Error` for single-item
  GET requests (`/wp/v2/posts/{id}`, `/wp/v2/pages/{id}`) against a restricted, inaccessible
  object. Collection/list REST endpoints are **not** filtered in this pass — documented
  limitation, not silently dropped.
- Shortcodes: `[wps_role_membership_restricted level_id="X"]` (wrap content, shown to any
  member holding that level, or any level at all if `level_id` is omitted) and
  `[wps_role_membership_level_name]` (display the user's highest-rank level name).

## Membership login page + flow

`includes/role-based/public/class-mfw-role-based-login.php` (`Mfw_Role_Based_Login`):

- A real WordPress **Page** titled "Membership Login" (option `mfw_role_based_login_page_id`
  tracks its id), auto-created the moment the admin switches into role mode
  (`Mfw_Mode_Controller::set_mode()` calls `maybe_create_login_page()` directly — not via the
  `mfw_membership_mode_switched` action, because this module's own hook registration is itself
  gated on role mode already being active, which mid-switch it isn't yet). Its content is just
  the `[wps_role_membership_login]` shortcode, so the admin can move/edit/re-theme the page
  freely — the shortcode is the only thing that matters.
- The shortcode renders the **full flow**: an already-logged-in state (with a link to the
  My Account membership tab and a logout link), an error notice after a failed attempt, the
  login form itself, and a "lost your password" link.
- The form is core WordPress's own `wp_login_form()`/`wp-login.php` handling, not a hand-rolled
  authenticator — this keeps nonce handling and password verification exactly as secure as
  core itself. A failed attempt is bounced back to whichever page the form was on (via
  `wp_login_failed` + `wp_get_referer()`, the standard recipe for custom login pages) with
  `?login=failed`, rather than falling through to wp-login.php's own error screen. This never
  engages for logins attempted directly on wp-login.php/wp-admin, so the default admin login
  is completely unaffected.
- Successful login redirects to `?redirect_to` if present (how Private Site sends visitors
  here), else to the My Account "Membership" tab.

### Login page design (admin-configurable)

Three new options, all editable from the "Membership Login Page" card on the "Role-Based
Membership" admin page (`Mfw_Role_Based_Admin::LOGIN_HEADING_OPTION` /
`LOGIN_NOTICE_OPTION` / `LOGIN_NOTICE_ENABLED_OPTION`):

- a plain-text **heading** shown above the login form;
- a rich-text **notice** (a real `wp_editor()`/TinyMCE field, not a plain textarea, so the
  admin can actually format/link/style it — this is the "design" control) shown when enabled,
  useful for announcements, downtime messages, or promos;
- an **enabled** toggle for the notice.

Saved via a new AJAX action (`mfw_role_based_save_login_design`), sanitized with
`wp_kses_post()` for the notice and `sanitize_text_field()` for the heading. The shortcode
renders both (heading + notice, if enabled) above either the login form or the "already
logged in" state, wrapped in a small self-contained `<style>` block (the login page is
front-end, not wp-admin, so our admin stylesheet isn't loaded there).

### Login activity log

Every successful login by a user who holds at least one role-membership level is recorded
(`Mfw_Role_Based_Login::record_login()`, hooked to core's `wp_login` action) — reuses the
existing `mfw_role_membership_user_log` audit table with `action = 'login'` rather than a new
table, since this is conceptually the same "event in a member's membership history" as an
assign/revoke. A plain admin login (no level held) is never recorded — this is membership
activity, not a general site login log. Shown as a "Recent Member Logins" table (member,
level, timestamp — last 50) on the "Role-Based Membership" admin page via
`Mfw_Role_Based_Repository::get_login_log()`.

## Member discounts (levels)

Each level now has a `discount_percent` (0-100) column. `Mfw_Role_Based_Repository::get_user_discount_percent()`
returns the **highest** discount among a user's active levels — multiple levels never stack.
`includes/role-based/class-mfw-role-based-pricing.php` (`Mfw_Role_Based_Pricing`) applies it:

- `woocommerce_get_price_html` — shows the struck-through original price, the discounted
  price, and a small badge on shop/product pages, using WooCommerce's own
  `wc_format_sale_price()` so it looks like a native sale price.
- `woocommerce_before_calculate_totals` — actually discounts cart line items. Always
  recalculates from a **freshly fetched** product's price rather than the cart item's
  already-mutated price object, which is the standard-safe pattern for this hook: WooCommerce
  can fire it more than once per request, and recalculating from scratch each time means the
  discount can never compound.

This is a simple, site-wide-per-level discount (not per-product/per-category rules) —
a reasonable v1 scope; a future per-product/category discount rule engine is a natural
follow-up if needed.

## Bug fix: AJAX requests were skipping all role-based frontend hooks

While wiring the discount into cart totals, a real gap surfaced in `Mfw_Role_Based_Loader`:
`Mfw_Role_Based_Public::init()` (product-restriction filter, content restriction, shortcodes,
pricing) was only ever called on the `is_admin() === false` branch. But WooCommerce's own
AJAX endpoints (add-to-cart, update-cart, cart fragments) run through `admin-ajax.php`, where
`is_admin()` is **true** — so on any AJAX-driven cart interaction (the default on most modern
themes), none of these hooks were registered at all: a restricted product could be added to
cart via AJAX regardless of the visitor's level, and the discount wouldn't apply either. Fixed
by also running `Public::init()` when `wp_doing_ajax()` is true, alongside (not instead of)
`Admin::init()` — both safely coexist on the same admin-ajax.php request since only the one
hook relevant to whichever specific action fired ever actually runs.

## Notifications (WPS-7882)

`includes/role-based/notifications/class-mfw-role-based-notifications.php`: `wp_mail()` on
`mfw_role_based_after_assign_level` / `..._after_revoke_level` (fired by the repository).
Matches the plugin's existing notification style — there are no `WC_Email` subclasses
anywhere in this codebase, only raw `wp_mail()` calls.

**Open scope decision (unresolved, by design):** SMS/WhatsApp notifications. No such
integration exists anywhere in this plugin today. Rather than guess at a provider/API, a
filter extension point (`mfw_role_based_notification_channels`) is exposed so this can be
added later once the product owner decides on scope/provider.

## Campaigns

`includes/role-based/class-mfw-role-based-campaigns.php` (`Mfw_Role_Based_Campaigns`) —
admin-composed broadcast emails, distinct from the per-event assign/revoke notifications
above: the admin writes a subject + rich-text message (`wp_editor()`) and sends it to one of
four audiences:

- **Members** — every user currently holding at least one active role-membership level
  (`Mfw_Role_Based_Repository::get_member_user_ids()`).
- **Non-Members** — every other registered user (`get_non_member_user_ids()`).
- **Specific level(s)** — a checkbox picker of levels appears when this is chosen.
- **Everyone**.

Sending (`Mfw_Role_Based_Campaigns::send()`) is a synchronous `wp_mail()` loop — matching this
plugin's existing notification style (no queue/cron infrastructure exists anywhere in this
codebase) — with `{display_name}`/`{user_email}`/`{site_name}` placeholders replaced
per-recipient, and `text/html` content type for the rich-text message. Every send is logged
to a new `{prefix}mfw_role_membership_campaigns` table (name, subject, message, audience,
level ids, recipient count, who sent it, when) and shown in a read-only "Campaign History"
table.

**Wired as a plain form submit to `admin-post.php`**, not AJAX — two reasons: WordPress's own
`editor.js` syncs the `wp_editor()` content into its textarea automatically on form submit
(the AJAX flows elsewhere in this module, like the login-page notice, had to hand-roll that
TinyMCE-content-reading step in JS), and a send to a large audience isn't constrained by a
`fetch()`'s implicit timeout expectations.

**Known scale limitation** (documented, not silently ignored): a synchronous loop against a
very large audience (thousands of users) on a single request risks a PHP timeout. There's no
background job runner anywhere in this plugin to build on top of; a real queue/batch-sending
system would be the natural follow-up if audience sizes grow large enough to need it.

**Member/non-member resolution caveat**: `get_member_user_ids()` uses `WP_User_Query` to find
users who merely *have* the `mfw_role_membership_level_ids` meta key, then filters out empty
arrays in PHP (WP's meta query can't distinguish "key present with an empty array" from "key
present with values" at the SQL level). Fine at normal site scale; documented rather than
solved with a more complex query, consistent with the scale caveat above.

## Advanced features (v3.3.0)

Everything below implements `ADVANCED-FEATURES-ROADMAP.md`, on top of the v3.1.3 foundation
above. Two roadmap items were deliberately **not** built — see "Explicitly deferred" at the
end of this section — and every other schema/behavior change described here is additive to
what already existed; nothing above this section had to change its own public contract.

### Data model change: per-assignment metadata

`Mfw_Role_Based_Repository::USER_LEVELS_META_KEY` changed shape from a flat array of level
ids (`[1, 2, 3]`) to a map (`{level_id: {assigned_at, expires_at, reminded}}`). This was a
breaking internal format change made before any release of this feature (nothing has shipped
yet), so no migration path was needed — `get_user_level_map()` still transparently upgrades
the old flat-array shape if it's ever encountered, as a defensive fallback rather than a
required migration. `get_user_level_ids()` and everything built on it (`get_user_levels()`,
`user_has_role_access()`, restriction checks, etc.) is unchanged from the caller's
perspective — only code that needs `assigned_at`/`expires_at` (expiry, content dripping,
CSV export) reads the map directly.

### Membership lifecycle

- **Auto-expiry**: a level's `expiry_days` (0 = never) is used to compute a fixed
  `expires_at` at assignment time. `includes/role-based/class-mfw-role-based-expiry.php`
  runs a daily WP-Cron job (`mfw_role_based_expiry_check`) that revokes anything past its
  `expires_at` and sends a one-time reminder email 3 days before (tracked via the
  assignment's `reminded` flag, so it's never sent twice).
- **Self-service signup**: `[wps_role_membership_register]`
  (`includes/role-based/public/class-mfw-role-based-self-service.php`) — a public form,
  honeypot-protected, that creates a WP user and assigns a level, but **only** for levels
  with `self_signup_enabled` checked. Renders nothing if no level is self-signup-enabled, so
  a page carrying the shortcode degrades gracefully rather than showing a broken empty form.
- **Self-service level switching**: a "Switch Membership Level" section on the My Account
  "Membership" tab (via the `mfw_role_membership_myaccount_after` action) lists other
  self-signup-enabled levels the user doesn't hold; switching revokes only the user's
  *other self-signup* levels (never an admin-assigned one) and assigns the new one.

### Content & access control

- **Content dripping**: a restriction can set `drip_days` (post/page/product restrictions
  only, not term or min-rank) — a matching level only grants access once it's been held that
  long. Checked against the earliest-qualifying matching level's real `assigned_at`.
- **Category-scoped discounts**: a level's `discount_percent` can be limited to specific
  WooCommerce product categories via `discount_category_ids` (empty = every product, the
  original v3.1.3 behavior, unchanged).
- **WPML string registration**: the login heading/notice are registered with WPML's String
  Translation (`wpml_register_single_string`) when saved, if WPML is active — a no-op
  otherwise. This plugin already ships a `wpml-config.xml`, so multilingual sites are a real
  audience for this admin-authored copy.
- **Existing-role capability preview** (this shipped in v3.1.3, listed here for completeness
  since it's easy to miss): selecting an existing role in the level form ticks the matching
  checkboxes live.

### Campaigns & communication

- **Branded HTML email templates**: `includes/role-based/class-mfw-role-based-email-template.php`
  wraps every role-based email (assign/revoke notifications, expiry reminders, campaigns) in
  a shared table-based HTML shell with a configurable header color and footer text.
- **Unsubscribe / preference center**: every campaign email carries a one-click,
  token-signed (not a nonce — a nonce can't survive being opened days later by a logged-out
  recipient) unsubscribe link (`Mfw_Role_Based_Campaigns::maybe_handle_unsubscribe()`,
  hooked on `init`), plus a self-service "do not send me campaign emails" checkbox on the My
  Account tab. Both write the same `mfw_role_membership_campaign_opt_out` user meta, checked
  before every send.
- **Scheduled & drip campaigns**: `Mfw_Role_Based_Campaigns::submit()` supports `now`
  (unchanged v3.1.3 behavior), `scheduled` (books a `wp_schedule_single_event()` for the
  requested time), and `drip` (triggered per-recipient by
  `mfw_role_based_after_assign_level`, with a configurable delay — the campaign's own
  "Send To" audience is ignored for drip, since it always targets whoever gets the trigger
  level). A drip/scheduled campaign is logged with `status = 'scheduled'` until it actually
  sends.
- **Open/click tracking**: `includes/role-based/class-mfw-role-based-campaign-tracking.php`
  appends a 1x1 tracking-pixel image and rewrites every link in the message to go through a
  redirect endpoint that logs the click first — both endpoints are plain `template_redirect`
  query-var checks (no `add_rewrite_rule()`/permalink flush needed). Events land in a new
  `{prefix}mfw_role_membership_campaign_events` table, summarized as opens/clicks in Campaign
  History.

### Admin & reporting

- **Dashboard widget** (`class-mfw-role-based-dashboard-widget.php`): member/non-member/level
  counts, recent logins, recent campaign performance, right on the WP Dashboard.
- **CSV export/import** (`includes/role-based/admin/class-mfw-role-based-csv.php`): members
  (with their levels), and campaign history, as CSV downloads; bulk member↔level assignment
  import from a CSV with `user_email`/`user_id` + `level_name`/`level_id` columns — distinct
  from the existing JSON level-*definition* export/import, which is unchanged.
- **Dependency-free charts**: a "Reports" section renders a members-per-level bar chart and a
  30-day signups sparkline using plain HTML/CSS (no charting JS library — this plugin has no
  existing dependency on one, and pulling one in for two small charts wasn't judged worth
  the added weight). `Mfw_Role_Based_Repository::get_level_distribution()` /
  `get_signups_per_day()` supply the data.

### Integrations & extensibility

- **REST API** (`includes/role-based/class-mfw-role-based-rest-api.php`,
  namespace `mfw-role-based/v1`): list levels, read/assign/revoke a member's levels. Auth is
  entirely core WordPress's own (cookie+nonce or Application Passwords) plus a
  `manage_options` capability check per route — no custom authentication scheme.
- **Outbound webhooks** (`class-mfw-role-based-webhooks.php`): a non-blocking
  `wp_remote_post()` (5s timeout, `blocking => false`, so a slow/dead endpoint never delays
  the triggering request) fires on level-assigned, level-revoked, and campaign-sent, to
  admin-configured URLs. Built entirely on `do_action` hooks the repository/campaigns
  classes already fire (or, for campaign-sent, a direct method call after each send
  completes, since campaigns didn't previously have a dedicated action for it).

### Compliance & security

- **GDPR export/erasure** (`class-mfw-role-based-privacy.php`): registers with WordPress
  core's own Tools → Export/Erase Personal Data flow. Erasure removes a user's level
  assignments (same non-destructive semantics as an admin revoking them — level
  *definitions* are untouched) and their campaign opt-out flag; login/audit history is
  **retained** for security record-keeping, matching common audit-trail practice, and this
  is stated explicitly in the eraser's response message rather than silently kept.
- **Login rate-limiting**: *not implemented* — see "Explicitly deferred" below.

### Explicitly deferred (by design, not oversight)

- **Multisite / network-wide levels** — the data layer's queries assume a single site's
  `$wpdb` prefix throughout; making that network-aware would mean touching nearly every
  query in the repository. The roadmap itself flagged this as "High effort," and it's a
  large enough architectural change to warrant its own dedicated pass rather than being
  bolted on here.
- **A/B subject-line testing** — the roadmap document itself judged this "marginal gain...
  only worth it once campaign volume justifies it"; not built.
- **Login rate-limiting** — genuinely useful, but a minimal hand-rolled attempt-counter is
  the kind of security-adjacent code this session's static-only verification (no live
  environment, no security review) is a poor fit for testing properly; recommend pointing
  admins at an existing, well-audited security/login-protection plugin instead of adding a
  custom one here.
- **Social Login and Two-Factor Authentication** — explicitly excluded per product decision:
  both are real authentication surfaces where a bug is an auth-bypass risk, and this session
  cannot get either a security review or live testing. Use an established plugin (e.g.
  Nextend Social Login, WP 2FA) instead of custom code for either.

## v3.4.0: Roles manager + charging for protected content

Prompted by a live comparison against the reference "Members" plugin's own admin UI
(installed locally and inspected via its actual rendered HTML, not just its source code):
its Roles screen manages raw WordPress roles as a first-class, standalone concept (list,
Add New, Edit, Clone, Delete), with a genuinely nicer tabbed + searchable capability picker
than a flat scrollable list — and its **only** answer to "charge members for access" is a
generic "upgrade to MemberPress" banner; the free plugin has no priced-content flow at all.
Both gaps are closed here.

### Standalone Roles manager (`admin/class-mfw-role-based-roles-manager.php`)

A new **Roles** tab, decoupled from Levels: a role created/edited/cloned here is a plain
WordPress role with no Level-specific concepts (no discount, expiry, price) — it can still
be picked in any Level's "WordPress Role" field afterward, same as any other role.

- **List**: name, slug, current user count (`count_users()['avail_roles']`), with
  Edit/Clone/Delete actions.
- **Delete guard**: a role is only deletable if it's neither one of WordPress's five
  built-in roles nor currently held by at least one user — enforced both by hiding the
  Delete button and by re-checking server-side in `ajax_delete_role()`, so this can't be
  bypassed by a direct AJAX call.
- **Clone**: duplicates a role's capabilities under a new slug (`{slug}_copy`, or
  `_copy_2`, etc. if that's taken) named "{Name} (Copy)".
- **Add/Edit form — tabbed + searchable capability picker**: one tab per group from the
  same `capability_groups()` used by the Level form (so the two pickers stay in sync
  automatically), each with its own "Select All"/"Deselect All", plus a live search box
  that filters items within the active tab and auto-switches to the first tab with a match
  if the active tab has none — a lighter-weight equivalent of the reference plugin's
  "N capability match on other tabs" hint, solved by just jumping there instead of only
  showing a count.
- **Not implemented**: true tri-state grant/deny/unset per capability (the reference
  plugin's picker supports explicitly *denying* a capability, which WordPress's role API
  does support via `add_cap($cap, false)` — distinct from simply not having it). Judged a
  meaningfully bigger UI and semantics change for a modest benefit over the existing
  grant/unset model already used consistently across this module (levels, roles, sync
  logic); noted here as a real, deliberately deferred gap rather than an oversight.
- **Renaming a role**: WordPress's role API has no "update display name" call — this is
  handled by writing directly to the `$wp_roles->roles`/`role_names` arrays and persisting
  via `update_option( $wp_roles->role_key, ... )`, the same mechanism WordPress core itself
  uses internally for role storage.

### Charging for protected content (`class-mfw-role-based-paid-access.php`)

A level can now have a `price` (> 0 = paid). Saving a priced level auto-manages a linked,
hidden WooCommerce product (`Mfw_Role_Based_Repository::sync_level_product()` — virtual,
catalog-hidden, only ever reached via a "Buy Access" link this module generates, never
through the shop): created the first time a level gets a price, kept in sync (name/price)
on every save, and trashed (not deleted — recoverable) if the price is cleared back to 0.
Deleting a level trashes its linked product too.

**End-to-end flow**:
1. A "Buy Access" link/button appears wherever a paid level is relevant: right in the
   restriction message on a paywalled post/page/product (the actual paywall moment — the
   most direct answer to "charge for protected content", rather than only a generic
   upsell elsewhere), in the public registration form (priced levels show their price
   inline, e.g. "Gold — $9.99"), and in the My Account "Switch Membership Level" section
   (a priced level shows a price + "Buy Access" button instead of an instant "Switch").
2. Clicking it (`Mfw_Role_Based_Paid_Access::maybe_handle_buy_click()`, on
   `template_redirect`) sends a logged-out visitor to the membership login page first
   (carrying the buy link itself as `redirect_to`, so they land back on it after
   logging in) — WooCommerce checkout can't assign a level to "nobody". Registering
   through `[wps_role_membership_register]` with a **paid** level selected creates the
   account, then goes straight to checkout with that level's product in the cart instead
   of assigning it — a single unified flow for both free and paid self-signup, rather than
   two disconnected ones.
3. A logged-in visitor's click empties the cart, adds the level's product, and redirects
   to checkout.
4. On `woocommerce_order_status_completed` **or** `_processing` (virtual/digital orders
   commonly skip straight to one or the other depending on store settings — both are
   handled), `grant_access_from_order()` matches the order's line items against every
   level's linked product id and assigns the level(s) to the order's customer. A **guest**
   checkout is resolved to a real user by billing email — an existing account if one
   matches, otherwise a new one via WooCommerce's own `wc_create_new_customer()` — so
   "buy access" works end-to-end without forcing account creation as a separate prior step.
5. **Security**: `handle_switch_level()` re-validates server-side that a level being
   switched-to for free via that endpoint isn't actually a priced one (redirecting to the
   buy flow instead if it is) — the free/instant path is never reachable for a paid level
   merely by hiding the button in the UI; it's rejected server-side too.

**Scope boundary, stated explicitly**: this reuses WooCommerce's own cart/checkout/order
system entirely — no separate payment gateway, subscription/recurring billing, or refund
handling was built. A priced level is a one-time purchase; recurring "membership
subscriptions" would need WooCommerce Subscriptions (or similar) integration, which is a
distinct, larger feature not attempted here.

## Tiered membership: auto-upgrade by lifetime spend

Levels already had `level_rank` (for "minimum rank" restrictions) and `discount_percent`, which
between them are most of what a tier ladder needs — this adds the missing piece: automatic
progression. A new `upgrade_spend_threshold` column (DB_VERSION 1.8.0) on each level; a level
with 0 (the default) opts out of the ladder entirely, so existing admin-only/manually-assigned
levels are completely unaffected by this feature until an admin explicitly opts a level in.

- **Building a ladder**: set `upgrade_spend_threshold` on two or more levels, in ascending order
  of both `level_rank` and the threshold amount (e.g. Silver at rank 1/₹0, Gold at rank 2/₹5,000,
  Platinum at rank 3/₹20,000). `Mfw_Role_Based_Repository::get_tier_ladder_levels()` returns
  exactly the levels with a threshold set, ordered by rank.
- **Auto-upgrade** (`includes/role-based/class-mfw-role-based-tiers.php`, new): on
  `woocommerce_order_status_processing`/`completed` (same dual-hook pattern as
  `Mfw_Role_Based_Paid_Access`, since either can be the "paid" state depending on gateway), reads
  the order's customer, and — **only if they already hold at least one role-based level** (this
  is tier progression for existing members, not a mechanism for enrolling brand-new members from
  spend alone) — computes their lifetime spend via WooCommerce's own
  `wc_get_customer_total_spent()` and finds the highest-rank ladder level whose threshold that
  spend now clears. If it outranks their current max rank, their other ladder-tier level(s) are
  revoked and the new one assigned via the existing `assign_level_to_user()` /
  `revoke_level_from_user()` primitives — which means the existing "level assigned" email
  notification, user-log entry, and capability sync all fire automatically, with zero new
  plumbing. A non-ladder level held alongside (an admin-assigned perk, say) is never touched,
  same principle as self-signup switching only ever touching self-signup levels. Upgrades are
  one-directional: a member is never auto-downgraded for falling short later — this is a reward
  mechanism, not a spend-tracking enforcement one.
- **"Show user to upgrade by earning discounts"**: the My Account "Membership" tab now shows an
  "Upgrade to `<next tier>`" panel (only when a next tier exists on the ladder) with a progress
  bar — spend so far, amount still needed, and, since the whole point is the incentive, **what
  discount rate the next tier grants** pulled straight from that level's `discount_percent`, so
  the message is concretely "spend ₹X more to unlock Y% off," not just "upgrade available."
  `Mfw_Role_Based_Repository::get_next_tier( $user_id )` does this computation once and is reused
  as-is by the display.

### Fixes (DB_VERSION 1.9.0)

- **Explicit ladder flag.** Ladder membership used to be "threshold > 0", so a ₹0 base tier
  (e.g. Silver) could never be on the ladder. On upgrade, Silver wasn't treated as a ladder
  tier and wasn't revoked, leaving the member holding both Silver and Gold. There is now a
  `tier_ladder` column and a "Part of the auto-upgrade tier ladder" checkbox on the level
  form; the threshold field only shows when it's ticked. The 1.9.0 migration sets
  `tier_ladder = 1` on every level that had a threshold, so existing ladders keep working.
  Level export/import carries the flag; older export files fall back to the old rule.
- **Fatal error fixed.** `get_tier_ladder_levels()` called `self::levels_table()`, which
  doesn't exist on the repository, so every paid order for a member and every My Account
  "Membership" tab view for a member hit a fatal error. It now uses `Mfw_Role_Based_Db::levels_table()`.
- **Ladder rank only.** Upgrades and the progress panel compare against the member's highest
  *ladder* rank (`get_user_ladder_rank()`, -1 if they hold no ladder tier), not all levels, so
  a high-rank admin perk level no longer blocks tier progression. A member who holds only
  non-ladder levels is placed on the ladder at the highest tier their spend qualifies for.
- **Spend cache.** `maybe_auto_upgrade()` calls `wc_delete_shop_order_transients( $order )`
  before reading `wc_get_customer_total_spent()`, so the order that was just paid is always counted.
- **Assign before revoke.** The new tier is assigned before the old one is revoked, so a role
  or capability shared by both tiers is never briefly stripped.
- **One upgrade email.** While `Mfw_Role_Based_Tiers::is_upgrading()` is true, the "access
  ended" email for the old tier is skipped and the assigned email says "You have been upgraded
  to X" (with the new discount, if any). Assign/revoke webhooks and log entries still fire as normal.
- **Recalculate Tiers Now.** Order hooks only fire on new orders, so members who already spent
  enough before the ladder was set up were never upgraded. A button on the Levels tab
  (`mfw_role_based_recalculate_tiers` AJAX, `manage_options` + nonce) runs
  `Mfw_Role_Based_Tiers::recalculate_all()` over every member. This is a synchronous loop,
  with the same scale caveat as campaign sending.
- **Ladder warnings.** `get_tier_ladder_problems()` shows a warning on the Levels tab when the
  ladder has only one level, two tiers share a rank, or a higher-rank tier doesn't need a
  higher spend.
- **Progress panel.** It says "your upgrade will be applied with your next order" when the
  member already qualifies, and leaves out the "unlock X% off" wording when the next tier has no discount.
- **New action.** `mfw_role_based_tier_upgraded( $user_id, $new_level, $replaced_level_ids, $spent )`.

Known limitation: like every other role-based table change, the 1.9.0 migration runs from
`admin_init` (`Mfw_Role_Based_Db::maybe_install()`). After updating, an admin page must be
loaded once before the new column exists.

**Deferred, not built** (scope explicitly left open, per the "add more" invitation this shipped
under): order-count-based tiers (spend is the only metric v1 supports), a points/rewards system
independent of raw spend, tier badges/icons, an admin dashboard widget summarizing tier
distribution (the existing Reports tab's level-distribution chart already covers this at a
glance since a ladder level is still just a level), and multi-currency threshold conversion (a
threshold is a flat number compared directly against `wc_get_customer_total_spent()`'s return
value, which is already in the store's base currency).

## Login page had no path to registration

Private Site is already the admin's "force login vs. guest mode" choice (off = normal browsing,
only explicitly restricted content is gated; on = every front-end page requires login) — but a
real gap surfaced using it end-to-end: the login page (`[wps_role_membership_login]`) rendered
only a username/password form, with no link anywhere to the self-signup page. Under Private
Site, that page is the *only* one a logged-out visitor can reach at all, so a brand-new visitor
with no account had no way forward.

Fixed with `Mfw_Role_Based_Self_Service::get_register_page_url()` — finds the first published
page whose content contains `[wps_role_membership_register]` (a direct `post_content LIKE`
lookup; the same signal `enforce_private_site()` already uses to exempt that page from the
redirect, so no separate "which page is registration" admin setting was needed) and returns
empty if none exists or no level currently allows self-signup. The login page now shows a
"New here? Join Membership" link whenever that returns a URL. The Private Site setting's
description was also expanded to say outright that turning it on requires a self-signup page to
exist, so this dependency isn't discovered the hard way again.

## Front-end visual design

The role-based module's customer-facing surfaces (login page, self-signup form, My Account
"Membership" tab, restricted-content notices, "Buy access" links) originally relied on bare
theme defaults plus a couple of small per-shortcode inline `<style>` blocks — functional but
plain (see the "Admin styling" note above, which explicitly deferred this). Replaced with a
single shared stylesheet, `includes/role-based/assets/css/mfw-role-based-public.css`: white
card containers with soft shadows and rounded corners, a blue-to-violet gradient for primary
buttons and price badges (matching the gradient already used on the purchase-flow's own
`[wps_membership_default_plans_page]` template, so the two flows don't visually clash if both
are ever seen by the same site owner), consistent field/label spacing, and dedicated notice/
error styles. Every rule is scoped to `.mfw-role-*` classes, so it can never bleed into theme or
WooCommerce markup elsewhere.

`Mfw_Admin_Assets::maybe_enqueue_public()` (new) loads it on every front-end request while role
mode is active — unlike the admin stylesheet, which only loads on the plugin's own two settings
pages, this one can't be limited to fixed screen ids, because an admin can place the module's
shortcodes on any page they choose. This required moving `Mfw_Admin_Assets::init()` out of the
`is_admin()` guard in `Mfw_Mode_Controller::init()` so its `wp_enqueue_scripts` callback actually
registers on front-end requests; each of the class's two callbacks still individually gates
itself on admin vs. front-end context, so behavior on the admin side is unchanged.

The My Account "Membership" tab reuses the same `.mfw-role-card` look but with an
`.mfw-role-card--account` modifier (`max-width: 100%`, no auto-margin) since it already renders
inside WooCommerce's own constrained My Account content column — the standalone login/register
pages keep the narrower centered-card treatment since they render directly in the page content
with nothing else constraining their width.

## Level descriptions (what a member actually gets)

Gap found by direct use of the self-signup flow: the registration form, the My Account
"Switch Membership Level" list, and the "Buy access to X — price" links on restricted content
all showed a bare level **name** plus its price, with nothing telling a prospective member what
that level actually unlocks — a customer had no way to judge whether a paid level was worth its
price before handing over payment details.

Added a `description` column to `mfw_role_membership_levels` (DB_VERSION 1.7.0, via dbDelta —
existing rows just get an empty description, no backfill needed) and threaded it through:

- **Admin**: a new "Description" textarea on the level form (Levels & Capabilities tab), saved/
  read by the same `mfw_role_based_save_level` AJAX action and included in level export/import.
- **Self-signup form** (`[wps_role_membership_register]`): each `<option>` carries the
  description as a `data-description` attribute; a small inline script swaps a description line
  below the dropdown whenever the selection changes, so the customer sees what they'd get before
  submitting.
- **My Account → Switch Membership Level**: the description is printed under each level's
  row/button — always visible, no interaction needed, since it's a short static list rather than
  a dropdown.
- **"Buy access to X — price" links** on restricted content: the description is appended inline
  next to the link, so a paywall doesn't just say "pay to unlock" without saying what "unlock"
  means.

All four surfaces render nothing extra when a level's description is empty, so this is fully
backward compatible with levels created before this field existed.

## Per-content custom restricted-message (restriction metabox tabs)

Cross-checked against the reference "Members" plugin's product-edit metabox, which splits its
"Content Permissions" box into three tabs: Roles, Paid Memberships, Error Message. Our own
`Role-Based Membership Restriction` metabox already unifies "Roles" and "Paid Memberships" into
one list (a level is just a level, whether or not it has a price — see "Charging for protected
content" above), so a separate "Paid Memberships" tab would only duplicate that list; instead,
paid levels are now labelled with their price inline in the existing checklist so an admin can
see at a glance which levels imply a paywall.

The one genuine gap found during that comparison: `Mfw_Role_Based_Public::filter_restricted_content()`
already read a per-object override from post meta `mfw_role_membership_restricted_message`
(falling back to the site-wide default), but nothing in the admin UI ever let anyone set it —
the field existed in the data model with no way to reach it. The restriction metabox
(`admin/partials/role-based-restriction-metabox.php`) is now split into two tabs, mirroring the
reference plugin's pattern:

- **Access** — the existing specific-levels / minimum-rank / content-drip controls.
- **Error Message** — a textarea for the per-object override; blank means "use the site-wide
  default from Settings."

Both tabs are saved together by the same "Save Restriction" button and the same
`mfw_role_based_save_restriction` AJAX action (`Mfw_Role_Based_Admin::ajax_save_restriction()`),
which now also reads `restricted_message` from the request and writes/deletes the post meta
accordingly. The permission check there was tightened at the same time from a blanket
`current_user_can( 'edit_posts' )` to `current_user_can( 'edit_post', $object_id )`, since a
user with generic edit-posts capability but no edit rights on this specific object should not be
able to change its restriction rule.

The tab UI is deliberately self-contained (inline `<style>`/`<script>` in the partial, no new
enqueue) rather than pulled from `mfw-role-based-admin.css`, because that stylesheet is only
enqueued on the plugin's own two settings pages (`Mfw_Admin_Assets::maybe_enqueue()`), not on
`post.php`/`post-new.php` — matching how the metabox already rendered in native WordPress admin
styling before this change (see "Admin styling" below).

## Admin styling

The "Membership Mode" and "Role-Based Membership" pages use the plugin's own existing WP
Swings brand (light-blue `#e5f4fe` body, `#2196f3` accent, NunitoSans typography, pill
buttons) instead of default WordPress grey — matching the rest of the WP Swings plugin family.
`includes/mode/class-mfw-admin-assets.php` enqueues the plugin's existing
`admin/css/wps-admin.css` (loaded as-is, never edited) plus one small new stylesheet,
`includes/role-based/assets/css/mfw-role-based-admin.css`, which only adds the pieces that
file doesn't already provide (a pure-CSS toggle switch, card wrappers, the mode-picker cards)
— it does not redefine any of the shared design tokens. Both are scoped to only load on our
own two page slugs, so they never affect any other admin screen. The restriction metabox and
user-profile fields are left in native WordPress admin styling, since they're embedded inside
WordPress's own edit screens and should look consistent with the rest of that screen, not with
our standalone pages.

## Gap-analysis items intentionally NOT built (out of scope)

Cross-checked against the reference "Members" plugin (v3.2.26) after the initial delivery.
Kept out deliberately, not by oversight:

- **Generic role hierarchy / role "positions"** beyond the simple numeric `rank` + minimum-rank
  restriction implemented above.
- **A full raw WP role editor** (rename/reorder/clone arbitrary roles independent of a level) —
  our "level" abstraction already covers the equivalent need, with on-the-fly role creation.
- **A third-party-extensible capability-group *registration API*** (`members_register_cap_groups`
  equivalent, where other plugins register their own named groups) — replaced with a full
  curated list (WordPress core + WooCommerce) plus automatic discovery of anything else already
  on a role, which covers the same practical need without an open addon/extension architecture.
- **REST/Gutenberg collection-level filtering** (only single-item GET requests are hidden).
- **Private feed restriction** (only the site-wide and REST private-mode equivalents were built).

## Deferred follow-ups (explicitly out of scope for this delivery)

1. Physical relocation of the purchase-flow files into `includes/legacy/` (see that folder's
   README for the recommended path-constant-indirection prep step).
2. SMS/WhatsApp notification channel (scope decision needed).
3. Single source of truth for the plugin version number (currently three hardcoded literals
   kept in sync manually — pre-existing issue, not introduced by this epic).
4. Automated tests — this plugin has none; verification for this delivery was `php -l` on
   every changed file plus manual review (see `QA-CHECKLIST-role-based-mode.md`).
