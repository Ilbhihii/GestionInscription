<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'db.php';

//  Vérifier si "code_massar" est envoyé
$codeMassar = $_GET['code_massar'] ?? '';
if (empty($codeMassar)) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre code_massar manquant']);
    exit;
}

//  Rechercher un élève par code Massar
$stmt = $pdo->prepare("
    SELECT code_massar, nom, prenom, classe,prix_mensuel, transport, prix_transport
    FROM eleves
    WHERE code_massar = ?
");
$stmt->execute([$codeMassar]);


$eleve = $stmt->fetch(PDO::FETCH_ASSOC);

if ($eleve) {
    echo json_encode($eleve);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Élève non trouvé']);
}
