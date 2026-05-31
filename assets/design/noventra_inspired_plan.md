Noventra-inspired UI + Responsiveness Plan

Goal: Implement a responsive, professional UI inspired by https://www.noventrasolutions.com/ across key sections: hero, services, process, portfolio, CTAs, testimonials, and footer.

Scope & Mapping
- Hero: Large left-aligned headline, supportive subhead, primary CTA and secondary CTA; visual on right (illustration or hero cards). Make hero responsive: stack on mobile.
- Services: Clean 3-4 column cards with icon, title, short description, subtle hover shadow, link to details.
- Process: Numbered steps with short title and 1-line description; compact and center-aligned on narrow screens.
- Portfolio: Equal-height cards with consistent image aspect, title overlay, tag filters responsive (wrap into rows on mobile).
- Testimonials: Quote cards with author and company, carousel on small screens.
- CTAs: Full-width CTA band sections with contrasting background and strong primary button.
- Footer: Clear contact block, social links, small sitemap column.

Technical approach (incremental):
1. Add CSS variables and base typography in `assets/css/style.css` (or new file) for colors, spacing, radii.
2. Implement hero layout HTML tweak in `index.php` and add responsive CSS rules.
3. Restyle service and portfolio cards using flexbox, ensure equal heights.
4. Add lightweight JS for testimonial carousel (Swiper is already included) and smooth scroll handlers.
5. Cross-browser testing and small fixes in `assets/css/responsive.css`.

Immediate next step: implement step 1 (CSS variables + base typography) and create a hero CSS skeleton. After that I'll implement the hero HTML/CSS and request visual confirmation.

Notes:
- I will keep changes incremental and provide a quick preview after each major step.
- Confirm whether you want exact visual parity or an "inspired" approach (avoid direct copying).