<?php
require_once 'bibliotheque_informationsdecons.php';
$genre = $_GET['genre'] ?? 0;
$ncondition = $_GET['condition'] ?? 0;
$genre=-$genre;
$idLivre = $_GET['id'] ?? null;
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
       $idoriginesOpenLibrairy = $result['idorigines'];
   } else {
       //echo "Aucune origine trouvée pour codeinterne='OL'.";
   }

} catch (Exception $e) {
   //echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
}
if ($idLivre) {
$stexterequete= <<< EOD
SELECT
    livres.idorigines,
    livres.IDLivres,
    livres.Titre,
    livres.ISBN10,
    livres.ISBN13,
    livres.OLID,
    livres.Etat,
    livres.Commentaire AS Commentaires,
    case when livres.jsonimporte is null then '' else livres.jsonimporte end as jsonimporte ,
    case when livres.jsonmodifie is null then '' else livres.jsonmodifie end as jsonmodifie,
    case when livres.OLJson is null then '' else livres.OLJson end as OLJson,
    Genre,
    CASE 
        WHEN livres.daterecuperation = '0000-00-00 00:00:00' THEN '' 
        ELSE DATE_FORMAT(livres.daterecuperation, '%d/%m/%Y %T') 
    END AS DateRecuperation,

            SUBSTRING(
                JSON_UNQUOTE(JSON_EXTRACT(
                    CASE 
                        WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                        ELSE livres.jsonimporte 
                    END, 
                    '$.items[0].volumeInfo.description'
                )), 
                1, 
                LENGTH(JSON_UNQUOTE(JSON_EXTRACT(
                    CASE 
                        WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                        ELSE livres.jsonimporte 
                    END, 
                    '$.items[0].volumeInfo.description'
                ))) - 1
            )
    AS description,

            REPLACE(
                REPLACE(
                    SUBSTRING(
                        JSON_UNQUOTE(JSON_EXTRACT(
                            CASE 
                                WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                                ELSE livres.jsonimporte 
                            END, 
                            '$.items[0].volumeInfo.authors'
                        )), 
                        2, 
                        LENGTH(JSON_UNQUOTE(JSON_EXTRACT(
                            CASE 
                                WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                                ELSE livres.jsonimporte 
                            END, 
                            '$.items[0].volumeInfo.authors'
                        ))) - 2
                    ), 
                    '\"', ''
                ), 
                '","', ', '
            )
        
    AS auteurs,

    CASE 
        WHEN idorigines not in($idoriginesOpenLibrairy) THEN 
            JSON_UNQUOTE(JSON_EXTRACT(
                CASE 
                    WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                    ELSE livres.jsonimporte 
                END, 
                '$.items[0].volumeInfo.canonicalVolumeLink'
            ))
        WHEN livres.idorigines = $idoriginesOpenLibrairy AND OLID IS NOT NULL AND OLID != '' THEN 
            CONCAT('https://openlibrary.org/books/', OLID)
        ELSE 
            JSON_UNQUOTE(JSON_EXTRACT(
                CASE 
                    WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                    ELSE livres.jsonimporte 
                END, 
                '$.items[0].volumeInfo.canonicalVolumeLink'
            ))
    END AS lienReference,

    CASE 
        WHEN livres.idorigines = $idoriginesGoogle THEN 
            JSON_UNQUOTE(JSON_EXTRACT(
                CASE 
                    WHEN livres.jsonmodifie IS NOT NULL AND livres.jsonmodifie != '' THEN livres.jsonmodifie 
                    ELSE livres.jsonimporte 
                END, 
                '$.items[0].volumeInfo.searchInfo[0].textSnippet'
            ))
        WHEN livres.idorigines = 1 AND OLID IS NOT NULL AND OLID != '' THEN 
            CONCAT('https://openlibrary.org/books/', OLID)
        ELSE '' 
    END AS textSnippet
    ,series.Nom as serieNom
    ,series.Adresse as serieSite
    ,series.IDSeries as serieID
FROM livres
left join _choix on _choix.choix_cleunik=livres.genre
LEFT JOIN livresparserie lp ON livres.IDLivres = lp.IDLivres
LEFT JOIN series ON series.IDSeries = lp.IDSeries

EOD;
   $srequete = $pdo->prepare($stexterequete." WHERE livres.IDLivres = ?");
    $srequete->execute([$idLivre]);
    $livre = $srequete->fetch(PDO::FETCH_ASSOC);
    echo json_encode($livre);
    exit;
}
$srequete= <<< EOD
SELECT
    livres.idorigines,
    livres.IDLivres,
    livres.Titre,
    livres.ISBN10,
    livres.ISBN13,
    livres.OLID,
    CASE 
        WHEN livres.etat = 1 THEN 'Présent' 
        ELSE 'Absent' 
    END AS Etat,
    livres.Commentaire AS Commentaires,
    CASE 
        WHEN livres.daterecuperation = '0000-00-00 00:00:00' THEN '' 
        ELSE DATE_FORMAT(livres.daterecuperation, '%d/%m/%Y %T') 
    END AS DateRecuperation,
    choix_valeur_texte_01 AS Genre,
            SUBSTRING(
                JSON_UNQUOTE(JSON_EXTRACT(
                    CASE 
                        WHEN jsonmodifie IS NOT NULL AND jsonmodifie != '' THEN jsonmodifie 
                        ELSE jsonimporte 
                    END, 
                    '$.items[0].volumeInfo.description'
                )), 
                1, 
                LENGTH(JSON_UNQUOTE(JSON_EXTRACT(
                    CASE 
                        WHEN jsonmodifie IS NOT NULL AND jsonmodifie != '' THEN jsonmodifie 
                        ELSE jsonimporte 
                    END, 
                    '$.items[0].volumeInfo.description'
                ))) - 1
            )
    AS description,

                REPLACE(
                REPLACE(
                    SUBSTRING(
                        JSON_UNQUOTE(JSON_EXTRACT(
                            CASE 
                                WHEN jsonmodifie IS NOT NULL AND jsonmodifie != '' THEN jsonmodifie 
                                ELSE jsonimporte 
                            END, 
                            '$.items[0].volumeInfo.authors'
                        )), 
                        2, 
                        LENGTH(JSON_UNQUOTE(JSON_EXTRACT(
                            CASE 
                                WHEN jsonmodifie IS NOT NULL AND jsonmodifie != '' THEN jsonmodifie 
                                ELSE jsonimporte 
                            END, 
                            '$.items[0].volumeInfo.authors'
                        ))) - 2
                    ), 
                    '\"', ''
                ), 
                '","', ', '
            )
        
    AS auteurs,

    CASE 
        WHEN idorigines not in($idoriginesOpenLibrairy) THEN 
            JSON_UNQUOTE(JSON_EXTRACT(
                CASE 
                    WHEN jsonmodifie IS NOT NULL AND jsonmodifie != '' THEN jsonmodifie 
                    ELSE jsonimporte 
                END, 
                '$.items[0].volumeInfo.canonicalVolumeLink'
            ))
        WHEN idorigines = $idoriginesOpenLibrairy AND OLID IS NOT NULL AND OLID != '' THEN 
            CONCAT('https://openlibrary.org/books/', OLID)
        ELSE             JSON_UNQUOTE(JSON_EXTRACT(
                CASE 
                    WHEN jsonmodifie IS NOT NULL AND jsonmodifie != '' THEN jsonmodifie 
                    ELSE jsonimporte 
                END, 
                '$.items[0].volumeInfo.canonicalVolumeLink'
            ))
 
    END AS lienReference,
    DateDerniereRecherche
    ,CASE 
        WHEN idorigines = $idoriginesGoogle THEN 
            JSON_UNQUOTE(JSON_EXTRACT(jsonimporte, '$.items[0].searchInfo.textSnippet'))
        ELSE '' 
    END AS textSnippet
    ,series.Nom
