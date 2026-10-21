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
