import './bootstrap';

import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';

window.bootstrap = bootstrap;
window.Alpine = Alpine;

Alpine.start();

/**
 * Scroll-reveal: fade elements in as they enter the viewport.
 * Add the class `.reveal` to any element you want animated.
 */
document.addEventListener('DOMContentLoaded', () => {
    const targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px',
    });

    targets.forEach((el) => observer.observe(el));
});

/**
 * Auto-dismiss success alerts after 5 seconds.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert-success.alert-dismissible').forEach((alert) => {
        setTimeout(() => {
            const instance = bootstrap.Alert.getOrCreateInstance(alert);
            instance.close();
        }, 5000);
    });
});
