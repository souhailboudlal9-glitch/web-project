<?php
/**
 * Configuration de la connexion à la base de données
 * Utilise PDO pour une meilleure sécurité et flexibilité
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'location_voiture');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Options PDO pour une meilleure sécurité et gestion des erreurs
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
];

// Création de la connexion PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // En production, ne jamais afficher les détails de l'erreur
    // Utiliser un système de logging à la place
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
    // Pour le développement:
    // die("Erreur de connexion: " . $e->getMessage());
}

/**
 * Fonction helper pour exécuter des requêtes préparées
 * 
 * @param PDO $pdo Instance PDO
 * @param string $sql Requête SQL avec placeholders
 * @param array $params Paramètres à binder
 * @return PDOStatement
 */
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Erreur SQL: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Fonction pour obtenir une seule ligne
 * 
 * @param PDO $pdo Instance PDO
 * @param string $sql Requête SQL
 * @param array $params Paramètres
 * @return array|false
 */
function fetchOne($pdo, $sql, $params = []) {
    $stmt = executeQuery($pdo, $sql, $params);
    return $stmt->fetch();
}

/**
 * Fonction pour obtenir toutes les lignes
 * 
 * @param PDO $pdo Instance PDO
 * @param string $sql Requête SQL
 * @param array $params Paramètres
 * @return array
 */
function fetchAll($pdo, $sql, $params = []) {
    $stmt = executeQuery($pdo, $sql, $params);
    return $stmt->fetchAll();
}

/**
 * Fonction pour échapper les sorties HTML (prévention XSS)
 * 
 * @param string $string Chaîne à échapper
 * @return string
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour générer une réponse JSON
 * 
 * @param mixed $data Données à retourner
 * @param int $statusCode Code HTTP
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Configuration du fuseau horaire
date_default_timezone_set('Africa/Casablanca');

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
