# Implemented Features — Role-Based Membership Mode (WPS-7875)

Every feature built on the `feature/WPS-7875-role-based-membership-mode` branch, with a short
description of what each one does. For implementation details see
`ARCHITECTURE-role-based-mode.md`, and for test steps see `QA-CHECKLIST-role-based-mode.md`.

All new code lives under `includes/mode/` and `includes/role-based/`. The existing
purchase-based flow is unchanged: the only edit to existing code is a two-line bootstrap in
`includes/class-membership-for-woocommerce.php`, plus the version bump to **3.1.3**.

---

## 1. Mode Architecture

### 1.1 Dual membership modes
The plugin now runs in one of two modes, never both at once:
- **Purchase-based** (the default, and the existing behaviour): customers buy a membership plan.
- **Role-based** (new): access comes from WordPress roles and capabilities, so no checkout is needed.

The active mode is stored in the `wps_membership_mode` option. Sites upgrading from an older
version have no value for this option, so they fall back to purchase mode and see no change.

### 1.2 Onboarding / mode selection screen (WPS-7878)
A standalone **Membership Mode** admin page, plus a first-run admin notice that links to it,
asks the admin to choose a mode. If they pick purchase mode, the existing React setup wizard
runs as before. If they pick role mode, they go straight to the new Role-Based Membership page.

### 1.3 Non-destructive mode switching (WPS-7883)
Once a mode is chosen, the same page shows a toggle with a confirmation modal. Switching
changes only the mode option and which menus are visible. It never deletes or migrates any
user meta, post meta or database rows, so existing purchase-mode members keep their access.

### 1.4 Admin menu visibility ("no hybrid mode")
Only the active mode's admin screens appear. In role mode, the purchase flow's settings,
Membership Plans and Members menus are hidden. This only changes navigation: the hidden
flow's hooks and customer access keep working.

### 1.5 Legacy boundary documentation
`includes/legacy/README.md` marks where the original purchase-based code ends. It also
records why that code was not physically moved, and how to do that move safely later.

---

## 2. Membership Levels & Roles

### 2.1 Membership levels manager (WPS-7879 / WPS-7880)
Admins can add, edit and delete levels. Each level has:
- a name and a **description** (what the member gets)
- a mapped **WordPress role**. If the role doesn't exist yet, it is created on save.
- a numeric **rank** and an **active/inactive** status
- extra **capabilities**
- a **discount %** and optional **discount categories**
- **expiry days**, a **self-signup** flag, a **price**, and **tier ladder** settings (a ladder checkbox and an upgrade spend threshold)

### 2.2 Grouped capability picker
The checklist covers every WordPress core and WooCommerce capability, grouped for easy
reading. An auto-discovered **"Other"** group shows any other capability already present on a
role, such as ones from custom post types or other plugins.

### 2.3 Real role capability sync
When a level is saved, the mapped role's capabilities are changed to match the checked boxes.
Unchecking a box really removes that capability. Capabilities outside the picker are never
touched, so mapping a level onto an existing role like `editor` won't break it.

### 2.4 Existing-role capability preview
Picking an existing role in the level form ticks that role's current capabilities right away,
so the admin can see what the role already grants.

### 2.5 Multiple levels per user
One user can hold several levels. When a level is revoked, its role and capabilities are
removed only if none of the user's other levels also grant them.

### 2.6 Level export / import (JSON)
Level definitions can be exported and imported as a simple JSON file, so a setup can be moved
between sites.

### 2.7 Standalone Roles manager (v3.4.0)
A **Roles** tab manages plain WordPress roles separately from levels:
- **List**: name, slug and user count, with Edit, Clone and Delete actions.
- **Add/Edit**: a tabbed, searchable capability picker with Select All / Deselect All on each
  tab. If the current tab has no match for a search, it jumps to the first tab that does.
- **Clone**: copies a role under a new slug, e.g. `{slug}_copy`, named "{Name} (Copy)".
- **Rename**: changes a role's display name.
- **Delete guard**: the 5 default WordPress roles and any role that still has users can't be
  deleted. This is checked on the server too, not just by hiding the button.

