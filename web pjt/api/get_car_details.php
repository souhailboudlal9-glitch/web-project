<?php
/**
 * API: Récupérer les détails d'une voiture
 */

require_once '../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    if (empty($_GET['id'])) {
        jsonResponse(['success' => false, 'message' => 'ID de voiture manquant'], 400);
    }
    
    $carId = intval($_GET['id']);
    
    // Détails de la voiture
    $car = fetchOne($pdo, 
        "SELECT c.*, b.nom AS marque, b.id AS brand_id, b.logo_url AS marque_logo,
                cat.nom AS categorie, cat.id AS category_id, cat.description AS categorie_description
         FROM cars c
         JOIN brands b ON c.brand_id = b.id
         JOIN categories cat ON c.category_id = cat.id
         WHERE c.id = ?", 
        [$carId]
    );
    
    if (!$car) {
        jsonResponse(['success' => false, 'message' => 'Voiture non trouvée'], 404);
    }
    
    // Voitures similaires
    $similarCars = fetchAll($pdo,
        "SELECT c.id, c.nom, c.image_url, c.prix_jour_dh, c.prix_jour_eur, c.prix_jour_usd, 
                c.transmission, c.carburant, b.nom AS marque
         FROM cars c
         JOIN brands b ON c.brand_id = b.id
         WHERE c.category_id = ? AND c.id != ? AND c.disponible = TRUE
         ORDER BY c.featured DESC, RAND() LIMIT 3",
        [$car['category_id'], $carId]
    );
    
    jsonResponse(['success' => true, 'data' => $car, 'similar' => $similarCars]);
    
} catch (Exception $e) {
    error_log("Erreur get_car_details.php: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur lors de la récupération des détails'], 500);
}
?>
