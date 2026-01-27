// Theme Toggle Script - Professional Slider
(function () {
    'use strict';

    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';

    // Apply theme immediately to prevent flash
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        // Select existing checkbox from DOM
        const checkbox = document.querySelector('.theme-switch input[type="checkbox"]');

        if (!checkbox) return;

        // Sync initial state
        checkbox.checked = savedTheme === 'dark';



        // Toggle theme function
        function toggleTheme() {
            const newTheme = checkbox.checked ? 'dark' : 'light';

            // Update theme
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Add change event
        checkbox.addEventListener('change', toggleTheme);

        // Optional: Add keyboard shortcut (Ctrl/Cmd + Shift + D)
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                checkbox.checked = !checkbox.checked;
                toggleTheme();
            }
        });
    });
})();

