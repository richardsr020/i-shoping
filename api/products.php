<?php
/**
 * API Endpoint pour les produits
 * Retourne les produits en JSON
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../app/config.php';

try {
    $db = getDB();
    
    // Récupérer les paramètres de filtrage
    $category = $_GET['category'] ?? null;
    $minPrice = $_GET['min_price'] ?? null;
    $maxPrice = $_GET['max_price'] ?? null;
    $brand = $_GET['brand'] ?? null;
    $search = $_GET['search'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = max(1, min(100, $limit));
    $offset = max(0, $offset);
    
    // Construire la requête
    $query = "
        SELECT 
            p.*,
            s.name as shop_name,
            s.id as shop_id,
            s.stars as shop_stars,
            s.currency as shop_currency,
            u.id as user_id
        FROM products p
        INNER JOIN shops s ON p.shop_id = s.id
        INNER JOIN users u ON s.user_id = u.id
        WHERE p.status = 'active'
    ";
    
    $params = [];
    
    if ($category) {
        $query .= " AND p.category = ?";
        $params[] = $category;
    }
    
    if ($minPrice !== null) {
        $query .= " AND p.price >= ?";
        $params[] = (float)$minPrice;
    }
    
    if ($maxPrice !== null) {
        $query .= " AND p.price <= ?";
        $params[] = (float)$maxPrice;
    }
    
    if ($brand) {
        $query .= " AND p.brand = ?";
        $params[] = $brand;
    }
    
    if ($search) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $query .= " ORDER BY p.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Récupérer le nombre total pour la pagination
    $countQuery = "
        SELECT COUNT(*) as total
        FROM products p
        INNER JOIN shops s ON p.shop_id = s.id
        WHERE p.status = 'active'
    ";
    
    $countParams = [];
    if ($category) {
        $countQuery .= " AND p.category = ?";
        $countParams[] = $category;
    }
    if ($minPrice !== null) {
        $countQuery .= " AND p.price >= ?";
        $countParams[] = (float)$minPrice;
    }
    if ($maxPrice !== null) {
        $countQuery .= " AND p.price <= ?";
        $countParams[] = (float)$maxPrice;
    }
    if ($brand) {
        $countQuery .= " AND p.brand = ?";
        $countParams[] = $brand;
    }
    if ($search) {
        $countQuery .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $searchTerm = "%$search%";
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
    }
    
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    
    // Récupérer les catégories uniques pour les filtres
    $categoriesStmt = $db->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Récupérer les marques uniques pour les filtres
    $brandsStmt = $db->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand");
    $brands = $brandsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'total' => (int)$total,
        'limit' => $limit,
        'offset' => $offset,
        'filters' => [
            'categories' => $categories,
            'brands' => $brands
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des produits',
        'message' => $e->getMessage()
    ]);
}



