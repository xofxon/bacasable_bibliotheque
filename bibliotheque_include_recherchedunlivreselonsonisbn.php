<?php
try {
    $table = "livres";
    $condition = "ISBN13='$isbn13'";
    $nombre = fa_compte($table, $condition);
    switch ($nombre){
        case 0 :
            $response['existe'] = false;
            break;
        case -1 :    
            $response['existe'] = false;
            break;
        default :
        try {
            // Préparation de la requête
            $query = $pdo->prepare("SELECT idlivres FROM livres WHERE ISBN13 = :codeinterne");
            $query->execute(['codeinterne' => $isbn13]);
            // Récupérer le résultat
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $response['idlivre'] = $result['idlivres'];
            } else {
                $response['idlivre'] = -1;
            }
        
        } catch (Exception $e) {
            echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
        };
        $response['existe'] = true;
        break;
    }
} catch (Exception $e) {
    throw new Exception("Une exception de comptage a été levée : " . $e->getMessage());
}    
if ($nombre ==0 ){
    try {
        $table = "livres";
        $condition = "ISBN10='$isbn10'";
        $nombre = fa_compte($table, $condition);
        switch ($nombre){
            case 0 :
                $response['existe'] = false;
                break;
            case -1 :    
                $response['existe'] = false;
                break;
            default :
            try {
                // Préparation de la requête
                $query = $pdo->prepare("SELECT idlivres FROM livres WHERE ISBN10 = :codeinterne");
                $query->execute(['codeinterne' => $isbn10]);
                // Récupérer le résultat
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $response['idlivre'] = $result['idlivres'];
                } else {
                    $response['idlivre'] = -1;
                }
            
            } catch (Exception $e) {
                echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
            };
            $response['existe'] = true;
            break;
        }
    } catch (Exception $e) {
        throw new Exception("Une exception de comptage a été levée : " . $e->getMessage());
    }
}
?>