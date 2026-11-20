import './bootstrap';

const menuToggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#primary-navigation');

if (menuToggle && navigation) {
    menuToggle.addEventListener('click', () => {
        const open = navigation.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(open));
        menuToggle.textContent = open ? 'Close' : 'Menu';
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navigation.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.textContent = 'Menu';
        });
    });
}

// Register the PWA worker. Its fetch handler is limited to public static assets.
if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {
            // The site remains usable when service workers are unavailable.
        });
    });
}

// Show the native install prompt only when the browser supports it.
let installPrompt = null;
const installButton = document.querySelector('#install-mrk-pwa');
const installHint = document.querySelector('#install-mrk-hint');

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    installPrompt = event;
    if (installButton) installButton.hidden = false;
    if (installHint) installHint.hidden = true;
});

if (installButton) {
    installButton.addEventListener('click', async () => {
        if (!installPrompt) return;
        installPrompt.prompt();
        await installPrompt.userChoice;
        installPrompt = null;
        installButton.hidden = true;
    });
}

window.addEventListener('appinstalled', () => {
    if (installButton) installButton.hidden = true;
    if (installHint) installHint.textContent = 'MRK Study App انسٹال ہو گئی ہے۔';
});

// Subtle scroll-reveal animation; content remains visible if JS or observers are unavailable.
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealTargets = document.querySelectorAll(
        '.hero-grid > *, .section-heading, .cards article, .service-card, .project-card, .cta-box, .split > *, .contact-grid > *, .info-panel > *, .contact-points > *'
    );
    revealTargets.forEach((element, index) => {
        element.classList.add('reveal');
        element.classList.add('reveal-delay-' + (index % 4));
    });
    document.documentElement.classList.add('reveal-ready');
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -35px 0px' });
    revealTargets.forEach((element) => revealObserver.observe(element));
}
