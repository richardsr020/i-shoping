<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveaux Produits - FASHIONISTA BOUTIQUE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === STYLES GÉNÉRAUX === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        :root {
            --primary: #ff4500;
            --primary-dark: #e03d00;
            --secondary: #1e90ff;
            --dark: #000;
            --light: #fff;
            --gray: #f8f9fa;
            --gray-dark: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* === SIDEBAR === */
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 24px;
        }

        .logo h1 {
            font-size: 20px;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 5px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ccc;
            transition: all 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            background-color: #333;
            color: white;
            border-left: 4px solid var(--primary);
        }

        .nav-links i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* === CONTENU PRINCIPAL === */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .top-header {
            background-color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            margin: 0 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Contenu de la page produits */
        .products-content {
            padding: 30px;
            flex: 1;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .page-title p {
            color: var(--gray-dark);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        /* Formulaire d'ajout de produit */
        .product-form {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .form-section {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .section-header i {
            margin-right: 10px;
            color: var(--primary);
        }

        .section-header h3 {
            font-size: 18px;
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

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 69, 0, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Upload d'images */
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .image-upload:hover {
            border-color: var(--primary);
        }

        .upload-icon {
            font-size: 40px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .upload-text {
            margin-bottom: 15px;
        }

        .upload-text p {
            color: var(--gray-dark);
            font-size: 14px;
        }

        .image-preview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 120px;
            background-color: #f8f9fa;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* Options de produit */
        .options-list {
            margin-bottom: 20px;
        }

        .option-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .option-details {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .option-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .option-actions {
            display: flex;
            gap: 10px;
        }

        .option-actions button {
            background: none;
            border: none;
            color: var(--gray-dark);
            cursor: pointer;
        }

        .option-actions button:hover {
            color: var(--primary);
        }

        .add-option {
            display: flex;
            gap: 10px;
        }

        .add-option input {
            flex: 1;
        }

        /* Produits récemment ajoutés */
        .recent-products {
            margin-top: 30px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 160px;
            background-color: #f8f9fa;
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

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .product-price {
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--gray-dark);
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .product-actions button {
            background: none;
            border: none;
            color: var(--gray-dark);
            cursor: pointer;
        }

        .product-actions button:hover {
            color: var(--primary);
        }

        /* === RESPONSIVE === */
        @media (max-width: 1200px) {
            .product-form {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px 0;
            }
            
            .nav-links {
                display: flex;
                overflow-x: auto;
            }
            
            .nav-links li {
                flex: 0 0 auto;
                margin-bottom: 0;
            }
            
            .nav-links a {
                padding: 10px 15px;
                border-left: none;
                border-bottom: 4px solid transparent;
            }
            
            .nav-links a:hover, .nav-links a.active {
                border-left: none;
                border-bottom: 4px solid var(--primary);
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .top-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .search-bar {
                max-width: 100%;
                margin: 0;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <!-- === SIDEBAR === -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-crown"></i>
            <h1>FASHIONISTA</h1>
        </div>
        <ul class="nav-links">
            <li><a href="#"><i class="fas fa-home"></i> Tableau de bord</a></li>
            <li><a href="#" class="active"><i class="fas fa-shopping-bag"></i> Produits</a></li>
            <li><a href="#"><i class="fas fa-receipt"></i> Commandes</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Clients</a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> Analytics</a></li>
            <li><a href="#"><i class="fas fa-tags"></i> Promotions</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
        </ul>
    </div>

    <!-- === CONTENU PRINCIPAL === -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="search-bar">
                <!-- <input type="text" placeholder="Rechercher un produit..."> -->
            </div>
            <div class="user-menu">
                <div class="notifications">
                    <i class="far fa-bell"></i>
                </div>
                <div class="user-info">
                    <div class="user-avatar">FB</div>
                    <div>
                        <div class="user-name">Fashionista Boutique</div>
                        <div class="user-role">Administrateur</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu de la page produits -->
        <div class="products-content">
            <!-- En-tête de page -->
            <div class="page-header">
                <div class="page-title">
                    <h1>Ajouter un nouveau produit</h1>
                    <p>Créez une nouvelle fiche produit pour votre boutique</p>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-secondary">Enregistrer comme brouillon</button>
                    <button class="btn btn-primary" id="publishBtn">Publier le produit</button>
                </div>
            </div>

            <!-- Formulaire d'ajout de produit -->
            <div class="product-form">
                <!-- Section informations de base -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Informations de base</h3>
                    </div>

                    <div class="form-group">
                        <label for="productName">Nom du produit *</label>
                        <input type="text" id="productName" placeholder="Ex: Robe élégante été">
                    </div>

                    <div class="form-group">
                        <label for="productDescription">Description du produit *</label>
                        <textarea id="productDescription" placeholder="Décrivez votre produit en détail..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="productCategory">Catégorie *</label>
                            <select id="productCategory">
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="robes">Robes</option>
                                <option value="hauts">Hauts</option>
                                <option value="bas">Bas</option>
                                <option value="accessoires">Accessoires</option>
                                <option value="chaussures">Chaussures</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="productBrand">Marque</label>
                            <input type="text" id="productBrand" placeholder="Marque du produit">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="productPrice">Prix de vente *</label>
                            <input type="number" id="productPrice" placeholder="0.00" step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="productComparePrice">Prix comparatif</label>
                            <input type="number" id="productComparePrice" placeholder="0.00" step="0.01">
                        </div>
                    </div>

                    <!-- <div class="form-row">
                        <div class="form-group">
                            <label for="productCost">detaille sur larticle</label>
                            <input type="text" id="productCost" placeholder="0.00" step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="productSKU">SKU</label>
                            <input type="text" id="productSKU" placeholder="Code SKU">
                        </div>
                    </div> -->
                </div>

                <!-- Section images et options -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-images"></i>
                        <h3>Images du produit</h3>
                    </div>

                    <div class="image-upload" id="imageUpload">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">
                            <h4>Glissez-déposez vos images ici</h4>
                            <p>ou <span style="color: var(--primary); cursor: pointer;">parcourir vos fichiers</span></p>
                            <p>Formats supportés: JPG, PNG | Taille max: 5MB</p>
                        </div>
                    </div>

                    <div class="image-preview" id="imagePreview">
                        <!-- Les images prévisualisées apparaîtront ici -->
                    </div>

                    <div class="section-header" style="margin-top: 30px;">
                        <i class="fas fa-palette"></i>
                        <h3>Options du produit</h3>
                    </div>

                    <div class="options-list" id="optionsList">
                        <div class="option-item">
                            <div class="option-details">
                                <div class="option-color" style="background-color: #000;"></div>
                                <div>
                                    <div class="option-name">Noir</div>
                                    <div class="option-stock">Stock: 15</div>
                                </div>
                            </div>
                            <div class="option-actions">
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>

                        <div class="option-item">
                            <div class="option-details">
                                <div class="option-color" style="background-color: #fff; border: 1px solid #eee;"></div>
                                <div>
                                    <div class="option-name">Blanc</div>
                                    <div class="option-stock">Stock: 12</div>
                                </div>
                            </div>
                            <div class="option-actions">
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="add-option">
                        <input type="text" id="newOption" placeholder="Ajouter une option (couleur, taille...)">
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </div>
            </div>

            <!-- Produits récemment ajoutés -->
            <div class="recent-products">
                <div class="section-header">
                    <i class="fas fa-clock"></i>
                    <h3>Produits récemment ajoutés</h3>
                </div>

                <div class="products-grid">
                    <!-- Produit 1 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Robe estivale">
                        </div>
                        <div class="product-info">
                            <div class="product-name">Robe estivale fleurie</div>
                            <div class="product-price">$89.99</div>
                            <div class="product-meta">
                                <span>Catégorie: Robes</span>
                                <span>Stock: 24</span>
                            </div>
                            <div class="product-actions">
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-chart-bar"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Produit 2 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Collier">
                        </div>
                        <div class="product-info">
                            <div class="product-name">Collier perles dorées</div>
                            <div class="product-price">$45.50</div>
                            <div class="product-meta">
                                <span>Catégorie: Accessoires</span>
                                <span>Stock: 18</span>
                            </div>
                            <div class="product-actions">
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-chart-bar"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Produit 3 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1544441893-675973e31985?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Sac à main">
                        </div>
                        <div class="product-info">
                            <div class="product-name">Sac à main cuir noir</div>
                            <div class="product-price">$120.00</div>
                            <div class="product-meta">
                                <span>Catégorie: Accessoires</span>
                                <span>Stock: 8</span>
                            </div>
                            <div class="product-actions">
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-chart-bar"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour la gestion des produits
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des liens actifs dans la sidebar
            const navLinks = document.querySelectorAll('.nav-links a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Simulation d'upload d'images
            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');

            imageUpload.addEventListener('click', function() {
                // Dans une application réelle, on utiliserait un input file
                // Ici, nous allons simuler l'ajout d'une image
                const newImage = document.createElement('div');
                newImage.className = 'preview-item';
                newImage.innerHTML = `
                    <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Nouveau produit">
                    <div class="preview-remove"><i class="fas fa-times"></i></div>
                `;
                
                imagePreview.appendChild(newImage);
                
                // Ajouter l'événement de suppression
                const removeBtn = newImage.querySelector('.preview-remove');
                removeBtn.addEventListener('click', function() {
                    imagePreview.removeChild(newImage);
                });
            });

            // Gestion de la publication du produit
            const publishBtn = document.getElementById('publishBtn');
            publishBtn.addEventListener('click', function() {
                const productName = document.getElementById('productName').value;
                const productPrice = document.getElementById('productPrice').value;
                
                if (!productName || !productPrice) {
                    alert('Veuillez remplir les champs obligatoires (Nom et Prix)');
                    return;
                }
                
                // Simulation de publication
                alert(`Produit "${productName}" publié avec succès!`);
                
                // Réinitialiser le formulaire
                document.getElementById('productName').value = '';
                document.getElementById('productDescription').value = '';
                document.getElementById('productPrice').value = '';
                document.getElementById('productComparePrice').value = '';
                document.getElementById('productCost').value = '';
                document.getElementById('productSKU').value = '';
                document.getElementById('productBrand').value = '';
                document.getElementById('productCategory').selectedIndex = 0;
                
                // Vider les prévisualisations d'images
                imagePreview.innerHTML = '';
            });

            // Ajout d'options de produit
            const addOptionBtn = document.querySelector('.add-option .btn');
            const newOptionInput = document.getElementById('newOption');
            const optionsList = document.getElementById('optionsList');
            
            addOptionBtn.addEventListener('click', function() {
                const optionName = newOptionInput.value.trim();
                
                if (!optionName) {
                    alert('Veuillez saisir un nom d\'option');
                    return;
                }
                
                const newOption = document.createElement('div');
                newOption.className = 'option-item';
                newOption.innerHTML = `
                    <div class="option-details">
                        <div class="option-color" style="background-color: #${Math.floor(Math.random()*16777215).toString(16)};"></div>
                        <div>
                            <div class="option-name">${optionName}</div>
                            <div class="option-stock">Stock: 0</div>
                        </div>
                    </div>
                    <div class="option-actions">
                        <button><i class="fas fa-edit"></i></button>
                        <button><i class="fas fa-trash"></i></button>
                    </div>
                `;
                
                optionsList.appendChild(newOption);
                newOptionInput.value = '';
                
                // Ajouter l'événement de suppression
                const removeBtn = newOption.querySelector('.fa-trash').parentNode;
                removeBtn.addEventListener('click', function() {
                    optionsList.removeChild(newOption);
                });
            });
        });
    </script>
</body>
</html>