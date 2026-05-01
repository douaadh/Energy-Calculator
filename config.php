<?php
// ============================================
// Configuration de la base de données
// MODIFIEZ CES PARAMÈTRES SELON VOTRE ENVIRONNEMENT
// ============================================

define('DB_HOST', 'localhost');     // Serveur MySQL (généralement localhost)
define('DB_USER', 'root');          // Nom d'utilisateur MySQL
define('DB_PASS', '');              // Mot de passe MySQL (laisser vide sous XAMPP/WAMP)
define('DB_NAME', 'tp_batiment');   // Nom de la base de données

// Démarrage de session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// Fonctions utilitaires
// ============================================

// Connexion à la base de données
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("❌ Erreur de connexion: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Rediriger vers login si non connecté
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Obtenir l'utilisateur connecté
function getCurrentUser() {
    return $_SESSION['username'] ?? null;
}

// Obtenir l'ID de l'utilisateur connecté
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}
?>