    <style>
        .site-footer {
            margin-top: var(--spacing-2xl);
            padding: 18px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            background: linear-gradient(180deg, var(--color-bg), var(--color-bg-secondary));
        }

        [data-theme="dark"] .site-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .site-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .site-footer-copy {
            color: var(--color-text-muted);
            font-size: 12px;
            opacity: 0.9;
            letter-spacing: 0.02em;
        }

        .site-footer-links {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .site-footer-links a {
            color: var(--color-text-muted);
            font-size: 12px;
            opacity: 0.88;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .site-footer-links a:hover {
            color: var(--color-primary);
            opacity: 1;
        }
    </style>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="site-footer-row">
                <p class="site-footer-copy">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits reserves.</p>
                <nav class="site-footer-links" aria-label="Liens du footer">
                    <a href="<?php echo url('home'); ?>">Accueil</a>
                    <a href="<?php echo url('about'); ?>">A propos</a>
                    <a href="<?php echo url('contact'); ?>">Contact</a>
                    <a href="<?php echo url('terms'); ?>">Terms</a>
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>



