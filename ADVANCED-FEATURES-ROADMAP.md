# Advanced Features Roadmap — Role-Based Membership Mode

This is a roadmap of features that could reasonably be added on top of what's already built
(see `ARCHITECTURE-role-based-mode.md` for what exists today). Nothing here is built yet.
Each item has a rough effort estimate and, where relevant, the reason it wasn't done already.
Effort is relative to this module's own codebase, not an absolute time estimate.

## Already-flagged follow-ups (carried over from delivery notes)

These were identified during the build and deliberately deferred — listed here for
completeness, with more detail in `ARCHITECTURE-role-based-mode.md`:

- **Physical relocation** of the purchase-flow files into `includes/legacy/` (currently a
  logical/documentation-only boundary).
- **SMS/WhatsApp notification channel** — open scope decision, no provider chosen.
- **Per-product/per-category discount rules** — today's discount is a single flat percentage
  per level, site-wide.
- **Queue/batch email sending** — campaigns and notifications are synchronous `wp_mail()`
  loops; fine at normal scale, risky at very large audiences.
- **REST API collection-level restriction filtering** — only single-item REST reads are
  currently hidden from non-members.

## Membership lifecycle

| Feature | Value | Effort |
|---|---|---|
| **Time-limited levels / auto-expiry** — a level assignment that automatically expires after N days and revokes the role. | Trial periods, seasonal access, subscription-like behavior without payment processing. | Medium — needs a daily cron check against an `expires_at` column on the existing assignment. |
| **Renewal / expiry reminder emails** | Reduces silent churn; reuses the campaign email infrastructure already built. | Low-Medium — mostly a scheduled query + template, once expiry (above) exists. |
| **Self-service signup/registration flow** | Right now only an admin can assign a level (profile screen / bulk action). A public "become a member" form would let visitors self-enroll into a level (free tiers, waitlists). | Medium-High — needs a public form, spam/abuse protection, and a decision on whether self-signup should require admin approval. |
| **Level upgrade/downgrade self-service UI** | Lets a logged-in member switch their own level from My Account, instead of only an admin doing it. | Medium — mostly UI; the underlying assign/revoke methods already exist. |

## Content & access control

| Feature | Value | Effort |
|---|---|---|
| **Content dripping** (unlock content N days after a level was assigned, rather than immediately) | Classic membership-site pattern; keeps content consumption paced. | Medium — needs an "assigned_at" timestamp (exists via the audit log) and a date comparison in the restriction check. |
| **Richer role hierarchy** (beyond the current single numeric rank) — named tiers, visual ladder | More intuitive for admins with many levels than a bare number. | Medium — mostly UI polish over the existing rank column. |
| **WPML / multilingual support** for restriction messages and the login page | This plugin already ships a `wpml-config.xml`, so multilingual sites are a real audience; our restriction messages and login page copy aren't currently registered as translatable strings for WPML's string translation. | Low-Medium — register the relevant options/strings, no architecture change. |
| **Category/taxonomy-based discount rules** (alongside the existing product-level discount) | More granular than "one discount for the whole catalog." | Medium — extends the existing pricing filters with a lookup by product category. |

## Campaigns & communication

| Feature | Value | Effort |
|---|---|---|
| **Branded HTML email templates** (header/footer, logo, colors) for both campaigns and per-event notifications | Currently plain HTML strings; a consistent template raises perceived quality. | Low-Medium — a shared template wrapper function, no data-model change. |
| **Unsubscribe / preference center** | Real compliance concern (CAN-SPAM/GDPR) once campaign emails are actually being sent at any volume — currently there's no opt-out. | Medium — a token-based unsubscribe link + a per-user "opted out" flag checked before sending. **Recommend prioritizing this before campaigns are used in production at scale.** |
| **Scheduled / recurring campaigns** (send later, or a repeating drip sequence triggered by level assignment — e.g. a 3-part welcome series) | Turns the one-shot broadcast tool into an automated lifecycle-marketing tool. | Medium-High — needs WP-Cron scheduling and a small state machine for drip steps. |
| **Campaign analytics** (open/click tracking) | Lets the admin see whether campaigns are actually being read. | Medium-High — needs a tracking pixel + link-wrapping/redirect endpoint, plus a stats view. |
| **A/B subject-line testing** | Marginal gain for most use cases; only worth it once campaign volume justifies it. | Medium. |

