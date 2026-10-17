# Oxionex Web Solutions Security Changelog

## Phase 1

- SEC-01: Removed database credentials from `config.php`; added local configuration loading and a placeholder example.
- SEC-02: Disabled debug output and stopped database DSN/user leakage.
- SEC-03: Removed the public diagnostic endpoint.
- SEC-04: Added Git ignore rules and upload execution protection.
- SEC-05: Added HTTPS, HSTS, CSP, Referrer-Policy, Permissions-Policy, and sensitive-file blocking.
- SEC-06: Replaced stale Pixelara configuration values and made AJAX database errors generic.

## Phase 2

- FORM-01: Added CSRF, honeypot, minimum-time, rate-limit, and field-length checks to public forms.
- FORM-02: Added MIME verification, upload allowlists, random filenames, file-count limits, and total-size limits.
- FORM-03: Stopped double-escaping values before database storage; escaped at output boundaries.
- FORM-04: Removed duplicate contact submit handling and duplicate `main.js` loading.
- FORM-05: Checked mail delivery and report confirmation-mail failures honestly.

## Phase 3

- ADMIN-01: Added CSRF protection to admin forms and destructive actions.
- ADMIN-02: Converted project deletion and logout to POST-only operations.
- ADMIN-03: Added IP+username login lockouts and session ID regeneration.
- ADMIN-04: Added first-login password change and one-time administrator setup.
- ADMIN-05: Aligned project category/status values with database enums and validated URLs.
- ADMIN-06: Removed the public Admin footer link.

## Phase 4

- REL-01: Added PDO-null/Throwable fallback handling for the homepage.
- REL-02: Added preloader DOM-ready, load, reduced-motion, and hard-timeout fallbacks.
- REL-03: Removed automatic notification permission prompts.
- REL-04: Corrected the image fallback path.
- REL-05: Removed duplicate and unused frontend libraries and deferred active scripts.

## Phase 5

- RESP-01: Consolidated mobile navigation around the Bootstrap-compatible `991.98px` boundary.
- RESP-02: Added inert drawer behavior and keyboard focus trapping.
- RESP-03: Replaced portfolio inline display animation with the native `hidden` state.
- RESP-04: Replaced forced flex layouts with responsive grids and removed dead nth-child layout rules.
- RESP-05: Removed the responsive Bootstrap `.container` override and OS dark-mode override.
- RESP-06: Added stable `100svh` hero sizing with a tall-screen cap.

## Phase 6

- A11Y-01: Added `<main id="main-content">`, project-card keyboard behavior, filter `aria-pressed`, and visible focus states.
- SEO-01: Added canonical, Open Graph, Twitter, and Organization JSON-LD metadata.
- SEO-02: Added `robots.txt` and `sitemap.xml`.
- CONTENT-01: Centralized visible contact details and corrected email, LinkedIn, WhatsApp, and markup errors.

## Data requiring owner verification

The seeded database currently contains sample statistics, testimonials, project descriptions, client names, performance claims, and `live_link` values of `#`. These are not verified production claims and must be replaced with real data before launch. The hard-coded fallback statistics in the homepage should also be replaced with confirmed values.
