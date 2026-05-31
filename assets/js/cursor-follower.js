(function(){
    const emojiDefault = '🙂';
    const emojiClick = '😄';
    const emojiSad = '😢';

    const el = document.createElement('div');
    el.id = 'cursor-follower';
    el.textContent = emojiDefault;
    document.body.appendChild(el);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let posX = mouseX;
    let posY = mouseY;
    const ease = 0.18;

    let isBigTimeout = null;

    function raf() {
        posX += (mouseX - posX) * ease;
        posY += (mouseY - posY) * ease;
        el.style.transform = `translate3d(${posX}px, ${posY}px, 0) translate(-50%, -50%)`;
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    }, {passive:true});

    // enlarge and smile on clickable interactions
    function triggerBigSmile() {
        el.textContent = emojiClick;
        el.classList.add('big');
        clearTimeout(isBigTimeout);
        isBigTimeout = setTimeout(() => {
            el.classList.remove('big');
            el.textContent = emojiDefault;
        }, 700);
    }

    // sad face on logout clicks
    function triggerSadAndNavigate(href) {
        el.textContent = emojiSad;
        el.classList.add('sad');
        // delay to let user see the sad face before navigation
        setTimeout(() => {
            window.location.href = href;
        }, 240);
    }

    // Listen for mousedown on interactive elements
    document.addEventListener('mousedown', (e) => {
        const target = e.target.closest('button, a, input[type="submit"], .btn');
        if (!target) return;

        // If this is a logout link, make it sad and navigate
        const href = target.getAttribute && target.getAttribute('href');
        if (href && href.indexOf('logout.php') !== -1) {
            e.preventDefault();
            triggerSadAndNavigate(href);
            return;
        }

        // normal click interaction
        triggerBigSmile();
    }, true);

    // Also react to keyboard activation (Enter/Space) on focused buttons
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            const active = document.activeElement;
            if (active && (active.tagName === 'BUTTON' || active.tagName === 'A' || active.classList.contains('btn'))) {
                triggerBigSmile();
            }
        }
    });

    // Hide on touch devices (optional) to avoid covering taps
    function detectTouch() {
        return ('ontouchstart' in window) || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
    }

    if (detectTouch()) {
        el.style.display = 'none';
    }
})();