## Admin & reporting

| Feature | Value | Effort |
|---|---|---|
| **Dashboard widget** (member count, recent signups, recent campaign performance) | At-a-glance visibility without opening the full admin page. | Low. |
| **CSV export** of members (with their levels) and of campaign history | Easier reporting/handoff to non-technical stakeholders than the existing JSON level export. | Low. |
| **Bulk CSV import of member↔level assignments** (distinct from the existing level-definition import/export) | Useful for migrating from another membership system or onboarding a large existing list at once. | Medium. |
| **Charts** (member growth over time, level distribution, churn) | Turns the raw login/audit log data already being collected into something visual. | Medium — data already exists in `mfw_role_membership_user_log`; mostly a charting UI. |

## Integrations & extensibility

| Feature | Value | Effort |
|---|---|---|
| **REST API for this module** (list levels, assign/revoke a level, read a member's status) | Enables headless frontends, mobile apps, or external automation (Zapier, Make) without going through wp-admin. | Medium — a handful of `register_rest_route()` endpoints wrapping the existing repository methods. |
| **Outbound webhooks** on assign/revoke/campaign-sent events | Lets external systems (CRM, Slack, analytics) react to membership events without polling. | Low-Medium — the `do_action` hooks these would key off already exist (`mfw_role_based_after_assign_level`, etc.); mostly needs a webhook-URL config UI + an HTTP POST on each hook. |
| **Social login** on the membership login page (Google/Facebook, etc.) | Lowers friction for the login flow already built. | Medium-High — typically means adopting a well-audited third-party OAuth library rather than hand-rolling it; a security-sensitive addition. |
| **Two-factor authentication** for high-value roles | Meaningful security improvement if any role-membership level maps to elevated capabilities. | Medium — similarly, best built on a well-audited existing 2FA library/plugin integration rather than from scratch. |
| **Multisite / network-wide levels** | Lets one level definition apply across a multisite network instead of per-site. | High — touches the data layer's assumptions about a single site's `$wpdb` prefix throughout. |

## Compliance & security

| Feature | Value | Effort |
|---|---|---|
| **GDPR data export/erasure** for role-membership data (levels, login log, campaign history involving a user) | WordPress core has built-in personal-data export/erasure hooks (`wp_privacy_personal_data_exporters/erasers`) that this module doesn't currently register with. | Low-Medium — mostly wiring existing repository queries into those core hooks. |
| **Campaign consent/opt-out tracking** | See "Unsubscribe / preference center" above — same underlying need, listed here for the compliance angle specifically. | (See above.) |
| **Login rate-limiting / brute-force protection** on the membership login page | The login form currently relies entirely on WordPress core's own (minimal) protection. | Low-Medium — a simple attempt-counter + temporary lockout, or point admins at an existing well-audited security plugin instead of reinventing one. |

## Suggested near-term priority

If picking a small next batch rather than everything above, in rough priority order:

1. **Unsubscribe/preference center** — genuine compliance gap the moment campaigns are used for real.
2. **Time-limited levels / auto-expiry** + **renewal reminders** — the most commonly requested "membership plugin" feature that's currently missing entirely.
3. **REST API** — unlocks integrations without forcing every future need through more wp-admin UI.
4. **Branded HTML email templates** — cheap, visible quality improvement across everything already built.
5. **GDPR export/erasure hooks** — cheap, and closes a real (if currently low-likelihood) compliance gap.
