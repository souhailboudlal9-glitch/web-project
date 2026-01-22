<?php
/**
 * API: Inscription newsletter
 */

require_once '../config/db.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

try {
    $email = trim($_POST['email'] ?? '');
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Email invalide'], 400);
    }
    
    // Créer la table si nécessaire
    $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL UNIQUE,
        statut ENUM('Actif', 'Inactif') DEFAULT 'Actif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Vérifier si l'email existe
    if (fetchOne($pdo, "SELECT id FROM newsletter_subscribers WHERE email = ?", [$email])) {
        jsonResponse(['success' => false, 'message' => 'Email déjà inscrit'], 400);
    }
    
    executeQuery($pdo, "INSERT INTO newsletter_subscribers (email) VALUES (?)", [$email]);
    
    jsonResponse(['success' => true, 'message' => 'Inscription réussie!'], 201);
    
} catch (Exception $e) {
    error_log("Erreur newsletter.php: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur lors de l\'inscription'], 500);
}
?>
