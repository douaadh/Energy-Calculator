<?php
require_once 'config.php';
requireLogin();

$nom = $_POST['nom'] ?? '';
$surface = floatval($_POST['surface'] ?? 0);
$dep = floatval($_POST['deperdition'] ?? 0);
$conso = floatval($_POST['consommation'] ?? 0);
$classe = $_POST['classe'] ?? '';

if (empty($nom) || $surface <= 0) {
    http_response_code(400);
    echo "Erreur: Données invalides";
    exit();
}

$conn = getDbConnection();
$user_id = getCurrentUserId();

$stmt = $conn->prepare("INSERT INTO historique (nom_batiment, surface_batiment, deperdition, consommation, classe, user_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdddsi", $nom, $surface, $dep, $conso, $classe, $user_id);

if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Erreur: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>