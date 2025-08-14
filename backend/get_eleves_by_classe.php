<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include 'db.php';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données.']);
    exit;
}

// Vérification du paramètre "code_massar"
if (!isset($_GET['code_massar']) || empty($_GET['code_massar'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre "code_massar" manquant.']);
    exit;
}

$codeMassar = $_GET['code_massar'];

$sql = "
    SELECT
        p.*,
        e.nom,
        e.prenom,
        e.prix_inscription,
        e.prix_mensuel,
        e.transport,
        e.prix_transport
    FROM
        paiements p
    JOIN
        eleves e ON
        p.code_massar = e.code_massar
    WHERE
        p.code_massar = ?
    ORDER BY
        p.mois
";
$sql = "SELECT * FROM vue_classe WHERE code_massar = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$codeMassar]);
$result = $stmt->fetchAll();

echo json_encode($result);
