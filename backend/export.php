<?php
require 'vendor/autoload.php';
require 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Vérification
if (!isset($_GET['code_massar'])) {
    die("Erreur : 'code_massar' manquant dans l'URL.");
}
$code = $_GET['code_massar'];

// Récupération de l'élève et du dernier paiement
$stmt = $pdo->prepare("
    SELECT e.nom, e.prenom, e.classe, e.code_massar,
           e.prix_mensuel, e.prix_transport,
           p.date_paiement
    FROM paiements p
    JOIN eleves e ON p.code_massar = e.code_massar
    WHERE p.code_massar = ?
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

// Création du fichier Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Reçu Paiement");

// Logo
$logoPath = __DIR__ . '/images/supmti-logo-reconnu-01.png';
if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setName('Logo SUP MTI');
    $drawing->setDescription('Logo SUP MTI');
    $drawing->setPath($logoPath);
    $drawing->setHeight(100);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($sheet);
}

// Styles
$styleTitre = [
    'font' => ['bold' => true, 'size' => 14],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
];
$styleContenu = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
];

// Reçu Direction
$sheet->mergeCells('A7:B7');
$sheet->setCellValue('A7', 'REÇU POUR LA DIRECTION');
$sheet->getStyle('A7')->applyFromArray($styleTitre);

$sheet->setCellValue('A8', 'Nom de l’élève');
$sheet->setCellValue('B8', $data['nom'] . ' ' . $data['prenom']);
$sheet->setCellValue('A9', 'Classe');
$sheet->setCellValue('B9', $data['classe']);
$sheet->setCellValue('A10', 'Date de paiement');
$sheet->setCellValue('B10', $data['date_paiement']);
$sheet->setCellValue('A11', 'Montant Total');
$sheet->setCellValue('B11', $total . ' DH');
$sheet->getStyle('A8:B11')->applyFromArray($styleContenu);

// Reçu Client
$sheet->mergeCells('A13:B13');
$sheet->setCellValue('A13', 'REÇU POUR LE CLIENT');
$sheet->getStyle('A13')->applyFromArray($styleTitre);

$sheet->setCellValue('A14', 'Nom de l’élève');
$sheet->setCellValue('B14', $data['nom'] . ' ' . $data['prenom']);
$sheet->setCellValue('A15', 'Classe');
$sheet->setCellValue('B15', $data['classe']);
$sheet->setCellValue('A16', 'Date de paiement');
$sheet->setCellValue('B16', $data['date_paiement']);
$sheet->setCellValue('A17', 'Montant Total');
$sheet->setCellValue('B17', $total . ' DH');
$sheet->getStyle('A14:B17')->applyFromArray($styleContenu);

// Centrage des titres
$sheet->getStyle('A7:B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A13:B13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Télécharger
$filename = "Recu_paiement_" . $data['code_massar'] . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
