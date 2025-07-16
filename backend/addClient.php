<?php
// 🛡️ Headers CORS à ajouter impérativement en haut
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// ⚠️ Gérer les pré-requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclusion de la base de données
include 'db.php';

// Récupération des données JSON du client
$data = json_decode(file_get_contents("php://input"), true);

// Préparation et exécution de la requête
$stmt = $pdo->prepare("INSERT INTO eleves (code_massar, nom, prenom, classe, parent_nom, parent_prenom, prix_inscription, prix_mensuel, transport, prix_transport, date_paiement)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
  $data["code_massar"],
  $data["nom"],
  $data["prenom"],
  $data["classe"],
  $data["parent_nom"],
  $data["parent_prenom"],
  $data["prix_inscription"],
  $data["prix_mensuel"],
  $data["transport"],
  $data["prix_transport"],
  $data["date_paiement"]
]);

$lastId = $pdo->lastInsertId();
echo json_encode(["success" => true, "id" => $lastId]);
?>
