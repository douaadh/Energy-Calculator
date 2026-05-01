<?php
// ============================================
// Script d'installation automatique
// Exécutez ce fichier une seule fois
// ============================================

require_once 'config.php';

echo "<!DOCTYPE html><html><head><title>Installation</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f0f0f0;}</style></head><body>";
echo "<h1>🔧 Installation d'Energy Calculator</h1>";

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Erreur de connexion: " . $conn->connect_error);
    }
    
    // Supprimer et recréer la base
    $conn->query("DROP DATABASE IF EXISTS tp_batiment");
    echo "<p>✅ Base existante supprimée</p>";
    
    $conn->query("CREATE DATABASE tp_batiment");
    echo "<p>✅ Base 'tp_batiment' créée</p>";
    
    $conn->select_db(DB_NAME);
    
    // Créer table users
    $conn->query("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        full_name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )");
    echo "<p>✅ Table 'users' créée</p>";
    
    // Créer table historique
    $conn->query("CREATE TABLE historique (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_batiment VARCHAR(100) NOT NULL,
        surface_batiment FLOAT NOT NULL,
        deperdition FLOAT NOT NULL,
        consommation FLOAT NOT NULL,
        classe VARCHAR(2) NOT NULL,
        date_calcul TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "<p>✅ Table 'historique' créée</p>";
    
    // Insérer utilisateurs
    $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $user_hash = password_hash('user123', PASSWORD_DEFAULT);
    
    $conn->query("INSERT INTO users (username, password, email, full_name) VALUES 
        ('admin', '$admin_hash', 'admin@energy-calculator.com', 'Administrateur'),
        ('user', '$user_hash', 'user@energy-calculator.com', 'Utilisateur')");
    echo "<p>✅ Utilisateurs créés (admin/admin123, user/user123)</p>";
    
    // Données démo
    $conn->query("INSERT INTO historique (nom_batiment, surface_batiment, deperdition, consommation, classe, user_id) VALUES
        ('Maison Eco', 120, 4500, 37.5, 'A', 1),
        ('Appartement Central', 85, 6800, 80, 'B', 1),
        ('Bureau Moderne', 200, 22000, 110, 'C', 1),
        ('Ancienne Maison', 95, 15675, 165, 'D', 2),
        ('Immeuble Ancien', 300, 75000, 250, 'E', 2),
        ('Entrepôt', 500, 165000, 330, 'F', 1),
        ('Usine', 1000, 450000, 450, 'G', 2)");
    echo "<p>✅ Données de démonstration ajoutées</p>";
    
    echo "<hr>";
    echo "<h2 style='color:green'>✅ Installation terminée avec succès !</h2>";
    echo "<p>👑 Compte Admin: <strong>admin</strong> / <strong>admin123</strong></p>";
    echo "<p>👤 Compte User: <strong>user</strong> / <strong>user123</strong></p>";
    echo "<br><a href='login.php' style='background:#2ecc71;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🚀 Aller à l'application</a>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Erreur d'installation: " . $e->getMessage() . "</h2>";
    echo "<p>Vérifiez que MySQL est démarré et que config.php est correct</p>";
}

echo "</body></html>";
?>