---

## 3. Assigning Levels to Users

### 3.1 User profile panel
Every user's profile edit screen has a **Role-Based Membership** checkbox panel for assigning
and revoking levels.

### 3.2 Bulk actions on the Users list
The Users screen has an "Assign: \<level\>" and a "Revoke: \<level\>" bulk action for each level.

### 3.3 CSV bulk assignment import
A CSV file can assign levels to members in bulk. It accepts `user_email` or `user_id` columns
together with `level_name` or `level_id` columns. Bad rows are skipped instead of stopping the
import.

### 3.4 Audit log
Every assign, revoke and member login is written to the `mfw_role_membership_user_log` table.

---

## 4. Content Restriction & Access Control

### 4.1 Post / page / product restriction metabox
A restriction metabox with two tabs on post, page and product edit screens:
- **Access**: either specific levels (paid levels show their price) or a **minimum rank**
  (you use one or the other), plus optional **content drip days**.
- **Error Message**: a custom restricted message for this item. If it's blank, the site-wide
  default is used.

Saving checks `edit_post` permission for that specific item.

### 4.2 Taxonomy term restriction
Every public taxonomy's term-edit screen has the same restriction field. A restricted term
archive returns a 403 to anyone without access.

### 4.3 Friendly content restriction
Restricted posts and pages still load, but their content and excerpt are replaced with a
message. The message is the item's own custom text or the site-wide default.

### 4.4 Restriction bypass rules
These users always see restricted content: site admins, the item's author, anyone who can
edit that item, anyone who can `manage_categories` (for terms), and anyone with the
`mfw_restrict_content` capability.

### 4.5 Product purchasability restriction
A restricted product can't be bought by users who don't have access. This check is separate
from the purchase flow's own filters.

### 4.6 REST API hiding
A single-item REST request (`/wp/v2/posts/{id}`, `/wp/v2/pages/{id}`) for restricted content
returns a 403 to users without access.

### 4.7 Content dripping
Content can unlock only after a member has held a matching level for N days.

### 4.8 Private Site mode
When this is on, logged-out visitors are sent to the Membership Login page from every
front-end page except the login and registration pages. The page they wanted is kept in
`redirect_to`, so they land there after logging in.

### 4.9 Shortcodes
- `[wps_role_membership_restricted level_id="X"]`: shows the wrapped content only to members
  (of level X, or of any level if `level_id` is left out).
- `[wps_role_membership_level_name]`: shows the name of the user's highest-rank level.

---

## 5. Frontend / Member Experience (WPS-7881)

### 5.1 My Account "Membership" tab
A new `role-membership` endpoint in My Account. It shows all of the user's levels, the
self-service level switcher, email preferences and the tier-upgrade progress bar.

### 5.2 Membership Login page
A **Membership Login** WordPress page is created automatically when the admin switches to role
mode. It holds the `[wps_role_membership_login]` shortcode, which provides:
- the login form, using core `wp_login_form()`, so it is as secure as the normal login
- an error notice after a failed login, shown on the same page
- a "lost your password" link
- a logged-in view with links to the Membership tab and to log out
- a "New here? Join Membership" link to the self-signup page, when one exists

After login, the member goes to `redirect_to` if it is set, otherwise to My Account → Membership.
Logging in through the normal wp-login.php / wp-admin is not affected.

### 5.3 Login page design settings
Admins can set a heading and a rich-text notice (TinyMCE) for the login page, and turn the
notice on or off.

### 5.4 Self-service signup
The `[wps_role_membership_register]` shortcode shows a public registration form with a honeypot
to block bots. Only levels with self-signup turned on are listed, and each level's description
updates live as the visitor changes their choice. If no level allows self-signup, the shortcode
shows nothing.

### 5.5 Self-service level switching
On the My Account tab, members can switch to another self-signup level. Switching removes only
their other self-signup levels; levels assigned by an admin are never removed.

### 5.6 Level descriptions everywhere
A level's description appears on the signup form, in the level switcher and next to "Buy
access" links, so customers know what they get before paying.

