<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclusion de la base de données
include 'db.php';

// Récupération des données JSON du client
$data = json_decode(file_get_contents("php://input"), true);

// Préparation et exécution de la requête
$stmt = $pdo->prepare("INSERT INTO eleves (code_massar, nom, prenom, classe, prix_inscription, prix_mensuel, transport, prix_transport, date_paiement, mois)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
  $data["code_massar"],
  $data["nom"],
  $data["prenom"],
  $data["classe"],
  $data["prix_inscription"],
  $data["prix_mensuel"],
  $data["transport"],
  $data["prix_transport"],
  $data["date_paiement"],
  $data["mois"]
]);

$lastId = $pdo->lastInsertId();
echo json_encode(["success" => true, "id" => $lastId]);
?>
