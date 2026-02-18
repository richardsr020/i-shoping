# i-shopping - Plateforme E-commerce Multi-utilisateurs

## Description

**i-shopping** est une plateforme e-commerce multi-utilisateurs qui permet à plusieurs utilisateurs de créer des comptes, de gérer leurs propres boutiques, et d'interagir avec la communauté de la plateforme.

### Fonctionnalités principales

#### Pour les utilisateurs
- **Inscription et connexion** : Les utilisateurs peuvent créer un compte et se connecter de manière sécurisée
- **Gestion de boutiques** : Chaque utilisateur peut créer une ou plusieurs boutiques liées à son compte
- **Publication de produits** : Les propriétaires de boutiques peuvent publier des produits dans leurs boutiques
- **Consultation et recherche** : Tous les utilisateurs (avec ou sans boutique) peuvent consulter les produits de toutes les boutiques de la plateforme
- **Filtrage avancé** : Utilisation d'outils de filtrage de produits par différents critères (catégorie, prix, marque, taille, etc.)

#### Architecture de la plateforme
- **Multi-boutiques** : Un utilisateur peut avoir plusieurs boutiques
- **Communauté** : Tous les utilisateurs partagent la même plateforme et peuvent consulter tous les produits
- **Flexibilité** : Les utilisateurs peuvent avoir un compte sans boutique (pour simplement consulter et acheter) ou avec une ou plusieurs boutiques (pour vendre)

## Structure du projet

```
i-shoping/
├── index.php              # Point d'entrée unique de l'application
├── app/
│   ├── config.php         # Configuration de la plateforme (base de données, chemins, etc.)
│   ├── router.php         # Gestionnaire de routes
│   ├── controllers/       # Contrôleurs (logique métier)
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   └── ShopController.php
│   ├── models/            # Modèles (interaction avec la base de données)
│   │   ├── User.php
│   │   ├── Shop.php
│   │   └── Product.php
│   └── views/             # Vues (templates)
│       ├── header.php
│       ├── footer.php
│       ├── home.php
│       ├── login.php
│       ├── register.php
│       └── ...
├── public/                # Fichiers publics
│   ├── css/
│   ├── js/
│   └── images/
└── database/              # Scripts SQL MySQL
    ├── mysql_schema.sql
    └── export.sql         # Ancien export SQLite (archive)
```

## Technologies utilisées

- **Backend** : PHP (code natif)
- **Frontend** : HTML5, CSS3, JavaScript (vanilla)
- **Base de données** : MySQL (PDO)
- **Architecture** : MVC (Model-View-Controller)

## Installation

1. **Prérequis**
   - PHP 7.4 ou supérieur avec extension `pdo_mysql`
   - Serveur web (Apache/Nginx) ou serveur PHP intégré

2. **Configuration**
   - Le fichier `app/config.php` contient toutes les configurations de la plateforme
   - Configurez les constantes MySQL dans `app/config.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`)
   - Le schéma `database/mysql_schema.sql` est appliqué automatiquement si des tables manquent

3. **Lancement**
   ```bash
   # Avec le serveur PHP intégré
   php -S localhost:8000
   
   # Ou configurez votre serveur web pour pointer vers le dossier du projet
   ```

4. **Accès**
   - Ouvrez votre navigateur et accédez à `http://localhost:8000`
   - Toutes les pages sont accessibles uniquement via `index.php?page=nom_page`

## Système de routage

**Important** : Toutes les pages doivent être accessibles uniquement via `index.php`. Aucune page ne doit être accessible directement.

### Format des URLs
```
http://localhost:8000/index.php?page=nom_page
```

### Pages disponibles
- `index.php?page=home` - Page d'accueil
- `index.php?page=login` - Page de connexion
- `index.php?page=register` - Page d'inscription
- `index.php?page=create_shop` - Créer une boutique (nécessite une connexion)
- `index.php?page=dashboard_shop` - Tableau de bord boutique
- `index.php?page=create_product` - Créer un produit (nécessite une boutique)
- etc.

## Base de données

La base de données MySQL contient les tables suivantes :

- **users** : Informations des utilisateurs
- **shops** : Informations des boutiques (liées aux utilisateurs)
- **products** : Produits (liés aux boutiques)

## Sécurité

- Les mots de passe sont hashés avec `password_hash()` PHP
- Les sessions PHP sont utilisées pour la gestion de l'authentification
- Protection contre les injections SQL via les requêtes préparées
- Validation des données côté serveur et côté client

## Fonctionnalités à venir

- Système de panier d'achat
- Système de commandes
- Messagerie entre utilisateurs
- Système d'évaluation et de commentaires
- Tableau de bord administrateur avancé

## Auteur

Développé pour i-shopping - Plateforme e-commerce multi-utilisateurs
