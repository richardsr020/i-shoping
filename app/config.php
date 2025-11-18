<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FASHIONISTA BOUTIQUE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === STYLES GÉNÉRAUX === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #fff;
            color: #333;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* === HEADER MOBILE === */
        header {
            background-color: #000;
            color: #fff;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: white;
        }

        .search-bar {
            display: none;
            width: 100%;
            margin: 15px 0;
        }

        .search-bar.active {
            display: block;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 20px;
            border: none;
            font-size: 16px;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icons i {
            font-size: 20px;
            cursor: pointer;
            color: white;
        }

        .auth-buttons {
            display: none;
        }

        .mobile-search-btn {
            display: block;
        }

        /* === NAVIGATION MOBILE === */
        nav.main-nav {
            background-color: #fff;
            padding: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 70px;
            z-index: 999;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .mobile-nav {
            position: fixed;
            top: 0;
            left: -100%;
            width: 85%;
            height: 100%;
            background-color: white;
            z-index: 1100;
            transition: all 0.3s;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            padding: 20px;
        }

        .mobile-nav.active {
            left: 0;
        }

        .nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .nav-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }

        .mobile-nav-links li {
            border-bottom: 1px solid #eee;
        }

        .mobile-nav-links a {
            display: flex;
            align-items: center;
            padding: 15px 10px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .mobile-nav-links a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .mobile-nav-links a:hover {
            color: #ff4500;
        }

        .mobile-nav-auth {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .signin-btn {
            background-color: #1e90ff;
            color: white;
        }

        .register-btn {
            background-color: #ff4500;
            color: white;
        }

        .nav-links {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: white;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            z-index: 998;
        }

        .nav-links.active {
            display: flex;
        }

        .nav-links li {
            border-bottom: 1px solid #eee;
        }

        .nav-links a {
            display: block;
            color: #333;
            padding: 15px 20px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: #ff4500;
            background-color: #f9f9f9;
        }

        /* === CONTENU PRINCIPAL === */
        main {
            padding: 20px 0;
        }

        /* Section Boutique */
        .boutique-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .boutique-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .follow-btn {
            background-color: #ffa500;
            color: white;
            padding: 12px 30px;
            border-radius: 20px;
            font-size: 16px;
        }

        /* Section À propos et Informations */
        .boutique-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 40px;
        }

        .about-us, .information {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .about-us h2, .information h2 {
            margin-bottom: 15px;
            font-size: 22px;
        }

        .about-us p {
            margin-bottom: 20px;
            color: #666;
            line-height: 1.6;
        }

        .view-more-btn {
            background-color: transparent;
            color: #333;
            border: 1px solid #333;
            width: 100%;
            padding: 12px;
        }

        .info-list li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .info-list i {
            margin-right: 10px;
            color: #ff4500;
            width: 20px;
        }

        .message-store-btn {
            background-color: #ff4500;
            color: white;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            font-size: 16px;
        }

        /* Section Produits */
        .products-section {
            margin-top: 40px;
        }

        .section-title {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 100%;
            height: 180px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .product-actions i {
            background-color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            font-size: 16px;
        }

        .product-info {
            padding: 15px;
        }

        .product-price {
            font-weight: bold;
            font-size: 18px;
            color: #333;
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
            flex-direction: column;
            gap: 25px;
            text-align: center;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .social-icons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .social-icons span {
            margin-bottom: 10px;
        }

        .social-icons div {
            display: flex;
            gap: 15px;
        }

        .social-icons i {
            font-size: 20px;
            cursor: pointer;
        }

        /* === OVERLAY POUR MENU MOBILE === */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 900;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* === RESPONSIVE - TABLETTE === */
        @media (min-width: 768px) {
            .container {
                padding: 0 20px;
            }
            
            .search-bar {
                display: block;
                flex: 1;
                max-width: 500px;
                margin: 0 20px;
            }
            
            .mobile-search-btn {
                display: none;
            }
            
            .auth-buttons {
                display: flex;
                gap: 10px;
            }
            
            .header-container {
                justify-content: space-between;
            }
            
            .menu-toggle {
                display: none;
            }
            
            .mobile-nav {
                display: none;
            }
            
            .nav-links {
                display: flex;
                flex-direction: row;
                position: static;
                width: auto;
                box-shadow: none;
                justify-content: space-between;
            }
            
            .nav-links li {
                border-bottom: none;
            }
            
            .nav-links a {
                padding: 5px 10px;
            }
            
            .boutique-header {
                flex-direction: row;
                justify-content: space-between;
                text-align: left;
            }
            
            .boutique-title {
                margin-bottom: 0;
            }
            
            .boutique-info {
                flex-direction: row;
            }
            
            .about-us {
                flex: 2;
            }
            
            .information {
                flex: 1;
            }
            
            .view-more-btn {
                width: auto;
            }
            
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            
            .product-image {
                height: 200px;
            }
            
            .footer-content {
                flex-direction: row;
                justify-content: space-between;
                text-align: left;
            }
            
            .footer-links {
                flex-direction: row;
            }
            
            .social-icons {
                flex-direction: row;
            }
            
            .social-icons span {
                margin-bottom: 0;
                margin-right: 15px;
            }
        }

        /* === RESPONSIVE - DESKTOP === */
        @media (min-width: 1024px) {
            .boutique-title {
                font-size: 32px;
            }
            
            .section-title {
                font-size: 24px;
            }
            
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .product-image {
                height: 220px;
            }
        }
    </style>
</head>
<body>
    <!-- === HEADER === -->
    <header>
        <div class="container">
            <div class="header-container">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">i shopping</div>
                <!-- <div class="search-bar" id="searchBar">
                    <input type="text" placeholder="Rechercher des produits...">
                </div> -->
                <div class="header-icons">
                    <i class="far fa-bell"></i>
                    <i class="fas fa-shopping-cart"></i>
                    <div class="auth-buttons">
                        <button class="btn signin-btn">Sign in</button>
                        <button class="btn register-btn">Register</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- === NAVIGATION PRINCIPALE === -->
    <nav class="main-nav">
        <div class="container">
            <div class="nav-container">
                <ul class="nav-links" id="navLinks">
                    <li><a href="#">HOME</a></li>
                    <li><a href="#">Q search...</a></li>
                    <li><a href="#">Categories</a></li>
                    <li><a href="#">Price</a></li>
                    <li><a href="#">Brand</a></li>
                    <li><a href="#">Size</a></li>
                    <li><a href="#">NEW</a></li>
                    <li><a href="#">SALE</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- === MENU MOBILE === -->
    <div class="mobile-nav" id="mobileNav">
        <div class="nav-header">
            <div class="logo">i shopping</div>
            <button class="nav-close" id="navClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="mobile-nav-links">
            <li><a href="#"><i class="fas fa-home"></i> HOME</a></li>
            <li><a href="#"><i class="fas fa-search"></i> Q search...</a></li>
            <li><a href="#"><i class="fas fa-th-large"></i> Categories</a></li>
            <li><a href="#"><i class="fas fa-tag"></i> Price</a></li>
            <li><a href="#"><i class="fas fa-copyright"></i> Brand</a></li>
            <li><a href="#"><i class="fas fa-expand-alt"></i> Size</a></li>
            <li><a href="#"><i class="fas fa-star"></i> NEW</a></li>
            <li><a href="#"><i class="fas fa-percent"></i> SALE</a></li>
        </ul>
        <div class="mobile-nav-auth">
            <button class="btn signin-btn">Sign in</button>
            <button class="btn register-btn">Register</button>
        </div>
    </div>

    <!-- === OVERLAY === -->
    <div class="overlay" id="overlay"></div>

    <!-- === CONTENU PRINCIPAL === -->
    <main class="container">
        <!-- Section Boutique -->
        <section class="boutique-header">
            <h1 class="boutique-title">FASHIONISTA BOUTIQUE</h1>
            <button class="btn follow-btn" id="followBtn">Follow</button>
        </section>

        <!-- Section À propos et Informations -->
        <section class="boutique-info">
            <div class="about-us">
                <h2>ABOUT US</h2>
                <p>Yow deserabine neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit. There is no one who loves pain itself, who seeks after it and wants to have it, simply because it is pain.</p>
                <button class="btn view-more-btn">View more...</button>
            </div>

            <div class="information">
                <h2>INFORMATION</h2>
                <ul class="info-list">
                    <li><i class="fas fa-map-marker-alt"></i> Location</li>
                    <li><i class="fas fa-phone"></i> Contact</li>
                    <li><i class="far fa-clock"></i> Opening Hours</li>
                    <li><i class="fas fa-shipping-fast"></i> Shipping</li>
                    <li><i class="fas fa-undo"></i> Returns</li>
                </ul>
                <button class="btn message-store-btn">Message Store</button>
            </div>
        </section>

        <!-- Section Produits -->
        <section class="products-section">
            <h2 class="section-title">PRODUCTS FROM THIS BOUTIQUE</h2>
            <div class="products-grid">
                <!-- Produit 1 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="T-shirt fashion">
                    </div>
                    <div class="product-actions">
                        <i class="far fa-heart"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-price">$773.000</div>
                    </div>
                </div>

                <!-- Produit 2 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1544441893-675973e31985?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Sac à main">
                    </div>
                    <div class="product-actions">
                        <i class="far fa-heart"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-price">$773.000</div>
                    </div>
                </div>

                <!-- Produit 3 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Robe élégante">
                    </div>
                    <div class="product-actions">
                        <i class="far fa-heart"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-price">$773.000</div>
                    </div>
                </div>

                <!-- Produit 4 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1539109136881-3be0616acf4b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Chaussures de luxe">
                    </div>
                    <div class="product-actions">
                        <i class="far fa-heart"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-price">$773.000</div>
                    </div>
                </div>

                <!-- Produit 5 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Bijoux">
                    </div>
                    <div class="product-actions">
                        <i class="far fa-heart"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-price">$773.000</div>
                    </div>
                </div>

                <!-- Produit 6 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Veste en cuir">
                    </div>
                    <div class="product-actions">
                        <i class="far fa-heart"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-price">$773.000</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- === FOOTER === -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#">ABOUT US</a>
                    <a href="#">CEBPOAISE EOBRICE</a>
                </div>
                <div class="social-icons">
                    <span>FOLLOW UN</span>
                    <div>
                        <i class="fab fa-facebook-f"></i>
                        <i class="fab fa-twitter"></i>
                        <i class="fab fa-instagram"></i>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // === JAVASCRIPT ===
        
        // Menu hamburger pour mobile
        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');
        const navClose = document.getElementById('navClose');
        const overlay = document.getElementById('overlay');
        
        menuToggle.addEventListener('click', () => {
            mobileNav.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        navClose.addEventListener('click', () => {
            mobileNav.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
        
        overlay.addEventListener('click', () => {
            mobileNav.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
        
        // Recherche mobile
        const mobileSearchBtn = document.getElementById('mobileSearchBtn');
        const searchBar = document.getElementById('searchBar');
        
        mobileSearchBtn.addEventListener('click', () => {
            searchBar.classList.toggle('active');
        });
        
        // Fermer la recherche si on clique en dehors
        document.addEventListener('click', (e) => {
            if (!mobileSearchBtn.contains(e.target) && !searchBar.contains(e.target)) {
                searchBar.classList.remove('active');
            }
        });
        
        // Effet de survol sur les cartes de produit
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.opacity = '0.9';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.opacity = '1';
            });
        });
        
        // Gestion du bouton "Follow"
        const followBtn = document.getElementById('followBtn');
        
        followBtn.addEventListener('click', () => {
            if (followBtn.textContent === 'Follow') {
                followBtn.textContent = 'Following';
                followBtn.style.backgroundColor = '#28a745';
            } else {
                followBtn.textContent = 'Follow';
                followBtn.style.backgroundColor = '#ffa500';
            }
        });
        
        // Animation simple pour les icônes de coeur
        const heartIcons = document.querySelectorAll('.fa-heart');
        
        heartIcons.forEach(icon => {
            icon.addEventListener('click', function() {
                this.classList.toggle('far');
                this.classList.toggle('fas');
                this.style.color = this.classList.contains('fas') ? '#ff4500' : '';
            });
        });
    </script>
</body>
</html>