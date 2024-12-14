<?php
require_once 'bibliotheque_informationsdecons.php'; // Inclusion de la connexion à la base de données

header('Content-Type: application/json');
$response = ['succes' => false, 'message' => '','tableauIds' => []];
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

/**
 * Fonction pour convertir un ISBN10 en ISBN13
 */
function fl_convertISBN10to13($isbn10) {
    $isbn = substr("978" . $isbn10, 0, -1);
    $checkDigit = 0;
    for ($i = 0; $i < strlen($isbn); $i++) {
        $checkDigit += (int) $isbn[$i] * (($i % 2) ? 3 : 1);
    }
    $checkDigit = (10 - ($checkDigit % 10)) % 10;
    return $isbn . $checkDigit;
}

function fl_parseContenuDuFichier($binaryContent) {
    $data = [];
    $offset = 0;
    $recordLength = 20; // Exemple de longueur de chaque enregistrement, à adapter

    while ($offset < strlen($binaryContent)) {
        $isbn = trim(substr($binaryContent, $offset, 13)); // Suppose un ISBN de 13 caractères
        $additionalInfo = trim(substr($binaryContent, $offset + 13, $recordLength - 13)); // Autres infos, exemple

        $data[] = [$isbn, $additionalInfo];
        $offset += $recordLength;
    }
    return $data;
}
$perimetre = $_GET['perimetre'] ?? '';
$IDFichierImporte = $_GET['IDFichierImporte'] ?? 0;
$livresIds = [];
$importErrors = []; // Stocker les erreurs d'import

