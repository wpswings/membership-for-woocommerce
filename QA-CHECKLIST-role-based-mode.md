# QA Checklist — Role-Based Membership Mode (WPS-7884)

Automated checks run so far: `php -l` on every new/changed file (pass). No automated test
suite exists in this repository, and phpcs could not be run locally (WPCS ruleset not
installed — `vendor/` not present). Everything below is a **manual** pass required before
this branch is merged.

## A. Regression — purchase-based flow must be 100% unaffected

Run these on a fresh install/activation with no `wps_membership_mode` option set yet
(simulating an existing site upgrading to this version):

- [ ] Plugin activates without PHP notices/warnings/fatals (`WP_DEBUG` on).
- [ ] Existing React setup wizard (`/src`) still renders and completes exactly as before.
- [ ] A product/category assigned to a membership plan is still correctly gated by
      `woocommerce_is_purchasable` (purchase-flow filters).
- [ ] Membership CPTs (`wps_cpt_membership`, `wps_cpt_members`) and their admin screens are
      visible and functional (default mode = purchase, so nothing should be hidden).
- [ ] Existing emails (welcome, invoice, coupon assignment) still fire on plan purchase.
- [ ] First-run admin notice ("choose membership mode") appears and links to the new
      "Membership Mode" page without blocking or redirecting any existing admin screen.
- [ ] `wp_die()` from the new content-restriction hook does NOT fire for any page when in
      purchase mode with no role-based restrictions configured (loader is self-gated).

## B. Full pass — role-based mode

Switch to role mode via the "Membership Mode" page (initial chooser or later switch) and verify:

- [ ] Purchase flow's own settings/dashboard submenu and "Membership Plans" CPT menu are
      hidden from wp-admin nav (still reachable by direct URL — this is a nav-visibility
      change, not an access restriction).
- [ ] "Role-Based Membership" admin page appears; can create a level (name, WP role or a new
      role slug to auto-create, rank, status, capabilities), list it, edit it, and delete it.
- [ ] Clicking "Edit" on a level repopulates the form with its current name, role, rank,
      status, and checked capabilities; saving updates that same level (no duplicate row created).
- [ ] Entering a new (non-existent) role slug when adding a level actually creates that
      WordPress role with the chosen capabilities (`get_role()` returns it afterward).
- [ ] **Capability sync is real, not cosmetic**: unchecking a previously-checked capability on
      Edit and saving actually removes that capability from the mapped WP role
      (`get_role($slug)->has_cap($cap)` becomes false); checking a new one adds it
      (`add_cap`/`remove_cap` observed on `$role->capabilities`).
- [ ] Mapping a level onto an existing built-in role (e.g. `editor`) and saving does NOT strip
      that role's normal capabilities that aren't in our capability picker's list.
- [ ] The "Other (found on existing roles)" capability group appears and lists any site-specific
      capability (e.g. from a custom post type or another plugin) not already in the curated list.
- [ ] Restriction metabox on Post/Page/Product edit screens: both "specific levels" and
      "minimum rank" modes save correctly and are mutually exclusive (switching modes and
      saving clears the other).
- [ ] Every public taxonomy's term-edit screen shows the restriction checklist and persists it.
- [ ] A restricted post/page shows the replacement message (not a hard 403 page) to a
      non-matching visitor, and the real content to a matching one; a restricted taxonomy
      term archive 403s for a non-matching visitor.
- [ ] Escape hatches work: the post's author, a user who can `edit_post` it, and a user with
      the `mfw_restrict_content` capability all see restricted content regardless of level.
- [ ] A restricted product is not purchasable for a non-matching user, purchasable for a
      matching one.
- [ ] **User assignment**: the "Role-Based Membership" panel on a user's profile screen
      correctly assigns/revokes levels on save; the per-level bulk actions on the Users list
      screen ("Assign membership level: X" / "Revoke membership level: X") work on multiple
      selected users at once.
- [ ] A user holding two levels keeps the shared WP role/capabilities after only one of the
      two levels is revoked (only lost when the last level granting that role/cap is revoked).
- [ ] My Account → "Membership" tab lists all of the user's current levels (or the empty state).
- [ ] `[wps_role_membership_restricted]` (with and without `level_id`) and
      `[wps_role_membership_level_name]` shortcodes render correctly for members and non-members.
- [ ] Assigning/revoking a level sends the expected email and writes a row to
      `{prefix}mfw_role_membership_user_log`.
- [ ] **Private Site**: with the toggle on, a logged-out visitor hitting any front-end URL is
      redirected to the login screen; the login/register screens themselves remain reachable;
      turning it off restores normal access immediately.
- [ ] **REST hiding**: `GET /wp-json/wp/v2/posts/{id}` for a restricted, inaccessible post
      returns 403; an accessible one returns normally. Collection endpoints are NOT expected
      to be filtered (documented limitation).
