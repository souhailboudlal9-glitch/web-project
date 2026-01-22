<?php
/**
 * API: Formulaire de contact
 */

require_once '../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

try {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (!$nom || !$email || !$telephone || !$message) {
        jsonResponse(['success' => false, 'message' => 'Tous les champs sont requis'], 400);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Adresse email invalide'], 400);
    }
    
    // Créer la table si nécessaire
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        telephone VARCHAR(20) NOT NULL,
        message TEXT NOT NULL,
        statut ENUM('Non lu', 'Lu', 'Traité') DEFAULT 'Non lu',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Insérer le message
    executeQuery($pdo, 
        "INSERT INTO contact_messages (nom, email, telephone, message) VALUES (?, ?, ?, ?)",
        [$nom, $email, $telephone, $message]
    );
    
    jsonResponse(['success' => true, 'message' => 'Message envoyé avec succès!'], 201);
    
} catch (Exception $e) {
    error_log("Erreur contact.php: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur lors de l\'envoi du message'], 500);
}
?>
