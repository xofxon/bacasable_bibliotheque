<?php
require_once 'bibliotheque_informationsdecons.php';

$action = $_POST['action'] ?? null;
$id = $_POST['id'] ?? null; // Utilisation de l'ID comme critère

if ($action === 'Creer' || $action === 'Modifier') {
    // Utilisation de REPLACE pour créer ou modifier une série
    $query = $pdo->prepare("
        REPLACE INTO `series` (`IDSeries`, `Nom`, `Adresse`, `Etat`, `DateRecuperation`, `Commentaire`) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $query->execute([
        $id, 
        $_POST['nom'], 
        $_POST['adresse'], 
        $_POST['etat'], 
        $_POST['date'] . ' ' . $_POST['heure'], 
        $_POST['commentaire']
    ]);
    echo ($action === 'Creer') ? "Série créée avec succès." : "Série modifiée avec succès.";
} 
elseif ($action === 'Supprimer') {
    // Suppression d'une série en fonction de l'ID
    $query = $pdo->prepare("DELETE FROM `series` WHERE `IDSeries` = ?");
    $query->execute([$id]);
    echo "Série supprimée avec succès.";
}
?>