- [ ] **Export/Import**: exporting downloads a JSON file of all levels; importing that same
      file (or a hand-written one in the same shape) creates the expected levels without
      touching existing ones.
- [ ] **Member discount — display**: a product's shop/single price shows struck-through
      original + discounted price + badge for a user holding a level with a discount; no
      change for a user with no discount level.
- [ ] **Member discount — cart, classic**: adding a discounted product to cart via a normal
      (non-AJAX/JS-disabled) add-to-cart shows the discounted line price and correct total.
- [ ] **Member discount — cart, AJAX**: adding/updating the SAME product via the theme's
      default AJAX add-to-cart / update-cart also reflects the discount (this specifically
      regression-tests the admin-ajax.php loader fix below).
- [ ] **Member discount — no stacking**: a user holding two levels with different discount
      percentages gets only the higher one, not the sum.
- [ ] **Membership Login page**: switching to role mode creates a published "Membership
      Login" page automatically; visiting it while logged out shows the login form; while
      logged in shows the "already logged in" state with a link to My Account and a logout link.
- [ ] **Login flow — success**: logging in via the Membership Login page redirects to the
      My Account "Membership" tab by default.
- [ ] **Login flow — failure**: an incorrect password on the Membership Login page redirects
      back to that same page with a visible "Incorrect username or password" notice, NOT to
      wp-login.php's own error screen.
- [ ] **Login flow — default admin login unaffected**: an incorrect password on the normal
      wp-login.php (or wp-admin) login still shows WordPress's own default error, confirming
      the failed-login redirect never engages there.
- [ ] **Private Site → login page integration**: with Private Site on, visiting any restricted
      URL while logged out redirects to the Membership Login page (not wp-login.php) with the
      original URL preserved; logging in successfully lands back on that original URL; the
      login page itself is never redirected (no loop).
- [ ] **Login page design**: setting a heading and a notice (with some bold/link formatting,
      via the Visual editor) on the admin page and saving shows both on the actual login page;
      disabling the notice toggle hides it without losing the saved content; switching the
      editor to Text mode and saving still saves correctly (tests the JS TinyMCE/Text fallback).
- [ ] **Login activity log**: logging in as a user holding a level adds a row to "Recent
      Member Logins" (member, level, correct local timestamp); logging in as a user/admin with
      no level does NOT add a row.
- [ ] **Campaigns — Members audience**: sending a campaign to "Members" only reaches users
      holding at least one level; the member/non-member counts shown on the tab match reality.
- [ ] **Campaigns — Non-Members audience**: sending to "Non-Members" reaches every other
      registered user and none of the actual members.
- [ ] **Campaigns — Specific level(s)**: the level checkbox picker appears only when this
      audience is selected; sending reaches only users holding one of the checked levels.
- [ ] **Campaigns — Everyone**: reaches every registered user regardless of membership status.
- [ ] **Campaigns — placeholders**: `{display_name}`, `{user_email}`, and `{site_name}` in the
      message are replaced correctly per-recipient in the actual sent email.
- [ ] **Campaigns — rich text**: bold/link formatting applied in the Visual editor renders
      correctly in the received HTML email (confirms the plain-form-submit approach correctly
      syncs TinyMCE content, unlike the AJAX flows elsewhere which need to read it manually).
- [ ] **Campaign History**: every send appears with correct name, subject, audience label,
      recipient count, and timestamp; a failed/invalid submission (no subject, or bad audience)
      shows an error notice and is NOT logged to history.

## C. Mode switching — non-destructiveness

- [ ] Create data in both modes (a purchase-mode plan/member, and a role-mode level/assignment).
- [ ] Switch mode back and forth several times via the confirmation-modal flow.
- [ ] Confirm no data is lost or altered in either flow at any point — only the option value
      and admin-menu visibility should change.
- [ ] Confirm a purchase-mode customer's access (e.g. to a gated product) is unaffected while
      role mode is active (the "silently preserved" behavior spec).

## D. Advanced features (v3.3.0)

**Lifecycle**
- [ ] A level with `expiry_days` set: assigning it, then advancing the server clock (or
      backdating the assignment in the DB for a test), triggers the daily cron to revoke it;
      a reminder email is sent exactly once, 3 days before expiry.
- [ ] `[wps_role_membership_register]` shows nothing when no level has self-signup enabled;
      shows the form (with only self-signup-enabled levels listed) once one does.
- [ ] Submitting the registration form with the honeypot field filled in (simulating a bot)
      is silently rejected.
- [ ] A logged-in member sees "Switch Membership Level" only for self-signup-enabled levels
      they don't already hold; switching revokes only their other self-signup levels, never
      an admin-assigned one.

