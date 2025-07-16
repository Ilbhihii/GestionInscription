<?php
require 'vendor/autoload.php';
require 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Vérification du paramètre
if (!isset($_GET['code_massar'])) {
    die("Erreur : 'code_massar' manquant dans l'URL.");
}
$code = $_GET['code_massar'];

// Récupérer l'élève
$stmt = $pdo->prepare("SELECT * FROM eleves WHERE code_massar = ?");
$stmt->execute([$code]);
$eleve = $stmt->fetch();

if (!$eleve) {
    die("Erreur : Élève non trouvé.");
}

// Préparer le fichier Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Attestation Reçu");

// Ajouter le logo
$drawing = new Drawing();
$drawing->setName('Logo SUP MTI');
$drawing->setDescription('Logo SUP MTI');
//$drawing->setPath(__DIR__ . 'C:\Users\user\Downloads\supmti-logo-reconnu-01.png'); // Assure-toi que ce chemin est correct
$drawing->setHeight(100);
$drawing->setCoordinates('A1');
$drawing->setWorksheet($sheet);

// Définir les styles
$styleTitre = [
    'font' => ['bold' => true, 'size' => 14],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
];

$styleContenu = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
];

// Contenu du reçu pour la direction
$sheet->mergeCells('A7:B7');
$sheet->setCellValue('A7', 'REÇU POUR LA DIRECTION');
$sheet->getStyle('A7')->applyFromArray($styleTitre);

$sheet->setCellValue('A8', 'Nom de l’élève');
$sheet->setCellValue('B8', $eleve['nom'] . ' ' . $eleve['prenom']);
$sheet->setCellValue('A9', 'Classe');
$sheet->setCellValue('B9', $eleve['classe']);
$sheet->setCellValue('A10', 'Date de paiement');
$sheet->setCellValue('B10', $eleve['date_paiement']);

$total = $eleve['prix_inscription'] + $eleve['prix_mensuel'] + ($eleve['transport'] ? $eleve['prix_transport'] : 0);
$sheet->setCellValue('A11', 'Montant Total');
$sheet->setCellValue('B11', $total . ' DH');
$sheet->getStyle('A8:B11')->applyFromArray($styleContenu);

// Espacement pour le 2nd reçu
$sheet->mergeCells('A13:B13');
$sheet->setCellValue('A13', 'REÇU POUR LE CLIENT');
$sheet->getStyle('A13')->applyFromArray($styleTitre);

// Contenu du reçu pour le client
$sheet->setCellValue('A14', 'Nom de l’élève');
$sheet->setCellValue('B14', $eleve['nom'] . ' ' . $eleve['prenom']);
$sheet->setCellValue('A15', 'Classe');
$sheet->setCellValue('B15', $eleve['classe']);
$sheet->setCellValue('A16', 'Date de paiement');
$sheet->setCellValue('B16', $eleve['date_paiement']);
$sheet->setCellValue('A17', 'Montant Total');
$sheet->setCellValue('B17', $total . ' DH');
$sheet->getStyle('A14:B17')->applyFromArray($styleContenu);

// Centrer globalement les titres
$sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A7:B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A13:B13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Télécharger le fichier Excel
$filename = "Recu_paiement_" . $eleve['code_massar'] . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
