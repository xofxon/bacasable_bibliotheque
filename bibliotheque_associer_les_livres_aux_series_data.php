<?php
require_once 'bibliotheque_informationsdecons.php';
$idSerie = $_GET['id'] ?? null;
if ($idSerie) {
$srequete= <<< EOD
SELECT `livresparserie`.idlivreparserie  
,`livresparserie`.`IDLivres`
,`livresparserie`.`IDSeries`
,livres.Titre
,series.Nom
,livres.Etat
,livres.genre
FROM `livresparserie` 
join livres on livres.IDLivres = livresparserie.IDLivres
join series on series.IDSeries = livresparserie.IDSeries
EOD;
$srequete .= " WHERE series.IDSeries=$idSerie";
$srequete .= " ORDER BY livres.titre;";

$query = $pdo->query($srequete);
$livres = $query->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($livres);
?>
