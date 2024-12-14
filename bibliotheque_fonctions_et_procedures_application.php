<?php
function fa_compte($table, $condition = '') {
    global $pdo; // Utiliser la connexion PDO depuis le fichier inclus

    try {
        // Construire la requête SQL
        $sql = "SELECT COUNT(*) AS total FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }

        // Préparer et exécuter la requête
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Récupérer le résultat
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return (int)$result['total'];
        } else {
            //  On traitera le -1 dans le script appelant
            return -1;
        }
    } catch (PDOException $e) {
        //  echo "Erreur : " . $e->getMessage();
        //  On traitera le -1 dans le script appelant
        return -1;
    }
}
function fa_isValidISBN13($isbn) {
    $isbn = str_replace('-', '', $isbn);
    if (strlen($isbn) !== 13) return false;

    $sum = 0;
    for ($i = 0; $i < 13; $i++) {
        $factor = ($i % 2 === 0) ? 1 : 3;
        $sum += (int)$isbn[$i] * $factor;
    }
    
    return $sum % 10 === 0;
}
function fa_isValidISBN10($isbn) {
    $isbn = str_replace('-', '', $isbn);
    if (strlen($isbn) !== 10) return false;

    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
        if ($isbn[$i] === 'X') {
            $sum += 10 * ($i + 1);
        } else {
            $sum += (int)$isbn[$i] * ($i + 1);
        }
    }
    
    return $sum % 11 === 0;
}

?>
