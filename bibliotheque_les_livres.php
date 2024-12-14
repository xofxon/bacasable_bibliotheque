<?php
require_once 'bibliotheque_informationsdecons.php';
$perimetre = $_GET['perimetre'] ?? 'unitaire';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Livres</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<h2 id="livresNombre"></h2>
<!-- Boutons au-dessus de la table -->
<?php include 'bibliotheque_include_modale.php';?>
<div class="button-container">
<?php if ($perimetre === 'unitaire' || $perimetre === 'listeDesCourses'): ?>
        <button class="Bouton_Ajouter add-tab-btn" id="newBookBtn" title="Nouveau livre" data-titre="Ajouter un livre" aria-label="Ajouter un nouveau livre">Nouveau</button>
        <button class="Bouton_Consulter add-tab-btn" id="consultBookBtn" title="Consulter un livre" data-titre="Consulter un livre" aria-label="Consulter un livre">Consultation</button>
        <button class="Bouton_Modifier add-tab-btn" id="modifyBookBtn" title="Modifier un livre" data-titre="Modifier un livre" aria-label="Modifier un livre">Modification</button>
        <button class="Bouton_Supprimer add-tab-btn" id="deleteBookBtn" title="Supprimer un livre" data-titre="Supprimer un livre" aria-label="Supprimer un livre">Suppression</button>
        <button class="Bouton_Imprimer add-tab-btn" id="printBookBtn" title="Imprimer la liste des livres" data-titre="Imprimer la liste des livres" aria-label="Imprimer la liste des livres">Impression de la liste</button>
<!-- Bouton qui déclenche le menu contextuel -->
<button class="menu-button" onclick="pl_OuvrePSeudoMenuContextuel(event)">Autres actions</button>
<div class="context-menu" id="contextMenu">
    <div onclick="pa_afficheOuCacheLesFiltres()">Afficher/cacher les filtres</div>
    <div onclick="pl_chargerLivresTable()">Rafraîchir</div>
    <div onclick="pl_OuvrirOngletAuKilometre()">Au kilomètre</div>
    <div onclick="pl_afficheoumasqueLaDescription(2)">Masquer/Afficher la description</div>
</div>
<?php else: ?>
    <?php if ($perimetre === 'maListeDesCourses'): ?>
        <button class="menu-button" onclick="pl_OuvrePSeudoMenuContextuel(event)">Autres actions</button>    
        <div class="context-menu" id="contextMenu">
                <div onclick="pa_afficheOuCacheLesFiltres()">Afficher/cacher les filtres</div>
                <div onclick="pl_chargerLivresTable()">Rafraîchir</div>
            </div>
        </div>    
    <?php else: ?>
        <button class="Bouton_Modifier add-tab-btn" id="modifyBookBtn" title="Modifier un livre" data-titre="Modifier un livre" aria-label="Modifier un livre">Modification</button>
        <button class="Bouton_Filtre" id="LanceGoogleISBN13" title="ISBN13->Google" data-titre="ISBN13->Google" aria-label="ISBN13->Google">ISBN13->Google</button>
        <button class="Bouton_Filtre" id="LanceGoogleISBN10" title="ISBN10->Google" data-titre="ISBN10->Google" aria-label="ISBN10->Google">ISBN10->Google</button>
        <button class="Bouton_Filtre" id="LanceGoogleTitre" style="display:none;" title="Titre->Google" data-titre="Titre->Google" aria-label="Titre->Google">Titre->Google</button>
        <button class="Bouton_Filtre" id="LanceAmazonISBN13" title="ISBN13->Amazon" data-titre="ISBN13->Amazon" aria-label="ISBN13->Amazon">ISBN13->Amazon</button>
        <button class="Bouton_Filtre" id="LanceAmazonetgoogleISBN13" title="ISBN13->Google+Amazon" data-titre="ISBN13->Google+Amazon" aria-label="ISBN13->Google+Amazon">ISBN13->Google+Amazon (Nok)</button>
        <button class="menu-button" onclick="pl_OuvrePSeudoMenuContextuel(event)">Autres actions</button>
    <div class="context-menu" id="contextMenu">
        <div onclick='pa_afficheOuCacheLesFiltres()'>Afficher/cacher les filtres</div>
        <div onclick='pl_chargerLivresTable()''>Rafraîchir</div>
        <div onclick='pl_getISBN(1)'>ISBN 13 Api Google, Open Library, Amazon...</div>
        <div onclick='pl_getISBN(2)'>ISBN 10 Api Google, Open Library, Amazon...</div>
    </div>
    <?php endif; ?>
<?php endif; ?>    
</div>
<?php if ($perimetre === 'unitaire'  || $perimetre === 'listeDesCourses'): ?>
<table id="bibliotheque_TableDesLivres" class="TableDesLivres tabletriable tablefiltrable">
    <thead>
        <tr>
            <th style="display:none;">ID</th> <!-- Colonne ID cachée -->
            <th style="width: 10%;">Série</th>
            <th style="width: 20%;">Titre</th>
            <th style="width: 40%;">Description</th>
            <th style="width: 15%;">Auteurs</th>
            <th style="width: 5%;">ISBN13</th>
            <th style="width: 5%;">Date Maj</th>
            <th style="width: 5%;">ISBN10</th>
            <th style="display:none;">État</th>     <!-- Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque (livre + liste de courses) -->
            <th style="display:none;">Genre</th>    <!-- Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque -->
            <th style="display:none;">Référence</th>
        </tr>
    </thead>
    <tbody>
         <!-- Les lignes seront générées dynamiquement par le JavaScript -->
    </tbody>
</table>
<?php else: ?>
    <table id="bibliotheque_TableDesLivres" class="TableDesLivres tabletriable tablefiltrable">
    <thead>
        <tr>
            <th style="display:none;">ID</th> <!-- Colonne ID cachée -->
            <th style="width: 10%;">Série</th>
            <th style="width: 40%;">Titre</th>
            <th style="width: 15%;">Auteurs</th>
            <th style="width: 10%;">ISBN13</th>
            <th style="width: 10%;">Date Maj</th>
            <th style="width: 10%;">Dernière recherche</th>
            <th style="width: 10%;">ISBN10</th>
            <th style="display:none;">État</th>     <!-- Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque (livre + liste de courses) -->
            <th style="display:none;">Genre</th>    <!-- Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque -->
            
        </tr>
    </thead>
    <tbody>
         <!-- Les lignes seront générées dynamiquement par le JavaScript -->
    </tbody>
</table>

<?php endif; ?>   
<!-- Désactiver le cache en ajoutant un timestamp aux fichiers JS -->
<script>
<?php
echo "const qui='". $quiSuisJe. "';";
echo "const genre=". $_GET['genre']. ";";
echo "const condition=". $_GET['condition'] ?? 0 . ";";
?>
</script>
<?php if ($perimetre === 'maListeDesCourses'): ?>
        <script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>    
        <script src="bibliotheque_js/bibliotheque_les_livres.js.php?perimetre=<?=$perimetre?>&v=<?= time(); ?>"></script>
        <script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
    <?php else: ?>
        <script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
        <script src="bibliotheque_js/bibliotheque_menu.js?v=<?= time(); ?>"></script>
        <script src="bibliotheque_js/bibliotheque_les_livres.js.php?perimetre=<?=$perimetre?>&v=<?= time(); ?>"></script>
        <script src="bibliotheque_js/bibliotheque_les_boutons.js?v=<?= time(); ?>"></script>
        <script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
    <?php endif; ?>
</body>
</html>
