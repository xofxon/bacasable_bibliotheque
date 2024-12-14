<?php
require_once 'bibliotheque_informationsdecons.php';
header('Content-Type: application/json');
$response = ['succes' => false, 'message' => 'Cas imprévu'];
try {
    $query = $pdo->prepare("SELECT idorigines FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'RAF001']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesDefaut = $result['idorigines'];
    } else {
        echo "Aucune origine trouvée par défaut.";
    }
} catch (Exception $e) {
    echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
}
try {
    $query = $pdo->prepare("SELECT idorigines FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'GB']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesGoogle = $result['idorigines'];
    } else {
        //echo "Aucune origine trouvée pour codeinterne='GB'.";
    }
 } catch (Exception $e) {
    //echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
 }
 try {
    $query = $pdo->prepare("SELECT idorigines FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'OL']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesOL = $result['idorigines'];
    } else {
        //echo "Aucune origine trouvée pour codeinterne='GB'.";
    }
 } catch (Exception $e) {
    //echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
 }
 
// Initialisation de la réponse

/*
$action = $_POST['perimetre'] ?? 'Consultation';
$response = ['succes' => false, 'message' => $action];
echo json_encode($response);
exit;
*/
$Jsonmodifie="";
$titre="";
$description="";
$isbn10="";
$isbn13="";
$lienReference="";
$auteurs="";
try {
    // Récupération des paramètres POST
    $action = $_POST['perimetre'] ?? 'Consultation';
    $idLivre = $_POST['id'] ?? null;
    // Récupération des paramètres POST (partie formulaire)
    $titre = $_POST['titre'] ?? '';
    $isbn10 = $_POST['isbn10'] ?? null;
    $isbn13 = $_POST['isbn13'] ?? '';
    $etat = $_POST['etat'] ?? 1;
    $commentaire = $_POST['commentaire'] ?? null;
    $dateRecuperation = $_POST['daterecuperation'] ?? '';
    $jsonOutput = $_POST['jsonOutput'] ?? null;
    $pourquoi = $_POST['Pourquoi'] ?? 'Bibliotheque';
    if ($pourquoi === 'LesCourses') {
        $etat=0;
    }
    if ($jsonOutput) {
        $jsonString = base64_decode($jsonOutput);
        $jsonOutput=$jsonString;
    }    
    $lienReference=$_POST['lienReference'] ?? null;
    $description=$_POST['description'] ?? null;
    $auteurs=$_POST['auteurs'] ?? null;
    $olid = $_POST['olid'] ?? null;
    $sansJsonOutput= $_POST['sansJsonOutput'] ?? 'Non';
    $genre = $_POST['genre'] ?? 0;      //  ToutDoux : Gérer les autres genres de livres
    $idorigines= $_POST['idorigines'] ?? $idoriginesDefaut;
    $dateTime = new  DateTime($dateRecuperation);
    $timestamp = $dateTime->format('Y-m-d H:i:s');
    /*
    if (empty($jsonOutput)) {
        $jsonOutput=null;
    }
    */
    switch ($action){
        case 'Creation' :
        case 'Modification' :    
            // Vérification que le champ titre n'est pas vide pour la création ou la modification
            $concatenatedISBN = $isbn10 . $isbn13;
            if (empty($titre)) {
                throw new Exception('Le titre du livre est obligatoire.');
            }
            if (empty($concatenatedISBN)) {
                throw new Exception('L\'ISBN du livre est obligatoire.');
            }
            break;
        default :
            break;
    }

    switch ($action){
    // Suppression
        case 'Suppression' :
            if ($idLivre) {
                // Tentative de suppression du livre
                $requete = $pdo->prepare("DELETE FROM livres WHERE IDLivres = ?");
                if ($requete->execute([$idLivre])) {
                    $response['succes'] = true;
                    $response['message'] = 'Le livre a été supprimé avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la suppression du livre.');
                }
            } else {
                throw new Exception('L\'ID du livre est requis pour effectuer une suppression.');
            }
            break;
    // Création d'un nouveau livre
        case 'Creation' :
            // Vérifier si un livre avec le même ISBN10 existe déjà
            if ($isbn10) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM livres WHERE ISBN10 = ?");
                $requeteTitre->execute([$isbn10]);
                $count = $requeteTitre->fetchColumn();

                if ($count > 0) {
                    throw new Exception('Un livre avec cet Isbn10 existe déjà.');
                }
            }
            // Vérifier si un livre avec le même ISBN13 existe déjà
            if ($isbn13) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM livres WHERE ISBN13 = ?");
                $requeteTitre->execute([$isbn13]);
                $count = $requeteTitre->fetchColumn();

                if ($count > 0) {
                    throw new Exception('Un livre avec cet Isbn13 existe déjà.');
                }
            }

            // Création du nouveau livre
            //  En l'état si le livre vient de google books sinon on simule un jsonmodifie pour toujours requeter en json sur un seul modèle
            switch ($idorigines) {
                case $idoriginesOL:
                case $idoriginesDefaut:
                    pl_creeJSonModifie();
                    $requete = $pdo->prepare("INSERT INTO livres (Titre, ISBN10, ISBN13, Etat, Commentaire, DateRecuperation, genre,Jsonmodifie,OLID,IDOrigines,OLJson) 
                    VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?,?)");
                    if ($requete->execute([$titre, $isbn10, $isbn13, $etat, $commentaire, $timestamp, $genre,$Jsonmodifie,$olid,$idorigines,$jsonOutput])) {
                    $response['succes'] = true;
                    $response['message'] = 'Le livre a été créé avec succès.';
                    } else {
                    throw new Exception('Une erreur est survenue lors de la création du livre.');
                    }
                    break;    
                case $idoriginesGoogle:
                    $requete = $pdo->prepare("INSERT INTO livres (Titre, ISBN10, ISBN13, Etat, Commentaire, DateRecuperation, genre,JSONImporte,OLID,IDOrigines) 
                    VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?)");
                    if ($requete->execute([$titre, $isbn10, $isbn13, $etat, $commentaire, $timestamp, $genre,$jsonOutput,$olid,$idorigines])) {
                    $response['succes'] = true;
                    $response['message'] = 'Le livre a été créé avec succès.';
                    } else {
                    throw new Exception('Une erreur est survenue lors de la création du livre.');
                    }
                    break;
                default:
                    $srequete .= " WHERE genre = $genre";
                    break;
            }
            break;
    // Modification d'un livre existant
        case 'Modification' :
            // Vérifier si un livre avec le même ISBN10 existe déjà (hors du livre actuellement modifié)
            if ($isbn10) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM livres WHERE ISBN10 = ? AND IDLivres != ?");
                $requeteTitre->execute([$isbn10, $idLivre]);
                $count = $requeteTitre->fetchColumn();

                if ($count > 0) {
                    throw new Exception('Un livre avec cet Isbn10 existe déjà.');
                }
            }
            // Vérifier si un livre avec le même ISBN13 existe déjà
            if ($isbn13) {
                $requeteTitre = $pdo->prepare("SELECT COUNT(*) FROM livres WHERE ISBN13 = ? AND IDLivres != ?");
                $requeteTitre->execute([$isbn13, $idLivre]);
                $count = $requeteTitre->fetchColumn();

                if ($count > 0) {
                    throw new Exception('Un livre avec cet Isbn13 existe déjà.');
                }
            }
            //  On cherche les informations dans le json d'origine
            $stexterequete= <<< EOD
            SELECT
                CASE 
                    WHEN idorigines = $idoriginesGoogle THEN 
                        SUBSTRING(
                            JSON_UNQUOTE(JSON_EXTRACT(jsonimporte, '$.items[0].volumeInfo.description')), 
                            1, 
                            LENGTH(JSON_UNQUOTE(JSON_EXTRACT(jsonimporte, '$.items[0].volumeInfo.description'))) - 1
                        )
                    ELSE '' 
                END AS descriptioninitiale,
                CASE 
                    WHEN idorigines = $idoriginesGoogle THEN 
                        REPLACE(
                            REPLACE(
                                SUBSTRING(JSON_UNQUOTE(JSON_EXTRACT(jsonimporte, '$.items[0].volumeInfo.authors')), 2, LENGTH(JSON_UNQUOTE(JSON_EXTRACT(jsonimporte, '$.items[0].volumeInfo.authors'))) - 2), 
                                '\"', ''
                            ), 
                            '","', ', '
                        )
                    ELSE '' 
                END AS auteursInitiaux
                
            FROM livres
            EOD;
            $srequete = $pdo->prepare($stexterequete." WHERE `IDLivres` = ?");
            $srequete->execute([$idLivre]);
            $livre = $srequete->fetch(PDO::FETCH_ASSOC);
            if ($livre) {
                $descriptioninitiale = $livre['descriptioninitiale'];
                $auteursInitiaux = $livre['auteursInitiaux'];
            } else {
                //pas de livre !!! Plus que bizarre. On vient de me le supprimer sous le nez ??
            }
            if ($description !== $descriptioninitiale || $auteurs !== $auteursInitiaux) {
                //  On reproduit comme un json de goggle api. A voir s'il n'y en a pas de plus riches
                //  On pourrait avoir l'éditeur, dans certains cas ?
                pl_creeJSonModifie();
            } else {
                // Faire un autre truc si les deux sont égales
                $Jsonmodifie=Null;
            }
            // Modification du livre existant
            if ($sansJsonOutput == 'Non') {
            $requete = $pdo->prepare("UPDATE livres 
                            SET Titre = ?, 
                                ISBN10 = ?, 
                                ISBN13 = ?, 
                                Etat = ?, 
                                Commentaire = ?, 
                                DateRecuperation = ?, 
                                genre = ?,
                                JSONImporte= ?,
                                OLID= ?,
                                IDOrigines =?,
                                Jsonmodifie = ?
                            WHERE IDLivres = ?");
                if ($requete->execute([$titre, $isbn10, $isbn13, $etat, $commentaire, $timestamp, $genre, $jsonOutput,$olid,$idorigines,$Jsonmodifie,$idLivre])) {
                    $response['succes'] = true;
                    $response['message'] = 'Le livre a été modifié avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la modification du livre.');
                }
            } else {
                $requete = $pdo->prepare("UPDATE livres 
                SET Titre = ?, 
                    ISBN10 = ?, 
                    ISBN13 = ?, 
                    Etat = ?, 
                    Commentaire = ?, 
                    DateRecuperation = ?, 
                    genre = ?,
                    OLID= ?,
                    IDOrigines =?,
                    Jsonmodifie = ?
                WHERE IDLivres = ?");
                if ($requete->execute([$titre, $isbn10, $isbn13, $etat, $commentaire, $timestamp, $genre, $olid,$idorigines,$Jsonmodifie,$idLivre])) {
                    $response['succes'] = true;
                    $response['message'] = 'Le livre a été modifié avec succès.';
                } else {
                    throw new Exception('Une erreur est survenue lors de la modification du livre.');
                }
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

function pl_creeJSonModifie(){
    global $Jsonmodifie;
    global $titre;
    global $description;
    global $isbn10;
    global $isbn13;
    global $lienReference;
    global $auteurs;
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
                    "publishedDate" => date("Y"), // Année actuelle. Voir si on conserve l'information ...
                    "description" => $description,
                    "industryIdentifiers" => [
                        ["type" => "ISBN_10", "identifier" => $isbn10],
                        ["type" => "ISBN_13", "identifier" => $isbn13]
                    ],
                    "readingModes" => ["text" => false, "image" => false],
                    "pageCount" => 48,  // Exemple de valeur, à adapter si nécessaire
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
