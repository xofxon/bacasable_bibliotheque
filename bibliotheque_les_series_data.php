<?php
require_once 'bibliotheque_informationsdecons.php';
$idSerie = $_GET['id'] ?? null;
if ($idSerie) {
$srequete = <<<EOD
SELECT
   Idseries,
   Nom,
   Adresse as 'Adresse sur site',
   Commentaire as 'Commentaires',
   etat as 'Etat',
   CASE WHEN `daterecuperation` = '0000-00-00 00:00:00' THEN '' ELSE DATE_FORMAT(daterecuperation, '%d/%m/%Y %T') END as 'DateRecuperation'
   ,(select count(*) from livresparserie join livres on livresparserie.IDLivres=livres.IDLivres and livres.etat=1 where livresparserie.IDSeries=series.IDSeries) as 'Nombre livres présents'
   ,(select count(*) from livresparserie join livres on livresparserie.IDLivres=livres.IDLivres and livres.etat=0 where livresparserie.IDSeries=series.IDSeries) as 'Nombre liste de courses'
   ,nature as Genre 
FROM
   `series`
EOD;
   $srequete = $pdo->prepare($srequete." WHERE `Idseries` = ?");
   $srequete->execute([$idSerie]);
   $series = $srequete->fetch(PDO::FETCH_ASSOC);
} else {
   $srequete = <<<EOD
   SELECT
      Idseries,
      Nom,
      Adresse as 'Adresse sur site',
      Commentaire as 'Commentaires',
      CASE WHEN etat = 1 THEN 'Incomplète' ELSE 'Complète' END as 'Etat',
      CASE WHEN `daterecuperation` = '0000-00-00 00:00:00' THEN '' ELSE DATE_FORMAT(daterecuperation, '%d/%m/%Y %T') END as 'DateRecuperation'
      ,(select count(*) from livresparserie join livres on livresparserie.IDLivres=livres.IDLivres and livres.etat=1 where livresparserie.IDSeries=series.IDSeries) as 'Nombre livres présents'
      ,(select count(*) from livresparserie join livres on livresparserie.IDLivres=livres.IDLivres and livres.etat=0 where livresparserie.IDSeries=series.IDSeries) as 'Nombre liste de courses'
   
   FROM
      `series`
   ORDER BY nom;
   EOD;
   $query = $pdo->query($srequete);
   $series = $query->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($series);
?>