### 5.7 Front-end styling
One shared stylesheet (`mfw-role-based-public.css`) gives the login page, signup form, account
tab, notices and buy links a card design with gradient buttons that matches the purchase flow.
All rules are scoped to `.mfw-role-*` classes, so they don't affect the theme.

---

## 6. Pricing, Discounts & Paid Access

### 6.1 Member discounts
Each level can have a discount percentage. A member gets their **highest** level discount;
discounts from several levels don't add up. Shop and product pages show the original price
struck through, the member price and a badge. Cart totals are recalculated from the product's
real price each time, so the discount is never applied twice.

### 6.2 Category-scoped discounts
A level's discount can be limited to certain product categories. If no category is chosen,
it applies to the whole store.

### 6.3 Charging for protected content (v3.4.0)
Setting a **price** on a level creates a hidden, virtual WooCommerce product linked to it. The
product's name and price stay in sync with the level. The product is moved to trash if the
price is set back to 0 or the level is deleted. How buying works:
1. A **"Buy access to X — price"** link appears in the restricted message, on the signup form
   and in the level switcher.
2. Logged-out visitors are sent to log in first, then return to the buy link.
3. Clicking the link empties the cart, adds the level's product and goes to checkout.
4. When the order reaches Processing or Completed, the level is assigned. For guest orders,
   the plugin finds the account by billing email or creates a new one.
5. The server rejects any attempt to switch to a paid level for free.

Paid levels are one-time purchases; recurring subscriptions are not included.

### 6.4 Tiered membership auto-upgrade
Levels with the **"Part of the auto-upgrade tier ladder"** checkbox ticked form a tier ladder.
Each has a Rank and a lifetime-spend threshold; the base tier can use a threshold of 0.
- When an existing member's order is paid, their lifetime spend is checked. If it qualifies
  them for a higher tier, they are moved up automatically. Their old tier is removed, they get
  one "You have been upgraded" email, the audit log is updated and capabilities are synced.
- Members are never moved down. Levels outside the ladder, such as admin-assigned perks, are
  never changed and never block an upgrade.
- **Recalculate Tiers Now** on the Levels tab upgrades members who already spent enough before
  the ladder was set up.
- The Levels tab warns about ladder setup mistakes: only one tier, two tiers with the same
  rank, or a higher tier that doesn't need a higher spend.
- Developers can use the `mfw_role_based_tier_upgraded` action.

### 6.5 Upgrade progress bar
My Account shows an "Upgrade to \<next tier\>" panel with a progress bar: amount spent, amount
still needed, and the discount the next tier unlocks (if it has one). If the member already
qualifies, it says the upgrade will be applied with their next order.

### 6.6 Bug fix: AJAX cart interactions
Frontend hooks (restriction, discounts, shortcodes) now also load on `admin-ajax.php`
requests. Before this fix, a restricted product could be added to the cart through AJAX and
discounts didn't apply there.

---

## 7. Notifications & Campaigns

### 7.1 Level notifications (WPS-7882)
Members get an email when a level is assigned or revoked. There is an extension point,
`mfw_role_based_notification_channels`, for adding SMS or WhatsApp later.

### 7.2 Branded HTML email template
Every role-based email uses one shared HTML layout with a header colour and footer text the
admin can change.

### 7.3 Broadcast campaigns
Admins write a subject and a rich-text message and send it to **Members**, **Non-Members**,
**specific level(s)** or **Everyone**. The placeholders `{display_name}`, `{user_email}` and
`{site_name}` are filled in for each recipient. Every campaign is saved in **Campaign History**.

### 7.4 Scheduled & drip campaigns
- **Now**: sends straight away.
- **Scheduled**: sends at a date and time you pick.
- **Drip**: sends to each user a set delay after they get a trigger level.

### 7.5 Open / click tracking
A tracking pixel records opens, and links are routed through a redirect that records clicks.
Opens and clicks appear in Campaign History.

### 7.6 Unsubscribe / preference center
Every campaign email has a signed one-click unsubscribe link. Members also have a "don't send
me campaign emails" checkbox in My Account. Both control the same opt-out, which is checked
before every send.

