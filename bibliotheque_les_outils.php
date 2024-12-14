<?php
require_once 'bibliotheque_informationsdecons.php';
// ToutdouxVérifier si l'utilisateur a les autorisations nécessaires
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les origines</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<div class="button-container">
<div>    
    <button class="add-tab-btn" id="km2" title="KM BD avec ISBN13 invalides et ISBN10 valides" data-titre="KM BD avec ISBN13 invalides et ISBN10 valides" aria-label="KM BD avec ISBN13 invalides et ISBN10 valides">KM BD avec ISBN13 invalides et ISBN10 valides</button>
</div>
<div>    
    <button class="add-tab-btn" id="km3" title="KM BD avec ISBN13 valides et ISBN10 invalides" data-titre="KM BD avec ISBN13 valides et ISBN10 invalides" aria-label="KM BD avec ISBN13 valides et ISBN10 invalides">KM BD avec ISBN13 valides et ISBN10 invalides</button>
</div>
<div>    
    <button class="add-tab-btn" id="getISBN" title="Recherche ISBN Google API, OpenLibrairy API,..." data-titre="Recherche ISBN Google API, OpenLibrairy API,..." aria-label="Recherche ISBN Google API, OpenLibrairy API,...">Recherche ISBN Google API, OpenLibrairy API,...</button>
</div>
<div>
    <a title="Recherche avancée dans Google books" data-titre="Recherche avancée dans Google books" aria-label="Recherche avancée dans Google books" href="https://books.google.com/advanced_book_search?hl=fr" target="_blank">https://books.google.com/advanced_book_search?hl=fr</a>
</div>
<div>
    <a title="Nudger Comparateur écologique en ligne" data-titre="Nudger Comparateur écologique en ligne" aria-label="Nudger Comparateur écologique en ligne" href="https://nudger.fr/" target="_blank">https://nudger.fr/ Comparateur écologique en ligne</a>
</div>
<div>
    <a title="bedetheque.com Une référence de la bd" data-titre="bedetheque.com Une référence de la bd" aria-label="bedetheque.com Une référence de la bd" href="https://www.bedetheque.com/" target="_blank">https://www.bedetheque.com/ Une référence de la bd</a>
</div>
<div>
    <a title="Open Library est un catalogue de bibliothèque ouvert" data-titre="Open Library est un catalogue de bibliothèque ouvert" aria-label="Open Library est un catalogue de bibliothèque ouvert" href="https://openlibrary.org/" target="_blank">https://openlibrary.org// Open Library est un catalogue de bibliothèque ouvert</a>
</div>

<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque_outils.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
