<?php
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
// Initialisation de la réponse JSON
$response = ['succes' => false, 'message' => '','existe' => false,'idlivre' =>-1];
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Récupérer le code ISBN envoyé depuis le formulaire
        $isbn = $_GET['isbn'];
        // Appel du webservice OpenLibrary avec l'ISBN fourni
        include 'bibliotheque_include_openlibrairyapi.php';
        if ($httpCode == 200) {
            $responseData = json_decode($result, true);
            if (isset($responseData["ISBN:$isbn"])) {
                include 'bibliotheque_include_openlibrairyapi_contenu.php';
                $response['json']=$responseData;
                $livre = $book;
                $response['succes'] = true;
                $response['idlivre'] = 0;
                $response['data'] = [
                    'titre' => $title .' ' . $sousTitre,
                    'previsualisation' => $previsualisation,
                    'isbn10' => $isbn10,
                    'isbn13' => $isbn13,
                    'id' => $id,
                    'description' =>'Non disponible',
                    'liencanonique' => $liencanonique,    
                    'auteurs' => $authors
                ];
                require_once 'bibliotheque_include_recherchedunlivreselonsonisbn.php';    
            } else {
                $response['succes'] = false;
                $response['message'] = 'Aucun livre trouvé avec cet ISBN.';
                $response['details'] = $responseData['error']['message'] ?? 'Détails non disponibles.';
                $response['code'] = $httpCode;
                $response['existe'] = false;
                $response['idlivre'] = -1;
            }
        } else {    
            $response['succes'] = false;
            $response['message'] = 'Erreur lors du questionnement de Open Librairy';
            $response['details'] = $responseData['error']['message'] ?? 'Détails non disponibles.';
            $response['code'] = $httpCode;
            $response['existe'] = false;
            $response['idlivre'] = -1;
        }    
    } else {
        echo "Méthode non autorisée.";
    }
} catch (Exception $e) {
    // Capturer les erreurs et retourner un message approprié
    $response['succes'] = false;
    $response['message'] = $e->getMessage();
    $response['existe'] = false;
    $response['idlivre'] = -1;
}
// Envoyer la réponse JSON
echo json_encode($response);

?>
