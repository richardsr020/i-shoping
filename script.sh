#!/bin/bash

# Script de génération d'arborescence MVC simple pour e-commerce
# Création : 2024

echo "=== Création de l'arborescence e-commerce simple ==="

# Dossiers principaux
mkdir -p app/controllers
mkdir -p app/models  
mkdir -p app/views
mkdir -p public/css
mkdir -p public/js
mkdir -p public/images
mkdir -p public/uploads

# Création des fichiers vides
touch public/index.php
touch app/config.php
touch app/router.php

# Contrôleurs
touch app/controllers/AuthController.php
touch app/controllers/ShopController.php
touch app/controllers/ProductController.php
touch app/controllers/HomeController.php

# Modèles
touch app/models/User.php
touch app/models/Shop.php
touch app/models/Product.php

# Vues
touch app/views/header.php
touch app/views/footer.php
touch app/views/home.php
touch app/views/login.php
touch app/views/register.php
touch app/views/create_shop.php
touch app/views/create_product.php
touch app/views/my_products.php

# CSS et JS
touch public/css/style.css
touch public/js/app.js

# Fichier de routing
touch .htaccess

echo "=== Arborescence créée avec succès ==="
echo "Structure :"
echo "├── app/"
echo "│   ├── controllers/"
echo "│   │   ├── AuthController.php"
echo "│   │   ├── ShopController.php" 
echo "│   │   ├── ProductController.php"
echo "│   │   └── HomeController.php"
echo "│   ├── models/"
echo "│   │   ├── User.php"
echo "│   │   ├── Shop.php"
echo "│   │   └── Product.php"
echo "│   ├── views/"
echo "│   │   ├── header.php"
echo "│   │   ├── footer.php"
echo "│   │   ├── home.php"
echo "│   │   ├── login.php"
echo "│   │   ├── register.php"
echo "│   │   ├── create_shop.php"
echo "│   │   ├── create_product.php"
echo "│   │   └── my_products.php"
echo "│   ├── config.php"
echo "│   └── router.php"
echo "├── public/"
echo "│   ├── index.php"
echo "│   ├── css/style.css"
echo "│   ├── js/app.js"
echo "│   ├── images/"
echo "│   └── uploads/"
echo "└── .htaccess"
