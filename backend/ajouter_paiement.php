<?php
// ✅ Autoriser les requêtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code_massar'];
$mois = $data['mois'];

// ✅ Récupérer prix_mensuel et prix_transport de l'élève
$stmt = $pdo->prepare("SELECT prix_mensuel, prix_transport FROM eleves WHERE code_massar = ?");
$stmt->execute([$code]);
$eleve = $stmt->fetch();

if (!$eleve) {
    http_response_code(404);
    echo json_encode(['error' => 'Élève non trouvé']);
    exit;
}

// ✅ Calcul du montant total
$montant = $eleve['prix_mensuel'] + $eleve['prix_transport'];

// ✅ Insertion du paiement
$stmt = $pdo->prepare("INSERT INTO paiements (code_massar, mois, montant, date_paiement)
    VALUES (?, ?, ?, CURRENT_DATE)");
$stmt->execute([$code, $mois, $montant]);

echo json_encode(['message' => 'Paiement enregistré avec succès']);
