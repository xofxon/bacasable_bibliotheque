<?php
// TOutDoux : Vérifier si l'utilisateur a l'autorisation d'accéder aux autres tables et si oui, lesquelles
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les autres tables</title>
    <link rel="stylesheet" href="bibliotheque_css/bibliotheque_styles_communs.css?v=<?= time(); ?>">
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<div class="button-container">
<div>    
    <button class="add-tab-btn" id="lesOrigines" title="Table des origines" data-titre="Gérer les origines" aria-label="Gérer les origines">Gérer les origines</button>
</div>
<div>    
    <button class="add-tab-btn" id="lesFichiersImportes" title="Table des fichiers importés" data-titre="Les fichiers importés" aria-label="Les fichiers importés">Les fichiers importés</button>
</div>

</div>
<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque_autres_tables.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
