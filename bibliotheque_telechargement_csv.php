<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Télécharger un fichier CSV</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
    <h2>Télécharger un fichier CSV</h2>
    <form id="csvForm">
        <label for="csvfile">Choisissez un fichier CSV :</label>
        <input type="file" name="csvfile" id="csvfile" accept=".csv">
        <button type="button" onclick="pl_uploadCSV()">Télécharger et traiter</button>
        <div class="form-group">
    </form>
    <label for="etat">État :</label>
        <select id="etat" name="etat">
            <option value="1">Présent</option>
            <option value="0">Liste de courses</option>
        </select>
    </div>

<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque_telechargement_csv.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
