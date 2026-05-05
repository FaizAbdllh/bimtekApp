import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Defer Alpine start to allow inline scripts to register components
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});