**Content & access**
- [ ] A post/page restricted to a level with `drip_days` > 0 is inaccessible to a
      newly-assigned matching member, and becomes accessible once that many days have passed
      since their `assigned_at` (verify via a backdated test assignment).
- [ ] A level's discount scoped to specific product categories only discounts products in
      those categories, not the whole catalog; leaving categories unchecked still discounts
      everything (unchanged v3.2.0 behavior).
- [ ] With WPML active, the login heading/notice strings appear in WPML's String Translation
      screen after being saved; with WPML inactive, saving them causes no errors.

**Campaigns**
- [ ] The unsubscribe link in a received campaign email opts the recipient out; a
      tampered/invalid link shows an error instead of silently opting out an arbitrary user.
- [ ] The My Account "Email Preferences" checkbox and the emailed unsubscribe link both
      correctly reflect/set the same opt-out state.
- [ ] A "Schedule for later" campaign does not send immediately, sends at the requested time,
      and its history row updates from `scheduled` to `sent` with the correct recipient count.
- [ ] A "Drip" campaign never sends at compose time; assigning its trigger level to a user
      schedules (and, after the delay, delivers) exactly one email to that one user; the
      campaign's "Send To" audience selector has no effect on a drip campaign.
- [ ] Opening a received campaign email registers an "open" (check Campaign History's
      Opens/Clicks column); clicking a link in it redirects correctly AND registers a "click".
- [ ] An opted-out user receives no campaign email at all, regardless of audience or send mode.

**Admin & reporting**
- [ ] The Dashboard widget shows correct member/non-member/level counts and recent
      logins/campaigns, and its "Manage" link goes to the right admin page.
- [ ] CSV export of members includes every member's level(s) with correct assigned/expiry
      dates; CSV export of campaign history matches what's shown in Campaign History.
- [ ] CSV import of member↔level assignments works with both `user_email`+`level_name` and
      `user_id`+`level_id` column combinations; malformed rows are skipped, not fatal errors.
- [ ] The "Reports" bar chart and sparkline render proportionally correct bars for known
      test data (e.g. two levels with a 3:1 member ratio show roughly a 3:1 bar-width ratio).

**Integrations**
- [ ] Each REST endpoint (`GET /levels`, `GET /members/{id}`, `POST .../assign`,
      `POST .../revoke`) works for an authenticated `manage_options` user and returns 401/403
      for a logged-out or insufficiently-privileged request.
- [ ] Configuring a webhook URL and triggering the matching event (assign/revoke/campaign
      sent) results in a POST to that URL with the expected JSON payload; an unreachable
      webhook URL does not delay or break the triggering action (non-blocking).
- [ ] Tools → Export Personal Data and Tools → Erase Personal Data for a member both include
      this plugin's data (levels, login history, campaign opt-out) — export shows it; erasure
      removes level assignments but explicitly retains audit/login history, matching the
      documented behavior.

## C0. Tiered membership auto-upgrade

- [ ] Tick "Part of the auto-upgrade tier ladder" on two levels (e.g. Silver rank 1/₹0 — always
      qualifies — Gold rank 2/₹1,000) and assign a test customer Silver. The spend field only shows
      while the checkbox is ticked; Silver shows "Base tier" in the levels table. Complete an order for that customer
      totalling ₹1,000+; once it reaches Processing or Completed, the customer is automatically
      moved to Gold. Silver is revoked, the customer gets ONE "You have been upgraded to Gold"
      email (no "access ended" email for Silver), and the user-log shows the change.
- [ ] A level without the tier ladder checkbox is never touched by auto-upgrade logic and never
      appears in the ladder.
- [ ] A customer holding a high-rank non-ladder level plus Silver is still upgraded to Gold.
- [ ] Upgrading from DB 1.8.0: after loading any admin page, levels that had a threshold > 0 now
      have the ladder checkbox ticked.
- [ ] "Recalculate Tiers Now" on the Levels tab upgrades a member who already spent enough
      before the ladder existed, and reports how many members were upgraded.
- [ ] The Levels tab shows a warning when the ladder has only one level, two ladder levels share
      a rank, or a higher-rank ladder level has a lower/equal spend threshold.
- [ ] A paid order for a member no longer causes a fatal error, and the My Account "Membership"
      tab loads for members (regression for the `self::levels_table()` fatal).
- [ ] A customer's admin-assigned non-ladder level (threshold = 0) is left alone when they
      auto-upgrade on the ladder.
- [ ] A guest checkout (no account) never triggers an auto-upgrade.
- [ ] A customer who isn't already a role-based member (holds zero levels) is never auto-
      enrolled by spend alone, no matter how much they spend.
- [ ] Once at the top ladder tier, further spend triggers no further changes (no error, no
      duplicate assignment).
