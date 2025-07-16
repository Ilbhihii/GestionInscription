<?php
// Autoriser les requêtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// Gérer les requêtes préflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'db.php';

// Vérifier la classe en paramètre
$classe = $_GET['classe'] ?? '';
if (empty($classe)) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre classe manquant']);
    exit;
}

// Récupérer tous les élèves de cette classe
$stmt = $pdo->prepare("SELECT code_massar, nom, prenom, classe, prix_mensuel, prix_transport, transport FROM eleves WHERE classe = ?");
$stmt->execute([$classe]);
$eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque élève, récupérer ses paiements
foreach ($eleves as &$eleve) {
    $stmt2 = $pdo->prepare("SELECT mois, montant, date_paiement FROM paiements WHERE code_massar = ? ORDER BY date_paiement DESC");
    $stmt2->execute([$eleve['code_massar']]);
    $eleve['paiements'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

// Retourner la réponse JSON
header("Content-Type: application/json");
echo json_encode($eleves);
