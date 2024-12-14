<?php
require_once 'bibliotheque_informationsDeCons.php';
require_once 'vendor/autoload.php'; // si vous utilisez une bibliothèque comme TCPDF

// Requête pour récupérer les séries
$query = $pdo->query("SELECT * FROM series");
$series = $query->fetchAll(PDO::FETCH_ASSOC);

// Créer le PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Titre
$pdf->Cell(0, 10, 'Liste des Séries', 0, 1, 'C');

// Table
$html = '<table border="1"><thead><tr><th>ID</th><th>Nom</th><th>Adresse</th></tr></thead><tbody>';
foreach ($series as $serie) {
    $html .= '<tr><td>' . $serie['IDSeries'] . '</td><td>' . $serie['Nom'] . '</td><td>' . $serie['Adresse'] . '</td></tr>';
}
$html .= '</tbody></table>';

$pdf->writeHTML($html);
$filename = date('Y-m-d_H-i-s') . "_ListeDesSeries.pdf";
$pdf->Output($filename, 'D'); // Télécharge directement le fichier

?>
<script src="bibliotheque_Js/bibliotheque_Serie_Impression_de_la_liste.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_bibliothèque.js?v=<?= time(); ?>"></script>
