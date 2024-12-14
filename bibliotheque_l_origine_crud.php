<?php
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
// Initialisation de la réponse
$response = ['succes' => false, 'message' => 'Cas imprévu'];
try {
    // Récupération des paramètres POST
    $action = $_POST['perimetre'] ?? 'Consultation';
    $IDOrigines = $_POST['id'] ?? null;
    // Récupération des paramètres POST (partie formulaire)
    $CodeInterne = $_POST['CodeInterne'] ?? '';
    $Nom = $_POST['nomtitre'] ?? '';
    $Adresse = $_POST['adresse'] ?? '';
    $Etat = $_POST['etat'] ?? 1;
    switch ($action){
        case 'Creation' :
            break;
        case 'Suppression' :
            break;    
        case 'Modification' :    
            throw new Exception('En développement.');
            break;
        default :
            throw new Exception('Bizarre, comme demande!');
            break;
    }
    switch ($action){
        case 'Suppression' :
            if ($idLivre) {
                // Tentative de suppression du livre
                $requete = $pdo->prepare("DELETE FROM origines WHERE Gestion != 1 and IDOrigines = ?");
                if ($requete->execute([$IDOrigines])) {
                    $response['succes'] = true;
                    $response['message'] = 'L\'origine a été supprimée avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la suppression de l\'origine.');
                }
            } else {
                throw new Exception('L\'ID de l\'origine est requis pour effectuer une suppression.');
            }
            break;
        case 'Creation' :
            if ($CodeInterne) {
                $table = "origines";$condition = "CodeInterne='$CodeInterne'";$nombre = fa_compte($table, $condition);
                switch ($nombre){
                    case 0 :
                        break;
                    case -1 :    
                        break;
                    default :
                        throw new Exception('Une origine avec ce code existe déjà.');
                        break;
                }
            }
            $requete = $pdo->prepare("INSERT INTO origines (Nom, Adresse, CodeInterne, Etat) 
                                    VALUES (?, ?, ?, ?)");
            if ($requete->execute([$Nom, $Adresse, $CodeInterne, $Etat])) {
                $response['succes'] = true;
                $response['message'] = 'L\'origine a été créée avec succès.';
            } else {
                throw new Exception('Une erreur est survenue lors de la création de l\'origine.');
            }
            break;
        case 'Modification' :
            if ($IDOrigines) {
                $requete = $pdo->prepare("UPDATE origines 
                                SET Nom = ?, 
                                    Adresse = ?, 
                                    ISBN13 = ?, 
                                    Etat = ?
                                WHERE IDOrigines = ?");
                if ($requete->execute([$Nom, $Adresse, $Etat, $IDOrigines])) {
                    $response['succes'] = true;
                    $response['message'] = 'L\'orgine a été modifiée avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la modification de l\'origine.');
                }
            }
            break;
        default :
            throw new Exception('Ce cas n\'est pas géré.');
            break;
    }
} catch (Exception $e) {
    // En cas d'exception, on capture l'erreur et on renvoie le message
    $response['succes'] = false;
    $response['message'] = $e->getMessage();
}

// Renvoi de la réponse en JSON
echo json_encode($response);
