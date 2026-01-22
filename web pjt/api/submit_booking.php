<?php
/**
 * API: Soumettre une réservation
 */

require_once '../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation des champs requis
    $required = ['nom_client', 'email_client', 'telephone_client', 'lieu_prise', 
                 'date_prise', 'heure_prise', 'date_retour', 'heure_retour', 'devise', 'mode_paiement'];
    
    foreach ($required as $field) {
        if (empty($input[$field])) {
            jsonResponse(['success' => false, 'message' => "Le champ '$field' est requis"], 400);
        }
    }
    
    // Extraire les données
    $carId = !empty($input['car_id']) ? intval($input['car_id']) : null;
    $devise = strtoupper(trim($input['devise']));
    
    // Validations
    if (!filter_var($input['email_client'], FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Email invalide'], 400);
    }
    
    if (!in_array($devise, ['DH', 'EUR', 'USD'])) {
        jsonResponse(['success' => false, 'message' => 'Devise invalide'], 400);
    }
    
    // Vérifier la voiture si sélectionnée
    $car = null;
    if ($carId) {
        $car = fetchOne($pdo, "SELECT id, nom, prix_jour_dh, prix_jour_eur, prix_jour_usd, disponible FROM cars WHERE id = ?", [$carId]);
        if (!$car) jsonResponse(['success' => false, 'message' => 'Voiture non trouvée'], 404);
        if (!$car['disponible']) jsonResponse(['success' => false, 'message' => 'Voiture non disponible'], 400);
    }
    
    // Calculer les jours et le prix
    $nombreJours = (new DateTime($input['date_retour']))->diff(new DateTime($input['date_prise']))->days;
    if ($nombreJours < 1) {
        jsonResponse(['success' => false, 'message' => 'La date de retour doit être après la date de prise'], 400);
    }
    
    $prixTotal = $car ? $car["prix_jour_" . strtolower($devise)] * $nombreJours : 0;
    
    // Extraire mode de paiement et chauffeur privé
    $modePaiement = in_array($input['mode_paiement'], ['sur_place', 'carte']) ? $input['mode_paiement'] : 'sur_place';
    $chauffeurPrive = isset($input['chauffeur_prive']) && $input['chauffeur_prive'] == '1' ? 1 : 0;
    
    // Insérer la réservation
    executeQuery($pdo,
        "INSERT INTO bookings (car_id, nom_client, email_client, telephone_client, lieu_prise, 
         date_prise, heure_prise, date_retour, heure_retour, nombre_jours, prix_total_dh, devise, mode_paiement, chauffeur_prive, message, statut)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'En attente')",
        [$carId, trim($input['nom_client']), trim($input['email_client']), trim($input['telephone_client']),
         trim($input['lieu_prise']), $input['date_prise'], $input['heure_prise'], 
         $input['date_retour'], $input['heure_retour'], $nombreJours, $prixTotal, $devise, 
         $modePaiement, $chauffeurPrive, trim($input['message'] ?? '')]
    );
    
    jsonResponse([
        'success' => true,
        'message' => 'Réservation enregistrée avec succès!',
        'data' => [
            'booking_id' => $pdo->lastInsertId(),
            'car_name' => $car ? $car['nom'] : 'À déterminer',
            'nombre_jours' => $nombreJours,
            'prix_total' => $prixTotal,
            'devise' => $devise
        ]
    ], 201);
    
} catch (Exception $e) {
    error_log("Erreur submit_booking.php: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur lors de la réservation'], 500);
}
?>
