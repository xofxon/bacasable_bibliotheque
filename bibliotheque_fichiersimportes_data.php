<?php
// Connexion à la base de données
require_once 'bibliotheque_informationsdecons.php';

// Récupération des paramètres
$perimetre = $_GET['perimetre'] ?? '';
$idFichierImporte = $_GET['IDFichierImporte'] ?? null;

if ($perimetre === 'liste') {
    // Requête pour récupérer la liste des fichiers importés
    $requete = <<<EOD
    SELECT 
    IdFichierImporte, 
    NomDuFichier, 
    Dateimportation, 
    Caracteristiques,
    CASE WHEN Typedefichier = 1 THEN 'CSV' ELSE 'Autre' END AS Typedefichier
    FROM fichiersimportes
    EOD;

    $requete = $pdo->query($requete);
    $fichiers = $requete->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($fichiers);

} elseif ($perimetre === 'détail' && $idFichierImporte) {
    // Requête pour récupérer le détail d'un fichier importé
    $requete = <<<EOD
    SELECT 
    IdFichierImporte, 
    NomDuFichier, 
    Dateimportation, 
    DescriptionCourte, 
    Typedefichier, 
    ContenuDuFichier,
    Caracteristiques,
    CompteRenduImportation
    FROM fichiersimportes
    WHERE IdFichierImporte = :id
    EOD;

    $requete = $pdo->prepare($requete);
    $requete->bindParam(':id', $idFichierImporte);
    $requete->execute();
    $fichierDetail = $requete->fetch(PDO::FETCH_ASSOC);

    if ($fichierDetail) {
        // Transformation du contenu de la colonne "ContenuDuFichier" en tableau d'ISBN
        $contenu = $fichierDetail['ContenuDuFichier'];
        //  Première colonne : ISBN
        //  Seconde colonne : genre
        $lignes = explode("\n", $contenu);  // Séparation des lignes
        $isbnArray = [];

        foreach ($lignes as $ligne) {
            $elements = explode(",", $ligne);  // Supposons que la virgule sépare les colonnes
            if (isset($elements[0])) {
                $isbnArray[] = trim($elements[0]);  // Extraction de l'ISBN et suppression des espaces
            }
        }

        // Remplacement du contenu par le tableau d'ISBN
        $fichierDetail['ContenuDuFichier'] = $isbnArray;
    }echo json_encode($fichierDetail);

} else {
    echo json_encode(["error" => "Paramètre 'périmètre' invalide ou IDFichierImporte manquant."]);
}
?>
