import './bootstrap';

const menuToggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.primary-navigation');

if (menuToggle && navigation) {
    menuToggle.addEventListener('click', () => {
        const open = navigation.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navigation.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
    });
}