FROM livres
left join _choix on _choix.choix_cleunik=livres.genre
LEFT JOIN livresparserie lp ON livres.IDLivres = lp.IDLivres
LEFT JOIN series ON series.IDSeries = lp.IDSeries
EOD;
switch ($ncondition) {
    case 0:
        //  on prend 
        $srequete .= " WHERE livres.Etat != 0 and genre = $genre";
        break;

    case 1:
        //  On est dans le cas du panier de courses
        $srequete .= " WHERE livres.Etat = 0";
        break;

    case -2:
        // ISBN13 invalides et ISBN10 valides
        $srequete .= " where xof_is_valid_isbn13(livres.ISBN13)=false and xof_is_valid_isbn10(livres.ISBN10)=true and genre = $genre";
        break;

    case 3:
        // ISBN13 valides et ISBN10 invalides
        $srequete .= "where xof_is_valid_isbn13(livres.ISBN13)=true and xof_is_valid_isbn10(livres.ISBN10)=false and genre = $genre";
        break;
    case 4:
        //  Les livres pas reliés à des séries
        $srequete .= " WHERE lp.IDLivres IS NULL and genre = $genre";
        break;
    default:
        $srequete .= " WHERE genre = $genre";
        break;
}

$srequete .= " ORDER BY titre;";

$query = $pdo->query($srequete);
$livres = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($livres);
?>
