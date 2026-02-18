<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/pages.css">
    <style>
        /* Styles spécifiques login */
        .login-container {
            background: var(--color-bg);
            border-radius: var(--radius-lg);
        }
        
        .login-form input,
        .login-form label {
            width: 100%;
        }
        
        .password-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-sm);
        }
        
        .forgot-password {
            color: var(--color-primary);
            font-size: 14px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            gap: var(--spacing-sm);
        }
        
        .remember-me input {
            width: auto;
        }
        
        .submit-btn {
            width: 100%;
        }
        
        .login-image {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
        }
    </style>
</head>
<body>
    <!-- === HEADER === -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="<?php echo url('home'); ?>" class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    <span>i shopping</span>
                </a>
                <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                    <a href="<?php echo url('home'); ?>" class="btn btn-ghost btn-sm">
                        <i class="fas fa-arrow-left"></i> Accueil
                    </a>
                    <button id="theme-toggle" class="theme-toggle" aria-label="Changer de thème">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- === CONTENU PRINCIPAL === -->
    <main>
        <div class="container">
            <div class="auth-container login-container">
                <div class="auth-form login-form">
                    <div class="form-header">
                        <h1>Connectez-vous</h1>
                        <p>Accédez à votre compte <?php echo APP_NAME; ?></p>
                    </div>

                    <?php 
                    $errors = isset($_SESSION['view_data']['errors']) ? $_SESSION['view_data']['errors'] : [];
                    $form_data = isset($_SESSION['view_data']['form_data']) ? $_SESSION['view_data']['form_data'] : [];
                    if (isset($errors['general'])): ?>
                        <div class="error-general">
                            <?php echo htmlspecialchars($errors['general']); ?>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" method="POST" action="<?php echo url('login'); ?>&action=login">
                        <div class="form-group">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" id="email" name="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>" value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>" autocomplete="email" autofocus required>
                            <?php if (isset($errors['email'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="password-header">
                                <label for="password">Mot de passe</label>
                                <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                            </div>
                            <div class="password-field">
                                <input type="password" id="password" name="password" class="form-input <?php echo isset($errors['password']) ? 'error' : ''; ?>" autocomplete="current-password" required>
                                <button type="button" class="password-toggle" aria-label="Afficher le mot de passe" aria-pressed="false" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
        
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Se connecter</button>

                        <div class="register-link" style="margin-top: 20px;">
                            Vous n'avez pas de compte? <a href="<?php echo url('register'); ?>">Inscrivez-vous</a>
                        </div>
                    </form>
                </div>

                <div class="auth-image login-image">
                    <div class="image-content">
                        <h2>Reconnectez-vous à votre univers mode</h2>
                        <p>Accédez à tous les avantages de votre compte Fashionista :</p>
                        <ul class="benefits-list">
                            <li><i class="fas fa-check-circle"></i> Votre historique de commandes</li>
                            <li><i class="fas fa-check-circle"></i> Vos listes de souhaits</li>
                            <li><i class="fas fa-check-circle"></i> Recommandations personnalisées</li>
                            <li><i class="fas fa-check-circle"></i> Offres exclusives membres</li>
                            <li><i class="fas fa-check-circle"></i> Service client prioritaire</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="<?php echo BASE_URL; ?>/public/js/theme.js"></script>
    <script>
        // Validation côté client du formulaire de connexion
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Validation basique
            if (!email || !password) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs.');
                return false;
            }
            
            // Le formulaire sera soumis au serveur
            return true;
        });

        // Animation des champs de formulaire
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Simulation de la réinitialisation de mot de passe
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            const email = prompt('Veuillez saisir votre adresse e-mail pour réinitialiser votre mot de passe:');
            if (email) {
                alert(`Un lien de réinitialisation a été envoyé à ${email}`);
            }
        });

        // Affichage / masquage du mot de passe
        document.querySelectorAll('.password-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;

                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                btn.setAttribute('aria-label', isPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe');

                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-solid');
                    icon.classList.remove('fa-regular');
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }
            });
        });
    </script>
</body>
</html>