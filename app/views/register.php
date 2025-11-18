<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - FASHIONISTA BOUTIQUE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === STYLES GÉNÉRAUX === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        /* === HEADER === */
        header {
            background-color: #000;
            color: #fff;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: white;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            color: #ff4500;
        }

        .back-to-home {
            color: white;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .back-to-home i {
            margin-right: 5px;
        }

        /* === CONTENU PRINCIPAL === */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .registration-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .registration-form {
            flex: 1;
            padding: 40px;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #222;
        }

        .form-header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input:focus, select:focus {
            border-color: #ff4500;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 69, 0, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .checkbox-group input {
            width: auto;
            margin-right: 10px;
            margin-top: 5px;
        }

        .checkbox-group label {
            font-size: 14px;
            color: #555;
        }

        .checkbox-group a {
            color: #ff4500;
        }

        .submit-btn {
            background-color: #ff4500;
            color: white;
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .submit-btn:hover {
            background-color: #e03d00;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 69, 0, 0.3);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #888;
        }

        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
            z-index: 1;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .social-btn:hover {
            background: #f5f5f5;
            transform: translateY(-2px);
        }

        .social-btn.facebook {
            color: #3b5998;
        }

        .social-btn.google {
            color: #db4437;
        }

        .login-link {
            text-align: center;
            color: #666;
        }

        .login-link a {
            color: #ff4500;
            font-weight: 500;
        }

        .registration-image {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            color: white;
        }

        .image-content h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .image-content p {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .benefits-list {
            list-style: none;
        }

        .benefits-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .benefits-list i {
            margin-right: 10px;
            color: #ffa500;
        }

        /* === FOOTER === */
        footer {
            background-color: #000;
            color: #fff;
            padding: 30px 0;
            margin-top: 40px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .social-icons i {
            font-size: 20px;
            cursor: pointer;
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .registration-container {
                flex-direction: column;
            }
            
            .registration-image {
                order: -1;
                padding: 30px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- === HEADER === -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    i shopping
                </div>
                <a href="#" class="back-to-home">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </header>

    <!-- === CONTENU PRINCIPAL === -->
    <main>
        <div class="container">
            <div class="registration-container">
                <div class="registration-form">
                    <div class="form-header">
                        <h1>Créez votre compte</h1>
                        <p>Rejoignez FASHIONISTA BOUTIQUE et découvrez un monde de mode</p>
                    </div>

                    <form id="registrationForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">Prénom</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Nom</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Adresse e-mail</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Confirmer le mot de passe</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>

                        <div class="form-group">
                            <label for="birthDate">Date de naissance</label>
                            <input type="date" id="birthDate" name="birthDate">
                        </div>

                        <div class="form-group">
                            <label for="gender">Genre</label>
                            <select id="gender" name="gender">
                                <option value="">Sélectionnez</option>
                                <option value="female">Femme</option>
                                <option value="male">Homme</option>
                                <option value="other">Autre</option>
                                <option value="prefer-not-to-say">Je préfère ne pas répondre</option>
                            </select>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">J'accepte les <a href="#">Conditions d'utilisation</a> et la <a href="#">Politique de confidentialité</a></label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter">
                            <label for="newsletter">Je souhaite m'abonner à la newsletter pour recevoir des offres exclusives</label>
                        </div>

                        <button type="submit" class="btn submit-btn">Créer mon compte</button>

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
                            Vous avez déjà un compte? <a href="#">Connectez-vous</a>
                        </div>
                    </form>
                </div>

                <div class="registration-image">
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
                    <a href="#">À propos</a>
                    <a href="#">Contact</a>
                    <a href="#">Confidentialité</a>
                    <a href="#">Conditions</a>
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

    <script>
        // Validation du formulaire d'inscription
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Validation du mot de passe
            if (password !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas.');
                return;
            }
            
            // Validation des conditions
            if (!terms) {
                alert('Veuillez accepter les conditions d\'utilisation.');
                return;
            }
            
            // Si tout est valide
            alert('Compte créé avec succès! Bienvenue chez FASHIONISTA BOUTIQUE.');
            // Ici, vous pouvez ajouter la logique pour envoyer les données au serveur
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
    </script>
</body>
</html>