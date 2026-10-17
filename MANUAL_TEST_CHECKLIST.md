# Manual Test Checklist

## Public forms

- [ ] Contact form rejects missing/invalid name, email, phone, and message.
- [ ] Contact form rejects stale/missing CSRF tokens.
- [ ] Contact form rejects the honeypot and submissions completed too quickly.
- [ ] Contact form prevents double submission and shows visible field errors.
- [ ] Project form validates both steps and preserves the CSRF token.
- [ ] Project uploads reject unsupported MIME types, oversized files, excess files, and excess total size.
- [ ] Uploaded files receive random server-side names and cannot execute PHP.
- [ ] Confirmation messages distinguish database success from mail delivery failure.

## Admin

- [ ] Login rejects invalid CSRF tokens and locks repeated IP+username failures.
- [ ] Successful login regenerates the session ID.
- [ ] A seeded/default hash redirects to password change.
- [ ] Logout works only through the CSRF-protected POST form.
- [ ] Project/service/testimonial/submission deletes require POST, CSRF, and confirmation.
- [ ] Add/edit project rejects invalid enum values and non-HTTP(S) URLs.
- [ ] First-admin setup creates one account and removes its setup script.

## Navigation and accessibility

- [ ] Skip link reaches `#main-content`.
- [ ] Project cards open with Enter and Space and show a visible focus ring.
- [ ] Filter buttons expose the correct `aria-pressed` state.
- [ ] Mobile drawer is inaccessible while closed, traps focus while open, and closes with Escape.
- [ ] Test navigation at 320, 360, 375, 390, 414, 600, 767, 768, 810, 820, 900, 991, 992, 1024, 1025, 1100, 1280, 1440, and 1920px.

## SEO and deployment

- [ ] Canonical URL uses the production `SITE_URL`.
- [ ] Open Graph/Twitter image loads from the production domain.
- [ ] `robots.txt` and `sitemap.xml` resolve publicly.
- [ ] HTTPS redirect and HSTS work on the InfinityFree host.
- [ ] CSP allows every required production CDN and blocks unexpected sources.
