# Legacy (purchase-based) flow boundary

This folder marks the logical boundary of the plugin's original, purchase-based membership
flow — the code shipped prior to WPS-7875 (Role-Based Membership Mode). It is documentation
only; the actual legacy code has **not** been physically moved here.

## Why the code itself was not moved

The purchase-based flow is effectively the entire pre-existing plugin: the admin controller
(`admin/class-membership-for-woocommerce-admin.php`, ~200KB), the public controller
(`public/class-membership-for-woocommerce-public.php`), the common class
(`common/class-membership-for-woocommerce-common.php`), and the global functions class
(`includes/class-membership-for-woocommerce-global-functions.php`, 2000+ lines) — plus
hundreds of hardcoded asset-path strings (e.g. `MEMBERSHIP_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/...'`)
scattered across dozens of files. Physically relocating all of it in a single pass, on a shipped
plugin with no automated test suite, is a high-risk refactor on its own and was explicitly
scoped out of the WPS-7875 delivery in favor of an additive, non-destructive approach: the
existing files are kept exactly where they are and are not modified by this epic.

## What "legacy isolation" means in this codebase

- **No file in `admin/`, `public/`, `common/`, or `includes/` (outside `includes/mode/` and
  `includes/role-based/`) was created, edited, or moved by WPS-7875**, with the single exception
  of a two-line, purely additive bootstrap call added to
  `includes/class-membership-for-woocommerce.php` (see `includes/mode/README` below for exactly
  what those two lines do) and the version-number/changelog updates required by WPS-7885.
- The new role-based flow lives entirely under `includes/mode/` and `includes/role-based/`,
  fully separate from, and sharing no restriction logic with, the purchase-based flow.
- The purchase-based flow's runtime behavior (product purchasability, plan CPTs, onboarding
  wizard, emails) is unchanged in every mode — this is what makes an existing purchase-mode
  customer's access "silently preserved" when the admin switches to role mode.

## Deferred follow-up

A **physical** relocation of the purchase-flow files into this folder remains a legitimate,
separately-scoped follow-up project. Recommended prerequisite (not done here): introduce a
path-constant/helper indirection layer (e.g. `Membership_For_Woocommerce_Legacy_Paths::admin_url()`)
to replace the hardcoded path strings first, so the eventual physical move becomes a mechanical,
low-risk change instead of a plugin-wide find-and-replace.
