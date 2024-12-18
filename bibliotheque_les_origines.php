<?php
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
<h2 id="originesNombre"></h2>
<!-- Boutons au-dessus de la table -->
<div class="button-container">
    <button class="Bouton_Ajouter add-tab-btn" id="newOrigineBtn" title="Nouvelle origine" data-titre="Ajouter une origine" aria-label="Ajouter une origine">Nouvelle origine</button>
    <button class="Bouton_Consulter add-tab-btn" id="consultOrigineBtn" title="Consulter une origine" data-titre="Consulter une origine" aria-label="Consulter une origine">Consultation origine</button>
    <button class="Bouton_Modifier add-tab-btn" id="modifyOrigineBtn" title="Modifier une origine" data-titre="Modifier une origine" aria-label="Modifier une origine">Modification origine</button>
    <button class="Bouton_Supprimer add-tab-btn" id="deleteOrigineBtn" title="Supprimer une origine" data-titre="Supprimer une origine" aria-label="Supprimer une origine">Suppression origine</button>
</div>
<div class="table-container">
<table id="bibliotheque_TableDesorigines" class="TableDesOrigines">
    <thead>
        <tr>
            <th style="display:none;">IDOrigines</th> <!-- Colonne ID cachée -->
            <th>Nom</th>
            <th>Adresse</th>
            <th>CodeInterne</th>
            <th>Etat</th>
            <th>Gestion</th>
        </tr>
    </thead>
    <tbody>
         <!-- Les lignes seront générées dynamiquement par le JavaScript -->
    </tbody>
</table>
<div>
<?php include 'bibliotheque_include_modale.php';?>
<!-- Désactiver le cache en ajoutant un timestamp aux fichiers JS -->
<script src="bibliotheque_js/bibliotheque_les_origines.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>