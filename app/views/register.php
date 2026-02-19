<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/pages.css">
    <style>
        /* Styles spécifiques register */
        .registration-container {
            background: var(--color-bg);
            border-radius: var(--radius-lg);
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: var(--spacing-lg);
            gap: var(--spacing-sm);
        }
        
        .checkbox-group input {
            width: auto;
            margin-top: 4px;
        }
        
        .checkbox-group a {
            color: var(--color-primary);
        }
        
        .registration-image {
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
            <div class="auth-container registration-container">
                <div class="auth-form registration-form">
                    <div class="form-header">
                        <h1>Créez votre compte</h1>
                        <p>Rejoignez <?php echo APP_NAME; ?> et découvrez un monde de mode</p>
                    </div>

                    <?php 
                    $errors = isset($_SESSION['view_data']['errors']) ? $_SESSION['view_data']['errors'] : [];
                    $form_data = isset($_SESSION['view_data']['form_data']) ? $_SESSION['view_data']['form_data'] : [];
                    if (isset($errors['general'])): ?>
                        <div class="error-general">
                            <?php echo htmlspecialchars($errors['general']); ?>
                        </div>
                    <?php endif; ?>

                    <form id="registrationForm" method="POST" action="<?php echo url('register'); ?>&action=register">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName" class="form-label">Prénom</label>
                                <input type="text" id="firstName" name="firstName" class="form-input <?php echo isset($errors['first_name']) ? 'error' : ''; ?>" value="<?php echo isset($form_data['first_name']) ? htmlspecialchars($form_data['first_name']) : ''; ?>" autocomplete="given-name" required>
                                <?php if (isset($errors['first_name'])): ?>
                                    <span class="error-message"><?php echo htmlspecialchars($errors['first_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="lastName" class="form-label">Nom</label>
                                <input type="text" id="lastName" name="lastName" class="form-input <?php echo isset($errors['last_name']) ? 'error' : ''; ?>" value="<?php echo isset($form_data['last_name']) ? htmlspecialchars($form_data['last_name']) : ''; ?>" autocomplete="family-name" required>
                                <?php if (isset($errors['last_name'])): ?>
                                    <span class="error-message"><?php echo htmlspecialchars($errors['last_name']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" id="email" name="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>" value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>" autocomplete="email" required>
                            <?php if (isset($errors['email'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="password-field">
                                <input type="password" id="password" name="password" class="form-input <?php echo isset($errors['password']) ? 'error' : ''; ?>" autocomplete="new-password" required>
                                <button type="button" class="password-toggle" aria-label="Afficher le mot de passe" aria-pressed="false" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                            <div class="password-field">
                                <input type="password" id="confirmPassword" name="confirmPassword" class="form-input <?php echo isset($errors['confirm_password']) ? 'error' : ''; ?>" autocomplete="new-password" required>
                                <button type="button" class="password-toggle" aria-label="Afficher le mot de passe" aria-pressed="false" data-target="confirmPassword">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="birthDate" class="form-label">Date de naissance</label>
                            <input type="date" id="birthDate" name="birthDate" class="form-input" value="<?php echo isset($form_data['birth_date']) ? htmlspecialchars($form_data['birth_date']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="gender" class="form-label">Genre</label>
                            <select id="gender" name="gender" class="form-select">
                                <option value="">Sélectionnez</option>
                                <option value="female" <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'female') ? 'selected' : ''; ?>>Femme</option>
                                <option value="male" <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'male') ? 'selected' : ''; ?>>Homme</option>
                                <option value="other" <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'other') ? 'selected' : ''; ?>>Autre</option>
                                <option value="prefer-not-to-say" <?php echo (isset($form_data['gender']) && $form_data['gender'] == 'prefer-not-to-say') ? 'selected' : ''; ?>>Je préfère ne pas répondre</option>
                            </select>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">J'accepte les <a href="<?php echo url('terms'); ?>">Conditions d'utilisation</a> et les <a href="<?php echo url('terms'); ?>">Terms</a></label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter">
                            <label for="newsletter">Je souhaite m'abonner à la newsletter pour recevoir des offres exclusives</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Créer mon compte</button>

                        <div class="divider">
                            <span>Ou inscrivez-vous avec</span>
                        </div>

                        <div class="social-login">
                            <button type="button" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                            <button type="button" class="social-btn google">
                                <i class="fab fa-google"></i> Google
                            </button>
                        </div>

                        <div class="login-link">
                            Vous avez déjà un compte? <a href="<?php echo url('login'); ?>">Connectez-vous</a>
                        </div>
                    </form>
                </div>

                <div class="auth-image registration-image">
                    <div class="image-content">
                        <h2>Rejoignez notre communauté de mode</h2>
                        <p>Inscrivez-vous dès maintenant et bénéficiez d'avantages exclusifs :</p>
                        <ul class="benefits-list">
                            <li><i class="fas fa-check-circle"></i> Réductions spéciales membres</li>
                            <li><i class="fas fa-check-circle"></i> Accès anticipé aux nouvelles collections</li>
                            <li><i class="fas fa-check-circle"></i> Suggestions personnalisées</li>
                            <li><i class="fas fa-check-circle"></i> Service client prioritaire</li>
                            <li><i class="fas fa-check-circle"></i> Retours gratuits</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- === FOOTER === -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="<?php echo url('about'); ?>">A propos</a>
                    <a href="<?php echo url('contact'); ?>">Contact</a>
                    <a href="<?php echo url('terms'); ?>">Terms</a>
                    <a href="<?php echo url('home'); ?>">Accueil</a>
                </div>
                <div class="social-icons">
                    <span>SUIVEZ-NOUS</span>
                    <i class="fab fa-facebook-f"></i>
                    <i class="fab fa-twitter"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-pinterest"></i>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo BASE_URL; ?>/public/js/theme.js"></script>
    <script>
        // Validation côté client du formulaire d'inscription
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Validation du mot de passe
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
            
            // Validation des conditions
            if (!terms) {
                e.preventDefault();
                alert('Veuillez accepter les conditions d\'utilisation.');
                return false;
            }
            
            // Le formulaire sera soumis au serveur
            return true;
        });

        // Animation des champs de formulaire
        const inputs = document.querySelectorAll('input, select');
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