// Si le paramètre "perimetre" est "retraitement", traiter le contenu d'un fichier déjà importé
if ($perimetre === 'retraitement') {
    try {
        $inserees=0;
        $totalLines=0;
        // Récupérer le contenu du fichier importé
        $requeteFichier = $pdo->prepare("SELECT contenudufichier FROM fichiersimportes WHERE IDFichierImporte = :IDFichierImporte");
        $requeteFichier->execute(['IDFichierImporte'=>$IDFichierImporte]);
        $fichier = $requeteFichier->fetch(PDO::FETCH_ASSOC);

        if ($fichier && !empty($fichier['contenudufichier'])) {
            // Traiter le contenu du fichier comme un tableau à deux dimensions (supposé encodé en JSON)
            $contenu = $fichier['contenudufichier'];
            $parsedData = fl_parseContenuDuFichier($contenu);

            foreach ($parsedData as $row) {
                $isbn = $row[0];
                $totalLines++;
                // Rechercher le livre dans la table "livres" avec jsonimporte vide
                $requeteLivre = $pdo->prepare("SELECT idlivres FROM livres WHERE (isbn13 = :isbn13 OR isbn10 = :isbn10) and (jsonimporte IS NULL OR jsonimporte ='')");
                $requeteLivre->execute([':isbn13' => $isbn, ':isbn10' => $isbn]);
                $livre = $requeteLivre->fetch(PDO::FETCH_ASSOC);
    
                if ($livre) {
                    $livresIds[] = $livre['idlivres'];
                    $inserees++;
                    //echo "Livre trouvé avec ID: {$livre['idlivre']} pour ISBN: $isbn\n";
                } else {
                    //echo "Aucun livre trouvé avec ISBN: $isbn et jsonimporte vide.\n";
                }
            }

            $response['succes'] = true;
            $response['message'] = "Le fichier a été traité avec succès. Lignes lues/à traiter : $totalLines/$inserees.";
            $response['tableauIds'] = $livresIds;
        } else {
            throw new Exception("Rien à traiter pour ce fichier.");
        }
    } catch (Exception $e) {
        $response['succes'] = false;
        $response['message'] = $e->getMessage();
    }    
} else {
    try {
        // Vérifier si le fichier CSV est présent
        if (isset($_FILES['csvfile']) && $_FILES['csvfile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['csvfile']['tmp_name'];
            $fileName = $_FILES['csvfile']['name'];
            $fileFullPath = realpath($fileTmpPath); // Chemin complet du fichier
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $originalFileName = $_POST['originalFileName']; // Nom d'origine du fichier
            $totalLines = 0; // Compteur de lignes lues
            //$etat=$_GET['etat'] ?? 1;
            $etat=1;
            // Vérifier si c'est bien un fichier CSV
            if ($fileExtension === 'csv') {
                $contenuFichier = file_get_contents($fileTmpPath); // Lire le contenu du fichier pour l'enregistrement

                // Ouvrir le fichier pour lecture
                if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                    
                    $inserees=0;
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        sleep(1);   //tempo 1 seconte pour ne pas stresser google books
                        $totalLines++;
                        $isbn = trim($data[0]);
                        $perimetre = isset($data[1]) ? (int) trim($data[1]) : null;
                        $isbn13 = null;

                        // Vérifier si l'ISBN est de type ISBN13
                        if (preg_match('/^\d{13}$/', $isbn)) {
                            $isbn13 = $isbn;
                            $isbn10 = null; // Pas de conversion nécessaire
                        } 
                        // Sinon, vérifier s'il s'agit d'un ISBN10 et le convertir en ISBN13
                        elseif (preg_match('/^\d{10}$/', $isbn)) {
                            $isbn13 = fl_convertISBN10to13($isbn);
                            $isbn10 = $isbn13 ? $isbn : null; // Si la conversion échoue, l'ISBN10 sera l'original
                        } else {
                            $importErrors[] = "Erreur à la ligne $totalLines : ISBN $isbn non valide";
                            continue; // Passer à la ligne suivante
                        }

                        // Vérifier si ISBN13 est déjà présent dans la table 'livres'
                        $checkQuery = $pdo->prepare("SELECT * FROM livres WHERE isbn13 = :isbn13 OR isbn10 = :isbn10");
                        $checkQuery->execute([':isbn13' => $isbn13, ':isbn10' => $isbn10]);
                        if ($checkQuery->rowCount() === 0) {
                            // Ajouter l'entrée dans la table 'livres' si l'ISBN13 est absent
                            $pdo->beginTransaction();
                            $insertQuery = $pdo->prepare("
                                INSERT INTO livres (ISBN13, DateRecuperation, nature, genre,etat,IdOrigines,ISBN10) 
                                VALUES (:isbn13, CURRENT_TIMESTAMP, :nature, :genre,:etat,:origine,:isbn10)
                            ");
                            $insertQuery->execute([
                                'isbn13' => $isbn13,
                                'nature' => 54467, // Valeur par défaut pour 'nature'
                                'genre' => $perimetre,  // Le périmètre est utilisé comme genre
                                'etat' => $etat,
                                'origine'=>$idoriginesDefaut,
                                'isbn10' =>$isbn10
                            ]);
                            $dernierId = $pdo->lastInsertId();
                            $pdo->commit();
                            $livresIds[] = $dernierId;
                            $inserees++;
                        } else {
                            $importErrors[] = "Erreur à la ligne $totalLines : ISBN13 $isbn13 déjà présent dans la base";
                        }
                    }
                    fclose($handle);

                    // Enregistrer les informations du fichier dans la table `fichiersimportes`
                    $descriptionCourte = "Importation du " . date('d/m/Y H:i:s');
                    $caracteristiques = "Nombre de lignes lues : $totalLines, insérées :$inserees \n";
                    if (!empty($importErrors)) {
                        $importErrorsWithNewlines = array_map(function($error) {
                            return $error . "\n";
                        }, $importErrors);
                        $CompteRenduImportation .= "Erreurs d'import :\n" . implode("", $importErrorsWithNewlines);
                    }
                    $insertFichierQuery = $pdo->prepare("
                        INSERT INTO fichiersimportes 
                        (NomDuFichier, DateImportation, DescriptionCourte, Caracteristiques, ContenuDuFichier, TypeDeFichier,CompteRenduImportation) 
                        VALUES (:nomDuFichier, CURRENT_TIMESTAMP, :descriptionCourte, :caracteristiques, :contenuFichier, :typeDeFichier,:CompteRenduImportation)
                    ");
                    $insertFichierQuery->execute([
                        'nomDuFichier' => $originalFileName,
                        'descriptionCourte' => $descriptionCourte,
                        'caracteristiques' => $caracteristiques,
                        'contenuFichier' => $contenuFichier,
                        'typeDeFichier' => 1, // Type 1 = CSV
                        'CompteRenduImportation'=>$CompteRenduImportation
                    ]);

                    $response['succes'] = true;
                    $response['message'] = "Le fichier a été traité avec succès. Lignes lues/Insérées : $totalLines"."/".$inserees.".";
                    $response['tableauIds'] = $livresIds;
                } else {
                    throw new Exception("Impossible de lire le fichier.");
                }
            } else {
                throw new Exception("Veuillez télécharger un fichier CSV valide.");
            }
        } else {
            throw new Exception("Erreur lors du téléchargement du fichier.");
        }
    } catch (Exception $e) {
        $response['succes'] = false;
        $response['message'] = $e->getMessage();
    }
}
// Envoyer la réponse JSON
echo json_encode($response);
?>