---

## 8. Admin, Reporting & Settings

### 8.1 Role-Based Membership admin page
One page with these tabs:
- **General Settings**: Private Site and the default restricted message
- **Login Page**: the login page heading and notice
- **Roles**
- **Levels & Capabilities**
- **Campaigns**
- **Login Activity**
- **Advanced**: reports, webhooks and email template
- **Import / Export**: JSON and CSV

### 8.2 Recent Member Logins
A table of the last 50 logins by members, with member, level and time.

### 8.3 Dashboard widget
A widget on the WordPress Dashboard shows member, non-member and level counts, recent logins
and recent campaign results.

### 8.4 Reports & charts
A **Reports** section shows a bar chart of members per level and a 30-day signups sparkline,
built with plain HTML/CSS (no charting library).

### 8.5 CSV exports
Members (with their levels and assigned/expiry dates) and campaign history can be downloaded
as CSV.

### 8.6 Admin styling
The admin pages use the WP Swings brand design (light-blue background, `#2196f3` accent,
NunitoSans font, pill buttons), and the styles load only on the plugin's own pages.

---

## 9. Membership Lifecycle

### 9.1 Auto-expiry
If a level has expiry days set, a daily WP-Cron job removes it from members once it expires.

### 9.2 Expiry reminders
Members get one reminder email 3 days before a level expires. It is sent only once.

---

## 10. Integrations & Extensibility

### 10.1 REST API
The `mfw-role-based/v1` namespace lets you list levels and read, assign or revoke a member's
levels. It uses core WordPress login (cookie + nonce or Application Passwords) and requires
`manage_options`.

### 10.2 Outbound webhooks
When a level is assigned or revoked, or a campaign is sent, a POST request goes to the URLs the
admin sets. The requests don't wait for a reply (5 s timeout), so a slow endpoint never holds
up the site.

### 10.3 WPML support
The login heading and notice are registered with WPML String Translation when WPML is active.

### 10.4 Developer hooks
- actions `mfw_membership_mode_switched`, `mfw_role_based_after_assign_level`,
  `mfw_role_based_after_revoke_level` and `mfw_role_based_tier_upgraded`
- action `mfw_role_membership_myaccount_after`
- filter `mfw_role_based_notification_channels`

---

## 11. Compliance & Security

### 11.1 GDPR export & erasure
The plugin's data is included in WordPress's Tools → Export/Erase Personal Data. Erasing a user
removes their level assignments and campaign opt-out. Login and audit history is kept for
security records, and the erasure message says so.

### 11.2 Hardening
- Every admin action checks a nonce and the user's capability.
- Restriction saves check `edit_post` for that specific item.
- The delete-role guard and the paid-level check run on the server.
- Unsubscribe links use a signed token.
- The signup form has a honeypot.
- All input is sanitized and output is escaped (`wp_kses_post`, `sanitize_text_field`).

---

## 12. Database

New tables, created with `dbDelta` and versioned by the `mfw_role_based_db_version` option:

| Table | Purpose |
|---|---|
| `{prefix}mfw_role_membership_levels` | Level definitions |
| `{prefix}mfw_role_membership_restrictions` | Restriction rules for items and terms (levels / minimum rank / drip) |
| `{prefix}mfw_role_membership_user_log` | Log of assigns, revokes and logins |
| `{prefix}mfw_role_membership_campaigns` | Campaign history and scheduled campaigns |
| `{prefix}mfw_role_membership_campaign_events` | Campaign opens and clicks |

---

## 13. Not built on purpose

- Multisite / network-wide levels
- A/B testing of email subject lines
- Login rate-limiting, social login and 2FA (use an established security plugin)
- Recurring subscription billing for paid levels
- Allow/deny/unset (three-state) capabilities in the picker
- Filtering of REST collection endpoints and private feeds
- SMS/WhatsApp notifications (waiting on a provider decision)
- Physically moving the legacy purchase-flow files
- Automated tests (verification so far is `php -l` plus the manual QA checklist)
