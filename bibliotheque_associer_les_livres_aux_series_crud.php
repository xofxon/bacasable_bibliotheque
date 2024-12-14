<?php
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
$response = ['succes' => false, 'message' => 'Cas imprévu'];
// Initialisation de la réponse
try {
    // Récupération des paramètres POST
    $action = $_POST['perimetre'] ?? 'Consultation';
    $idLivre = $_POST['livreid'] ?? null;
    $idSerie = $_POST['serieid'] ?? null;
    $idlivreparserie = $_POST['livreserieid'] ?? null;
    switch ($action){
        case 'Suppression' :
            if ($idlivreparserie) {
                // Tentative de suppression de l'association
                $requete = $pdo->prepare("DELETE FROM livresparserie WHERE idlivreparserie = ?");
                if ($requete->execute([$livreserieid])) {
                    $response['succes'] = true;
                    $response['message'] = 'L\'association a été supprimée avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la suppression de l\'association.');
                }
            } else {
                throw new Exception('L\'ID de l\'association est requis pour effectuer une suppression.');
            }
            break;
        // Création d'une nouvelle association
        case 'Creation' :
            // Vérifier si une association existe déjà
            if ($idLivre && $idSerie) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM livresparserie WHERE idLivres = ? and idSeries = ?");
                $requeteTitre->execute([$idLivre,$idSerie]);
                $count = $requeteTitre->fetchColumn();

                if ($count > 0) {
                    throw new Exception('Cette association existe déjà.');
                }
            } else {
                throw new Exception('Les ID du livre et de la série sont requis pour effectuer une association.');
            }
            // Création de l'association
            //  En l'état si le livre vient de google books sinon on simule un jsonmodifie pour toujours requeter en json sur un seul modèle
            $requete = $pdo->prepare("INSERT INTO livresparserie (idLivres, idSeries) 
            VALUES (?, ?)");
            if ($requete->execute([$idLivre, $idSerie])) {
                $response['succes'] = true;
                $response['message'] = 'L\'association a été créée avec succès.';
            } else {
                throw new Exception('Une erreur est survenue lors de la création de l\'association.');
            }
            break;
        default :
            throw new Exception('Ce cas n\'est pas géré ('.$action.') ->.'.$_POST['perimetre']);
            break;
    }
} catch (Exception $e) {
    // En cas d'exception, on capture l'erreur et on renvoie le message
    $response['succes'] = false;
    $response['message'] = $e->getMessage();
}
// Renvoi de la réponse en JSON
echo json_encode($response);
