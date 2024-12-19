<?php
require_once 'bibliotheque_informationsdecons.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associer les Livres aux séries</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<!-- Boutons au-dessus de la table -->
<?php include 'bibliotheque_include_modale.php';?>
<button class="menu-button" onclick="pl_OuvrePSeudoMenuContextuel(event)">Actions diverses</button>
<div class="context-menu" id="contextMenu">
    <div onclick="pa_afficheOuCacheLesFiltres()">Afficher/cacher les filtres</div>
    <div onclick="pl_chargerLivresTable()">Rafraîchir les livres sans association</div>
    <div onclick="pa_chargerSeriesTable()">Rafraîchir les séries</div>
    <div onclick="pl_afficheoumasqueLaDescription(2)">Masquer/Afficher la description</div>
</div>
<div class="tags-container" id="tagsContainer">
    
</div>
<div class="table-container">
<div class="container">
    <div class="tables-row">
        <div class="table-container">
            <table id="bibliotheque_TableDesLivres" class="TableDesLivres tabletriable tablefiltrable">
                <thead>
                    <tr>
                        <th style="display:none;">ID</th> <!-- Colonne ID cachée -->
                        <th style="width: 35%;">Titre</th>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 20%;">Auteurs</th>
                        <th style="width: 5%;">ISBN13</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les lignes seront générées dynamiquement par le JavaScript -->
                </tbody>
            </table>
        </div>

        <div class="separator"></div>

        <div class="table-container">
            <table id="bibliotheque_seriesTable" class="TableDesSeries tabletriable">
                <thead>
                    <tr>
                        <th style="display:none;">Idseries</th> <!-- Colonne ID cachée -->
                        <th style="width: 40%;">Nom</th>
                        <th style="width: 40%;">Site</th>
                        <th style="width: 20%;">État</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les lignes seront générées dynamiquement par le JavaScript -->
                </tbody>
            </table>

    </div>
</div>
</div>



    <!-- Désactiver le cache en ajoutant un timestamp aux fichiers JS -->
<script>
<?php
echo "const qui='". $quiSuisJe. "';";
echo "const genre=110146;";
echo "const condition=4;";
?>
</script>
<script src="bibliotheque_js/bibliotheque_associer_les_livres_aux_series.js.php?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_les_boutons.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
