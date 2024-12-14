<?php
require_once 'bibliotheque_informationsdecons.php';
$idorigine = $_GET['id'] ?? null;
if ($idorigine) {
   $stexterequete = <<<EOD
    SELECT 
    IDOrigines
    ,Nom
    ,Adresse
    ,CodeInterne
    ,CASE WHEN Etat = 0 THEN 'Actif' ELSE 'Inactif' END as 'Etat'
    ,CASE WHEN Gestion = 1 THEN 'Interne' ELSE 'Utilisateur' END as 'Gestion'
     FROM `origines`
EOD;

   $srequete = $pdo->prepare($stexterequete." WHERE `idorigines` = ?");
    $srequete->execute([$idorigine]);
    $origine = $srequete->fetch(PDO::FETCH_ASSOC);

    echo json_encode($origine);
    exit;
}
$srequete = <<<EOD
    SELECT 
    IDOrigines
    ,Nom
    ,Adresse
    ,CodeInterne
    ,CASE WHEN Etat = 0 THEN 'Actif' ELSE 'Inactif' END as 'Etat'
    ,CASE WHEN Gestion = 1 THEN 'Interne' ELSE 'Utilisateur' END as 'Gestion'
     FROM `origines`
EOD;
$srequete .= " ORDER BY Nom;";
$query = $pdo->query($srequete);
$origines = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($origines);
?>
