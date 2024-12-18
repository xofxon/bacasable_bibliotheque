<?php
require_once 'bibliotheque_informationsdecons.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Bibliothèque</title>
    <?php include 'bibliotheque_include_styles.php';?>
    <link rel="stylesheet" href="bibliotheque_css/bibliotheque_onglets.css?v=<?= time(); ?>">
   
</head>
<?php
require_once 'bibliotheque_include_ga.php';
?>
<body style="overflow: hidden;">
<!-- Conteneur pour les onglets -->
<div class="tab-container" id="tab-container"></div>
<!-- Conteneur pour le contenu des onglets -->
<div id="tab-content-container"></div>
<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_menu.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
