<?php
// Inclusion du fichier de connexion à la base de données
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
$response = ['succes' => false, 'message' => ''];
try {
    $query = $pdo->prepare("SELECT idorigines FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'OL']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesOpenLibrairy = $result['idorigines'];
    } else {
        throw new Exception('Origine Open Librairy introuvable.');
    }
 } catch (Exception $e) {
    // Gestion des erreurs
    $response['succes'] = false;
    $response['message'] = $e->getMessage();
    echo json_encode($response);
    exit;
}
try {
    require_once 'bibliotheque_include_lecture_d_un_ensemble_de_livres.php';
    // Parcourir chaque livre et mettre à jour les informations avec Google Books API
    foreach ($livres as $livre) {
        $Lues++;
        $id = $livre['IDLivres'];
        $titre = $livre['Titre'];
        $isbn13 = $livre['ISBN13'];
        $isbn10 = $livre['ISBN10'];
        if ($perimetre === 'ISBN13') {
            $isbn=$isbn13;
        }    
        else {
            $isbn=$isbn10;    
        }
        // Appel à l'API Google Books avec l'ISBN13
        include 'bibliotheque_include_googlebookapi.php';
        if ($result === false) {
            $updateQuery = $pdo->prepare("
            UPDATE livres 
                SET datederniererecherche = :datederniererecherche,
                Commentaire = CONCAT(:Commentaire, Commentaire)
            WHERE IDLivres = :idLivre
            ");
            $url="Erreur $httpCode sur recherche de https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn le ".date('d-m-Y à H:i:s')."\r\n";
            $updateQuery->execute([
                'idLivre' => $id,
                'Commentaire'=>$url,
                'datederniererecherche'=>date('Y-m-d H:i:s')    // Date actuelle en php car on maîtrise le fuseau avec PHP, pas avec mariadb dans le cas d'un serveur hébergé
            ]);
        }
        // Vérifier si des données sont retournées
        if (!isset($googleData['items']) || count($googleData['items']) === 0) {
            //  On n'a rien trouvé pour cette recherche dans l'API Gooogle
            // Appel à l'API Open Librairy avec l'ISBN13
            include 'bibliotheque_include_openlibrairyapi.php';
            if ($result === false) {
                $updateQuery = $pdo->prepare("
                UPDATE livres 
                    SET datederniererecherche = :datederniererecherche,
                    Commentaire = CONCAT(:Commentaire, Commentaire)
                WHERE IDLivres = :idLivre
                ");
                $url="Erreur $httpCode sur recherche de $url le ".date('d-m-Y à H:i:s')."\r\n";
                $updateQuery->execute([
                    'idLivre' => $id,
                    'Commentaire'=>$url,
                    'datederniererecherche'=>date('Y-m-d H:i:s')    // Date actuelle en php car on maîtrise le fuseau avec PHP, pas avec mariadb dans le cas d'un serveur hébergé
                ]);
            }
            // Vérifier si des données sont retournées
            $responseData = json_decode($result, true);
            if (isset($responseData["ISBN:$isbn"])) {
                $book = $responseData["ISBN:$isbn"];
                // Extraction des informations
                $titre = $book['title'] ?? 'Titre non disponible';
                $auteurs = isset($book['authors']) ? implode(", ", array_column($book['authors'], 'name')) : 'Auteur(s) non disponible(s)';
                $previsualisation=$book['url'] ?? '';
                if (empty($isbn10) && isset($book['identifiers']) && is_array($book['identifiers']['isbn_10']) && isset($book['identifiers']['isbn_10'][0])) {
                    $isbn10 = $book['identifiers']['isbn_10'][0];
                } else if (empty($isbn10)) {
                    $isbn10 = $book['identifiers']['isbn_10'] ?? '';
                }
                if (empty($isbn13) && isset($book['identifiers']) && is_array($book['identifiers']['isbn_13']) && isset($book['identifiers']['isbn_13'][0])) {
                    $isbn13 = $book['identifiers']['isbn_13'][0];
                } else if (empty($isbn13)) {
                    $isbn13 = $book['identifiers']['isbn_13'] ?? '';
                }
                if (is_array($book['identifiers']['openlibrary']) && isset($book['identifiers']['openlibrary'][0])) {
                    $olid = $book['identifiers']['openlibrary'][0];
                } else {
                    $olid = $book['identifiers']['openlibrary'] ?? '';
                }
                $lienReference=$book['url'] ?? 'Lien non disponible';
                $publish_date = $book['publish_date'] ?? 'Date de publication non disponible';
                $number_of_pages = $book['number_of_pages'] ?? 'Nombre de pages non disponible';
                $dateRecuperation = date('Y-m-d H:i:s'); // Date actuelle en php car on maîtrise le fuseau avec PHP, pas avec mariadb dans le cas d'un serveur hébergé
                try {
                    $table = "livres";
                    $condition = "ISBN10='$isbn10'";
                    $nombre = fa_compte($table, $condition);
                    switch ($nombre){
                        case 0 :
                            break;
                        case -1 :    
                            break;
                        default :
                            $isbn10=$isbn10.'-'.$dateRecuperation;
                            break;
                    }
                } catch (Exception $e) {
                    //echo "Une exception a été levée : " . $e->getMessage();
                    continue;
                }
                pl_creeJSonModifie();
                try {
                    $updateQuery = $pdo->prepare("
                        UPDATE livres 
                        SET ISBN10 = :isbn10, 
                            ISBN13 = :isbn13, 
                            DateRecuperation = :dateRecuperation, 
                            JSONModifie = :jsonModifie, 
                            OLID = :olid, 
                            titre = :titre, 
                            IDOrigines = :IDOrigines, 
                            datederniererecherche =:dateRecuperation,
                            dateRecuperation = :dateRecuperation

                        WHERE IDLivres = :idLivre
                    ");

                    $updateQuery->execute([
                        'isbn10' => $isbn10,
                        'isbn13' => $isbn13,
                        'dateRecuperation' => $dateRecuperation,
                        'jsonModifie' => $Jsonmodifie,
                        'olid' => $olid,
                        'idLivre' => $id,
                        'titre' => $titre,
                        'IDOrigines' =>$idoriginesOpenLibrairy
                    ]);
                    $Maj++;
                } catch (Exception $e) {
                    $response['succes'] = false;
                    $response['message'] = $e->getMessage();
                    exit;
                }    
            } else {
                $updateQuery = $pdo->prepare("
                UPDATE livres 
                    SET datederniererecherche = :datederniererecherche,
                    Commentaire = CONCAT(:Commentaire, Commentaire)
                WHERE IDLivres = :idLivre
                ");
                $url="Pas trouvé sur $url le ".date('d-m-Y à H:i:s')."\r\n";
                $updateQuery->execute([
                    'idLivre' => $id,
                    'Commentaire'=>$url,
                    'datederniererecherche'=>date('Y-m-d H:i:s')    // Date actuelle en php car on maîtrise le fuseau avec PHP, pas avec mariadb dans le cas d'un serveur hébergé
                ]);
            }
        } else {
            $Trouvees++;
            $dateRecuperation = date('Y-m-d H:i:s'); // Date actuelle en php car on maîtrise le fuseau avec PHP, pas avec mariadb dans le cas d'un serveur hébergé
            // Récupérer les informations depuis l'API Google
            $volumeInfo = $googleData['items'][0]['volumeInfo'] ?? null;
            if ($volumeInfo === null) {
                throw new Exception("Pas d'informations disponibles pour l'ISBN1 : $isbn");
            }
            $olid = $googleData['items'][0]['id'];
            $titre = $googleData['items'][0]['volumeInfo']['title'] ?? null;
            $isbn10= $googleData['items'][0]['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? 'isbn10 Non disponible-'.$dateRecuperation;
            //  On a déjà vu ds ISBN10 en plusieurs exemplaires ...
            try {
                $table = "livres";
                $condition = "ISBN10='$isbn10'";
                $nombre = fa_compte($table, $condition);
                switch ($nombre){
                    case 0 :
                        break;
                    case -1 :    
                        break;
                    default :
                        $isbn10=$isbn10.'-'.$dateRecuperation;
                        break;
                }
            } catch (Exception $e) {
                //echo "Une exception a été levée : " . $e->getMessage();
                continue;
            }
            $isbn13 = isset($googleData['items'][0]['volumeInfo']['industryIdentifiers'][1]['identifier']) && !empty($googleData['items'][0]['volumeInfo']['industryIdentifiers'][1]['identifier']) 
            ? $googleData['items'][0]['volumeInfo']['industryIdentifiers'][1]['identifier'] 
            : $isbn;
            $jsonImporte = json_encode($googleData); // JSON complet retourné par l'API Google
            // Mettre à jour les colonnes dans la table 'livres'
            try {
                $updateQuery = $pdo->prepare("
                    UPDATE livres 
                    SET ISBN10 = :isbn10, 
                        ISBN13 = :isbn13, 
                        DateRecuperation = :dateRecuperation, 
                        JSONImporte = :jsonImporte, 
                        OLID = :olid, 
                        titre = :titre, 
                        IDOrigines = :IDOrigines, 
                        datederniererecherche =:dateRecuperation,
                        dateRecuperation = :dateRecuperation

                    WHERE IDLivres = :idLivre
                ");

                $updateQuery->execute([
                    'isbn10' => $isbn10,
                    'isbn13' => $isbn13,
                    'dateRecuperation' => $dateRecuperation,
                    'jsonImporte' => $jsonImporte,
                    'olid' => $olid,
                    'idLivre' => $id,
                    'titre' => $titre,
                    'IDOrigines' =>$idoriginesGoogle
                ]);
                $Maj++;
            } catch (Exception $e) {
                //echo "Une exception a été levée : " . $e->getMessage();
                continue;
            }    
        }    
    }

    // Retourner le succès de l'opération
    $response['succes'] = true;
    $response['message'] = "Mise à jour réussie pour les livres sélectionnés (Trouvés/lus/Maj) (".$Trouvees."/".$Lues."/".$Maj.").";
    //$response['message'] = "Mise à jour réussie pour les livres sélectionnés.";


} catch (Exception $e) {
    // Gestion des erreurs
    $response['succes'] = false;
    $response['message'] = $e->getMessage();
}
function pl_creeJSonModifie(){
    global $Jsonmodifie;
    global $titre;
    global $description;
    global $isbn10;
    global $isbn13;
    global $lienReference;
    global $auteurs;
    global $number_of_pages;
    global $publish_date;
    $jsonData = [
        "kind" => "books#volumes",
        "totalItems" => 1,
        "items" => [
            [
                "kind" => "books#volume",
                "id" => uniqid(),  // Génère un ID unique
                "etag" => bin2hex(random_bytes(8)),  // Génère un etag aléatoire
                "selfLink" => "",  // Mettre un lien si nécessaire
                "volumeInfo" => [
                    "title" => $titre,
                    "authors" => explode(",", $auteurs),  // Transforme la chaîne d'auteurs en tableau
                    "publisher" => "Éditeur à définir",
                    "publishedDate" => $publish_date, // Année actuelle. Voir si on conserve l'information ...
                    "description" => $description,
                    "industryIdentifiers" => [
                        ["type" => "ISBN_10", "identifier" => $isbn10],
                        ["type" => "ISBN_13", "identifier" => $isbn13]
                    ],
                    "readingModes" => ["text" => false, "image" => false],
                    "pageCount" => $number_of_pages,  // Exemple de valeur, à adapter si nécessaire
                    "printType" => "BOOK",
                    "maturityRating" => "NOT_MATURE",
                    "allowAnonLogging" => false,
                    "contentVersion" => "preview-1.0.0",
                    "language" => "fr",
                    "previewLink" => "",  // Mettre un lien de prévisualisation si disponible
                    "infoLink" => "",  // Mettre un lien d'info si disponible
                    "canonicalVolumeLink" => $lienReference
                ],
                "saleInfo" => [
                    "country" => "FR",
                    "saleability" => "NOT_FOR_SALE",
                    "isEbook" => false,
                    "listPrice" => [
                        "amount"=> 0.01,
                        "currencyCode"=> "EUR"
                    ],
                    "retailPrice" => [
                        "amount"=> 0.01,
                        "currencyCode"=> "EUR"
                    ],
                    "buyLink"=> "http://example.com/buy"    
                ],
                "accessInfo" => [
                    "country" => "FR",
                    "viewability" => "NO_PAGES",
                    "embeddable" => false,
                    "publicDomain" => false,
                    "textToSpeechPermission" => "ALLOWED",
                    "epub" => ["isAvailable" => false],
                    "pdf" => ["isAvailable" => false],
                    "webReaderLink" => "",  // Mettre un lien de lecture en ligne si disponible
                    "accessViewStatus" => "NONE",
                    "quoteSharingAllowed" => false
                ],
                "searchInfo" => [
                    "textSnippet" => $description
                ]
            ]
        ]
    ];
    // Convertir les données en JSON
    $Jsonmodifie = json_encode($jsonData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

// Envoyer la réponse JSON
echo json_encode($response);
