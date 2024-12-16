<?php
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
// Initialisation de la réponse JSON
$response = ['succes' => false, 'message' => '','existe' => false,'idlivre' =>-1];
try {
    // Récupération de l'ISBN depuis les paramètres GET ou POST
    $isbn = $_GET['isbn'] ?? $_POST['isbn'] ?? null;
    if ($isbn !== null) {
        $isbn = preg_replace('/\s+/', '', $isbn);
    }
    if (empty($isbn)) {
        throw new Exception('L\'ISBN est requis pour la recherche.');
    }
    $isbn13=$isbn;
    if (!fa_isValidISBN13($isbn13)) {
        $isbn10=$isbn;
        if (!fa_isValidISBN10($isbn10)) {
            throw new Exception('Le format de l\'ISBN est invalide.');
        }    
    }
    include 'bibliotheque_include_googlebookapi.php';
    if ($result === false) {
        throw new Exception('Erreur lors de la communication avec l\'API.');
    }
    $responseData = json_decode($result, true);
    //echo $result. PHP_EOL;
    // Gestion des erreurs spécifiques, comme 429
    if ($httpCode == 429) {
        $response['succes'] = false;
        $response['message'] = 'Quota dépassé pour l\'API Google Books. Veuillez réessayer plus tard.';
        $response['details'] = $responseData['error']['message'] ?? 'Détails non disponibles.';
        $response['code'] = 429;
        $response['existe'] = false;
        $response['idlivre'] = -1;
    } elseif ($httpCode >= 400) {
        $response['succes'] = false;
        $response['message'] = "Erreur de l'API Google Books. Code: $httpCode";
        $response['details'] = $responseData['error']['message'] ?? 'Détails non disponibles.';
        $response['code'] = $httpCode;
        $response['existe'] = false;
        $response['idlivre'] = -1;
    } else {
        // Si la réponse est correcte
        if (isset($responseData['items'][0])) {
            $response['json']=$responseData;
            $livre = $responseData['items'][0];
            $response['succes'] = true;
            $response['idlivre'] = 0;
            $response['data'] = [
                'titre' => $livre['volumeInfo']['title'] ?? 'Titre non disponible',
                'soustitre' => $livre['volumeInfo']['subtitle'] ?? 'Sous-Titre non disponible',
                'auteurs' => implode(', ', $livre['volumeInfo']['authors'] ?? []),
                'description' => $livre['volumeInfo']['description'] ?? 'Description non disponible',
                'isbn10' => $livre['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? '???' . uniqid(),
                'isbn13' => $livre['volumeInfo']['industryIdentifiers'][1]['identifier'] ?? '???' . uniqid(),
                'id' => $livre['id'] ?? 'Non disponible',
                'editeur' => $livre['volumeInfo']['publisher'] ?? 'Inconnu',
                'datedepublication' => $livre['volumeInfo']['publishedDate'] ?? 'Inconnue',
                'nombredepages' => $livre['volumeInfo']['pageCount'] ?? 'Non disponible',
                'typedepublication' => $livre['volumeInfo']['printType'] ?? 'Non disponible',
                'categories' => implode(', ', $livre['volumeInfo']['categories'] ?? []),
                'classementmaturité' => $livre['volumeInfo']['maturityRating'] ?? 'Non disponible',
                'lectureanonymeautorisée' => $livre['volumeInfo']['allowAnonLogging'] ? 'Oui' : 'Non',
                'versionducontenu' => $livre['volumeInfo']['contentVersion'] ?? 'Non disponible',
                'langue' => $livre['volumeInfo']['language'] ?? 'Non disponible',
                'previsualisation' => $livre['volumeInfo']['previewLink'] ?? '',
                'plusdinformations' => $livre['volumeInfo']['infoLink'] ?? '',
                'liencanonique' => $livre['volumeInfo']['canonicalVolumeLink'] ?? '',
                'couverturedulivre' => $livre['volumeInfo']['imageLinks.thumbnail'] ?? ''
            ];
            $isbn10 = isset($livre['volumeInfo']['industryIdentifiers'][0]['identifier']) 
            ? $livre['volumeInfo']['industryIdentifiers'][0]['identifier'] 
            : null;
            $isbn13 = isset($livre['volumeInfo']['industryIdentifiers'][1]['identifier']) 
                    ? $livre['volumeInfo']['industryIdentifiers'][1]['identifier'] 
                    : null;
            require_once 'bibliotheque_include_recherchedunlivreselonsonisbn.php';
            } else {
            throw new Exception('Aucun résultat trouvé pour cet ISBN.');
        }
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
