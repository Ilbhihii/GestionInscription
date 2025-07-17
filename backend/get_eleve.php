<?php
// ✅ Autoriser les requêtes depuis le frontend (React ou autre)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// ✅ Gérer les requêtes préflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'db.php';

// ✅ Vérification du paramètre
$code = $_GET['code_massar'] ?? '';
if (empty($code)) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre code_massar manquant']);
    exit;
}

// ✅ Requête préparée
$stmt = $pdo->prepare("SELECT nom, prenom, classe, prix_mensuel FROM eleves WHERE code_massar = ?");
$stmt->execute([$code]);
$eleve = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Réponse
if ($eleve) {
    echo json_encode($eleve);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Élève introuvable']);
}
