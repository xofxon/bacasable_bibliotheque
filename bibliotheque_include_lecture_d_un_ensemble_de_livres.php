<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération des données JSON envoyées
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true); // Le deuxième argument 'true' indique que l'on veut un tableau associatif
        if ($data !== null) {
            // Le tableau d'identifiants est maintenant accessible dans $data['selectedIds']
            $selectedIds = $data['selectedIds'];
        }    
        else {
            throw new Exception('Erreur lors du décodage du JSON.');
        }
    } else {
        throw new Exception('Aucun ID sélectionné.');
    }
    $perimetre = $_GET['perimetre'] ?? 'ISBN13';
    if (empty($selectedIds)) {
        throw new Exception('Aucun ID sélectionné.');
    }
    // Vérification du type des identifiants (si nécessaire)
    foreach ($selectedIds as $id) {
        if (!is_numeric($id)) {
            throw new Exception("Tous les identifiants doivent être numériques.");
        }
    }
    $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
    $query = $pdo->prepare("SELECT IDLivres, Titre, ISBN13, ISBN10 FROM livres WHERE IDLivres IN ($placeholders)");
    $query->execute($selectedIds);
    $livres = $query->fetchAll(PDO::FETCH_ASSOC);
    if (empty($livres)) {
        throw new Exception('Aucun livre trouvé pour les IDs sélectionnés.');
    }
    $Lues=0;
    $Trouvees=0;
    $Maj=0;
?>