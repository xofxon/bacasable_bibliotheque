<?php
header("Content-Type: application/javascript");

$action = $_GET['action'] ?? 'Consulter';

switch ($action) {
    case 'Creer':
        echo "
            document.getElementById('serie-form').addEventListener('submit', function (e) {
                e.preventDefault();
                // Code de validation pour création
                alert('Création de la série');
                // TODO: Envoyer les données au serveur pour la création
            });
        ";
        break;

    case 'Modifier':
        echo "
            document.getElementById('serie-form').addEventListener('submit', function (e) {
                e.preventDefault();
                // Code de validation pour modification
                alert('Modification de la série');
                // TODO: Envoyer les données au serveur pour la modification
            });
        ";
        break;

    case 'Supprimer':
        echo "
            document.getElementById('serie-form').addEventListener('submit', function (e) {
                e.preventDefault();
                if (confirm('Confirmer la suppression ?')) {
                    alert('Série supprimée');
                    // TODO: Envoyer les données au serveur pour la suppression
                }
            });
        ";
        break;

    default: // Consultation
        echo "
            document.querySelectorAll('input, textarea').forEach(function (input) {
                input.setAttribute('readonly', 'true');
            });
        ";
        break;
}
?>
