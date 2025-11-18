<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - FASHIONISTA BOUTIQUE</title>
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

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-form {
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #ff4500;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 69, 0, 0.1);
        }

        .password-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .forgot-password {
            color: #ff4500;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me input {
            width: auto;
            margin-right: 10px;
        }

        .remember-me label {
            margin-bottom: 0;
            font-size: 14px;
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

        .register-link {
            text-align: center;
            color: #666;
        }

        .register-link a {
            color: #ff4500;
            font-weight: 500;
        }

        .login-image {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
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
            .login-container {
                flex-direction: column;
            }
            
            .login-image {
                order: -1;
                padding: 30px;
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
                    <i class="fas fa-home"></i> 
                </a>
            </div>
        </div>
    </header>

    <!-- === CONTENU PRINCIPAL === -->
    <main>
        <div class="container">
            <div class="login-container">
                <div class="login-form">
                    <div class="form-header">
                        <h1>Connectez-vous</h1>
                        <p>Accédez à votre compte FASHIONISTA BOUTIQUE</p>
                    </div>

                    <form id="loginForm">
                        <div class="form-group">
                            <label for="email">Adresse e-mail</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <div class="password-header">
                                <label for="password">Mot de passe</label>
                                <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                            </div>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>

                        <button type="submit" class="btn submit-btn">Se connecter</button>
        
                    </form>
                </div>

                <div class="login-image">
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

    <!-- === FOOTER === -->


    <script>
        // Validation du formulaire de connexion
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Validation basique
            if (!email || !password) {
                alert('Veuillez remplir tous les champs.');
                return;
            }
            
            // Si tout est valide
            alert('Connexion réussie! Redirection...');
            // Ici, vous pouvez ajouter la logique pour envoyer les données au serveur
            // et rediriger l'utilisateur vers son compte
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
    </script>
</body>
</html>