- [ ] My Account → Membership shows an "Upgrade to `<next tier>`" progress bar with the correct
      spent/remaining amounts and the next tier's discount percentage; it disappears once the
      member reaches the top tier or if no ladder is configured at all.

## D0. Private Site + login page registration path

- [ ] With Private Site off, guests can browse the whole site normally; only content you've
      explicitly restricted shows the membership notice.
- [ ] With Private Site on, every front-end page redirects a logged-out visitor to the login
      page, except the login page itself and any page carrying
      `[wps_role_membership_register]`.
- [ ] The login page shows a "New here? Join Membership" link pointing at that registration
      page, as long as at least one level has Self-Signup enabled; the link disappears if no
      level allows self-signup or no such page exists (rather than linking to a 404).

## E0. Level descriptions

- [ ] Adding/editing a level's Description and saving it persists (reload the edit form to
      confirm) and round-trips through Export/Import.
- [ ] The self-signup page (`[wps_role_membership_register]`) shows the selected level's
      description below the dropdown, updating live when the selection changes; a level with
      no description shows no extra line.
- [ ] My Account → Switch Membership Level shows each available level's description under its
      row.
- [ ] A "Buy access to X — price" link on restricted content shows the level's description
      next to it.
- [ ] Levels created before this field existed (empty description) show no broken/empty output
      anywhere above.

## E. Roles manager & charging for protected content (v3.4.0)

**Roles manager**
- [ ] Add a new role via the Roles tab; it appears in the list, and also appears in the
      Level form's "WordPress Role" dropdown afterward.
- [ ] Edit an existing role's name and capabilities; the name change reflects everywhere
      WordPress shows role names (Users screen "Role" column/filter), and capability changes
      take effect for users holding that role.
- [ ] Clone a role; the clone has identical capabilities to the source and a distinct slug.
- [ ] Delete is hidden (and server-side rejected if attempted via direct AJAX) for: any of
      the 5 default WP roles, and any role currently held by at least one user. A role with
      zero users and not a default role deletes successfully.
- [ ] The capability picker's search box filters within the active tab, and auto-switches
      to another tab if the active one has no matches for the query.
- [ ] Per-tab "Select All"/"Deselect All" only affects that tab's capabilities.

**Charging for protected content**
- [ ] Setting a level's Price > 0 creates a hidden WooCommerce product (visible in
      Products, catalog-visibility "hidden"); changing the price updates that same product's
      price rather than creating a duplicate; clearing the price back to 0 trashes it.
- [ ] A post/page/product restricted to a priced level shows a "Buy access to X — $Y" link
      in the restriction message; clicking it as a logged-out visitor goes to the membership
      login page first, then lands back on the same buy link after logging in.
- [ ] Registering via `[wps_role_membership_register]` with a **paid** level selected
      creates the account and goes straight to WooCommerce checkout (level NOT yet
      assigned); completing payment assigns the level. Selecting a **free** level still
      assigns immediately, unchanged from before.
- [ ] A guest checkout (no account) for a priced level's product still results in the level
      being assigned — to an existing account if the billing email matches one, otherwise a
      newly created account.
- [ ] My Account "Switch Membership Level" shows a priced level as "price + Buy Access"
      (not an instant Switch button); a direct POST to the switch-level endpoint with a paid
      level's id is rejected server-side and redirected to the buy flow instead of assigning
      for free.
- [ ] Deleting a level trashes (not permanently deletes) its linked product.

**Restriction metabox: Access / Error Message tabs**
- [ ] The restriction metabox on Post/Page/Product edit screens shows two tabs, "Access" and
      "Error Message"; switching tabs doesn't lose unsaved changes made in the other tab before
      clicking Save.
- [ ] Paid levels show their price next to the name in the Access tab's checklist.
- [ ] Typing a message in the Error Message tab and clicking Save Restriction persists it
      (reload the edit screen to confirm); a restricted visitor sees this exact message instead
      of the site-wide default.
- [ ] Clearing the Error Message field back to blank and saving reverts that object to the
      site-wide default message.
- [ ] A user who can generically edit posts but does not have edit rights on this specific
      object cannot save a restriction via direct AJAX call (permission check is per-object,
      not just the blanket `edit_posts` capability).

## F. Multisite

- [ ] Repeat activation checks on a multisite subsite, since the existing activator has a
      per-blog branch. Full multisite/network-wide level support is explicitly deferred
      (see the architecture doc) — this check is about the existing per-blog activation path
      still working, not about that deferred feature.

## G. Before merge

- [ ] Run `composer install` and the project's `phpcs.xml` ruleset against
      `includes/mode/` and `includes/role-based/`; fix any reported violations.
- [ ] Confirm `git diff release/3.1.3 --stat` shows only new files plus the two documented
      existing-file touches (the mode-controller bootstrap call, and the WPS-7885
      version/changelog commit).
