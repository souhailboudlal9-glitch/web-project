<?php
/**
 * API: Récupérer les catégories et marques
 */

require_once '../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    jsonResponse([
        'success' => true,
        'data' => [
            'categories' => fetchAll($pdo, "SELECT id, nom, description FROM categories ORDER BY nom"),
            'brands' => fetchAll($pdo, "SELECT id, nom, logo_url FROM brands ORDER BY nom")
        ]
    ]);
} catch (Exception $e) {
    error_log("Erreur get_filters.php: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur lors de la récupération des filtres'], 500);
}
?>
