<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Gérer les requêtes preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$code = $_GET['code_massar'];

try {
    // Remplace 'nom_de_ta_base' par le vrai nom de ta base de données
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_ecole;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM eleves WHERE code_massar = ?");
    $stmt->execute([$code]);
    $eleve = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eleve) {
        echo json_encode($eleve);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Élève non trouvé"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur : " . $e->getMessage()]);
}
