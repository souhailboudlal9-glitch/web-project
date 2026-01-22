<?php
/**
 * API: Récupérer la liste des voitures
 */

require_once '../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    // Paramètres de filtrage
    $category = $_GET['category'] ?? null;
    $brand = $_GET['brand'] ?? null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;
    $featured = isset($_GET['featured']) ? filter_var($_GET['featured'], FILTER_VALIDATE_BOOLEAN) : null;
    $limit = intval($_GET['limit'] ?? 50);
    $offset = intval($_GET['offset'] ?? 0);
    
    // Construction de la requête
    $baseQuery = "FROM cars c
                  JOIN brands b ON c.brand_id = b.id
                  JOIN categories cat ON c.category_id = cat.id
                  WHERE c.disponible = TRUE";
    
    $params = [];
    $conditions = [];
    
    if ($category) {
        $conditions[] = "c.category_id = ?";
        $params[] = intval($category);
    }
    if ($brand) {
        $conditions[] = "c.brand_id = ?";
        $params[] = intval($brand);
    }
    if ($search) {
        $conditions[] = "(c.nom LIKE ? OR b.nom LIKE ? OR cat.nom LIKE ?)";
        $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
    }
    if ($featured !== null) {
        $conditions[] = "c.featured = ?";
        $params[] = $featured ? 1 : 0;
    }
    
    if ($conditions) {
        $baseQuery .= " AND " . implode(" AND ", $conditions);
    }
    
    // Récupérer les voitures
    $sql = "SELECT c.*, b.nom AS marque, b.id AS brand_id, cat.nom AS categorie, cat.id AS category_id
            $baseQuery ORDER BY c.featured DESC, c.created_at DESC LIMIT ? OFFSET ?";
    
    $cars = fetchAll($pdo, $sql, array_merge($params, [$limit, $offset]));
    
    // Compter le total
    $total = fetchOne($pdo, "SELECT COUNT(*) as total $baseQuery", $params)['total'];
    
    jsonResponse([
        'success' => true,
        'data' => $cars,
        'pagination' => compact('total', 'limit', 'offset') + ['count' => count($cars)]
    ]);
    
} catch (Exception $e) {
    error_log("Erreur get_cars.php: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur lors de la récupération des voitures'], 500);
}
?>
