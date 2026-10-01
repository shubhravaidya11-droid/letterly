const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.main-nav');

if (menuButton && navigation) {
    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', String(!isOpen));
        navigation.classList.toggle('is-open', !isOpen);
    });
    navigation.addEventListener('click', (event) => {
        if (event.target.closest('a') && window.matchMedia('(max-width: 720px)').matches) {
            menuButton.setAttribute('aria-expanded', 'false');
            navigation.classList.remove('is-open');
        }
    });
}

const revealItems = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    revealItems.forEach((item) => revealObserver.observe(item));
} else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
}

document.querySelectorAll('[data-print-letter]').forEach((button) => {
    button.addEventListener('click', () => window.print());
});

document.querySelector('[data-preview-letter]')?.addEventListener('click', () => {
    document.querySelector('#letter-preview-panel')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
});