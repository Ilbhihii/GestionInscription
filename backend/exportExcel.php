<?php
require 'vendor/autoload.php';
require 'db.php';
require_once __DIR__ . '/tcpdf/tcpdf.php';


// Vérification
if (!isset($_GET['code_massar'])) {
    die("Erreur : 'code_massar' manquant dans l'URL.");
}
$code = $_GET['code_massar'];

// Récupération de l'élève et du dernier paiement
$stmt = $pdo->prepare("
    SELECT e.nom, e.prenom, e.classe, e.code_massar,
           e.prix_mensuel, e.prix_transport, e.date_paiement
    FROM eleves e
    LEFT JOIN paiements p ON p.code_massar = e.code_massar
    WHERE e.code_massar = ?
    ORDER BY p.date_paiement DESC
    LIMIT 1
");

$stmt->execute([$code]);
$data = $stmt->fetch();

if (!$data) {
    die("Erreur : Élève ou paiement non trouvé.");
}

// Calcul du montant total
$total = $data['prix_mensuel'] + $data['prix_transport'];

// Création du PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SUP MTI');
$pdf->SetTitle('Reçu Paiement');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage('P', 'A4');

// Logo
$logoPath = __DIR__ . '/images/supmti-logo-reconnu-01.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 10, 40); // X, Y, Taille
    $pdf->Ln(25); // espace après le logo
}

// Titre principal
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'REÇU DE PAIEMENT', 0, 1, 'C');
$pdf->Ln(5);

// Contenu HTML (double reçu)
$html = '
<style>
table {
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
}
td, th {
    border: 1px solid #000;
    padding: 6px;
}
th {
    background-color: #f2f2f2;
    text-align: center;
}
.titre {
    font-weight: bold;
    text-align: center;
    font-size: 14px;
}
</style>

<p class="titre">REÇU POUR LA DIRECTION</p>
<table>
    <tr><th>Nom de l\'élève</th><td>'.$data['nom'].' '.$data['prenom'].'</td></tr>
    <tr><th>Classe</th><td>'.$data['classe'].'</td></tr>
    <tr><th>Date de paiement</th><td>'.$data['date_paiement'].'</td></tr>
    <tr><th>Montant Total</th><td>'.$total.' DH</td></tr>
</table>
<br><br>
<p class="titre">REÇU POUR LE CLIENT</p>
<table>
    <tr><th>Nom de l\'élève</th><td>'.$data['nom'].' '.$data['prenom'].'</td></tr>
    <tr><th>Classe</th><td>'.$data['classe'].'</td></tr>
    <tr><th>Date de paiement</th><td>'.$data['date_paiement'].'</td></tr>
    <tr><th>Montant Total</th><td>'.$total.' DH</td></tr>
</table>
';

$pdf->SetFont('helvetica', '', 11);
$pdf->writeHTML($html, true, false, true, false, '');

// Sortie du PDF
$pdf->Output("Recu_paiement_{$data['code_massar']}.pdf", 'I');
