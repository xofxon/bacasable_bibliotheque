<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Fichiers Importés</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<?php include 'bibliotheque_include_modale.php';?>
    <div style="display: flex; flex: 1; flex-direction: column; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; height: auto; flex: 0;">
            <button id="importCSVButton">Importer un fichier CSV</button>
            <button id="TraitementCSVButton">Re-traiter le contenu d'un fichier CSV</button>
            <button id="refreshButton">Rafraîchir la table</button>
        </div>
        <table class="tabletriable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du Fichier</th>
                    <th>Date d'Importation</th>
                    <th>Caractéristiques</th>
                    <th>Type de Fichier</th>
                </tr>
            </thead>
            <tbody id="fichiersTableBody"></tbody>
        </table>
    </div>

    <div style="flex: 1; overflow-y: auto;">
        <h2>Détail du Fichier Sélectionné</h2>
        <div id="fichierDetail">
            <p>Sélectionnez un fichier pour voir les détails.</p>
        </div>
    </div>

<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_fichiersimportes.js?v=<?= time(); ?>"></script>

</body>
</html>
