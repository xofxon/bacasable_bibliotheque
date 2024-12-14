<?php
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
$response = ['succes' => false, 'message' => 'Cas imprévu'];
// Initialisation de la réponse
try {
    // Récupération des paramètres POST
    $action = $_POST['perimetre'] ?? 'Consultation';
    $idSerie = $_POST['id'] ?? null;
    // Récupération des paramètres POST (partie formulaire)
    $nom = $_POST['nom'] ?? '';
    $adresse = $_POST['site'] ?? null;
    $etat = $_POST['etat'] ?? 1;
    $commentaire = $_POST['commentaire'] ?? null;
    $dateRecuperation = $_POST['daterecuperation'] ?? '';
    $nature = $_POST['genre'] ?? 0;
    $dateTime = new  DateTime($dateRecuperation);
    $timestamp = $dateTime->format('Y-m-d H:i:s');
    switch ($action){
    // Suppression
        case 'Suppression' :
            if ($idSerie) {
                // Tentative de suppression du livre
                $requete = $pdo->prepare("DELETE FROM series WHERE idSeries = ?");
                if ($requete->execute([$idSerie])) {
                    $response['succes'] = true;
                    $response['message'] = 'La série a été supprimée avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la suppression de la série.');
                }
            } else {
                throw new Exception('L\'ID de la série est requis pour effectuer une suppression.');
            }
            break;
    // Création d'une nouvelle série
        case 'Creation' :
            // Vérifier si une série avec le même nom existe déjà
            if ($nom) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM series WHERE Nom = ?");
                $requeteTitre->execute([$nom]);
                $count = $requeteTitre->fetchColumn();
                if ($count > 0) {
                    throw new Exception('Une série avec ce nom existe déjà.');
                }
            }
            // Création d'une nouvelle série
            $requete = $pdo->prepare("INSERT INTO series (Nom, Adresse, Commentaire, Etat, DateRecuperation, nature) 
            VALUES (?, ?, ?, ?, ?, ?)");
            if ($requete->execute([$nom, $adresse, $commentaire, $etat, $timestamp, $nature])) {
            $response['succes'] = true;
            $response['message'] = 'La série a été créée avec succès.';
            } else {
            throw new Exception('Une erreur est survenue lors de la création de la série.');
            }
            break;    
    // Modification d'une série existante
        case 'Modification' :
            // Vérifier si une série avec le même nom existe déjà (hors de la série actuellement modifiée)
            if ($nom) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM series WHERE Nom = ? AND IDSeries != ?");
                $requeteTitre->execute([$nom, $idSerie]);
                $count = $requeteTitre->fetchColumn();
                if ($count > 0) {
                    throw new Exception('Une série avec ce nom existe déjà.');
                }
            }
            // Modification du livre existant
            $requete = $pdo->prepare("UPDATE series 
                        SET Nom = ?, 
                            Adresse = ?, 
                            Etat = ?, 
                            Commentaire = ?, 
                            DateRecuperation = ?, 
                            nature = ?
                        WHERE IDSeries = ?");
            if ($requete->execute([$nom, $adresse, $etat, $commentaire, $timestamp, $nature,$idSerie])) {
                $response['succes'] = true;
                $response['message'] = 'La série a été modifiée avec succès.';
            } else {
                throw new Exception('Une erreur est survenue lors de la modification de la série.');
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