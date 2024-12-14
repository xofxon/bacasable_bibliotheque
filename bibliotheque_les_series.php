<?php
require_once 'bibliotheque_informationsdecons.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Séries</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>

<?php
// Nombre d'enregistrements et date/heure de la requête
$nombre_enregistrements = 0;
$date_requete = '23-01-1961 à 5h';
?>
<h2 id="seriesNombre">Liste des Séries (<?= $nombre_enregistrements ?> enregistrements) - Requête effectuée le <?= $date_requete ?></h2>

<!-- Boutons au-dessus de la table -->
<div class="button-container">
    <button class="Bouton_Ajouter add-tab-btn" id="newSerieBtn" title="Nouvelle série" data-titre="Ajouter une série" aria-label="Ajouter une nouvelle série">Nouvelle série</button>
    <button class="Bouton_Consulter add-tab-btn" id="consultSerieBtn" data-titre="Consulter une série" title="Consulter une série" aria-label="Consulter une série">Consultation série</button>
    <button class="Bouton_Modifier add-tab-btn" id="modifySerieBtn" title="Modifier une série" data-titre="Modifier une série" aria-label="Modifier une série">Modification série</button>
    <button class="Bouton_Supprimer add-tab-btn" id="deleteSerieBtn" title="Supprimer une série" data-titre="Supprimer une série" aria-label="Supprimer une série">Suppression série</button>
    <button class="Bouton_Imprimer add-tab-btn" id="printSerieBtn" title="Imprimer la liste des séries" data-titre="Imprimer la liste des séries" aria-label="Imprimer la liste des séries">Impression de la liste</button>
    <button class="Bouton_Filtre" onclick="pa_afficheOuCacheLesFiltres()" title="Afficher/Cacher les filtres" aria-label="Afficher ou cacher les filtres">Afficher/Cacher les filtres</button>
    <button class="Bouton_Filtre" onclick="pa_chargerSeriesTable();" title="Rafraîchir" aria-label="Rafraîchir">Rafraîchir</button>
</div>

<table id="bibliotheque_seriesTable" class="TableDesSeries tabletriable">
    <thead>
        <tr>
            <th style="display:none;">Idseries</th> <!-- Colonne ID cachée -->
            <th>Nom</th>
            <th>Site</th>
            <th>Présents</th>
            <th>Courses</th>
            <th>État</th>
            <th>Commentaires</th>
        </tr>
    </thead>
    <tbody>
        <!-- Les lignes seront générées dynamiquement par le JavaScript -->
    </tbody>
</table>
<?php include 'bibliotheque_include_modale.php';?>
<!-- Désactiver le cache en ajoutant un timestamp aux fichiers JS -->
<script src="bibliotheque_js/bibliotheque_les_series.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_les_boutons.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
