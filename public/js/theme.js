/**
 * Gestion du système de thème (Light/Dark)
 */

(function() {
    'use strict';

    const ThemeManager = {
        // Obtenir le thème actuel
        getTheme: function() {
            return localStorage.getItem('theme') || 'light';
        },

        // Définir le thème
        setTheme: function(theme) {
            if (theme === 'dark' || theme === 'light') {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                this.updateThemeIcon(theme);
            }
        },

        // Basculer entre light et dark
        toggleTheme: function() {
            const currentTheme = this.getTheme();
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            this.setTheme(newTheme);
        },

        // Initialiser le thème au chargement
        init: function() {
            const savedTheme = this.getTheme();
            this.setTheme(savedTheme);

            // Ajouter l'événement au bouton de bascule
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    this.toggleTheme();
                });
            }
        },

        // Mettre à jour l'icône du thème
        updateThemeIcon: function(theme) {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                const icon = themeToggle.querySelector('i');
                if (icon) {
                    if (theme === 'dark') {
                        icon.className = 'fas fa-sun';
                    } else {
                        icon.className = 'fas fa-moon';
                    }
                }
            }
        }
    };

    // Initialiser quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            ThemeManager.init();
        });
    } else {
        ThemeManager.init();
    }

    // Exposer ThemeManager globalement pour utilisation ailleurs
    window.ThemeManager = ThemeManager;
})();




