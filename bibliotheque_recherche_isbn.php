<?php
require_once 'bibliotheque_informationsdecons.php';
// Chargement des genres pour le formulaire de sélection
$genre=-110146;
$requeteGenres = $pdo->query("SELECT choix_cleunik, choix_valeur_texte_01 FROM _choix WHERE critere_cleunik = 161");
$genres = $requeteGenres->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche ISBN avec l'API Google Books</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
    <h1>Recherche de livre par ISBN 13 (ou 10)</h1>
    <div>
    <input type="text" id="isbn" placeholder="Entrez un ISBN 13 (ou 10)" autocomplete="off">
    <button id='getISBN_ws01' title="Rechercher pour la bibliothèque;" data-titre="Rechercher pour la bibliothèque;" aria-label="Rechercher pour la bibliothèque;">Rechercher pour la bibliothèque.</button>
    <button id='getISBN_ws02' title="Rechercher pour les courses." data-titre="Rechercher pour les courses." aria-label="Rechercher pour les courses.">Rechercher pour les courses.</button>
    <button type="button" id="enregistrerBtn">Enregistrer</button>
    <button type="button" id="resetBtn">Réinitialiser</button>
    </div>

    <form id="livreFormulaire" method="POST">
    <div class="form-group">
        <label for="isbn13">ISBN13 :</label>
        <input type="text" id="isbn13" name="isbn13" maxlength="23" >
        <label for="isbn10">ISBN10 :</label>
        <input type="text" id="isbn10" name="isbn10" maxlength="20" >
    </div>
    <div class="form-group">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" maxlength="150" >
    </div>
    <div class="form-group">
        <label for="auteurs">Auteurs :</label>
        <input type="text" id="auteurs" name="auteurs"  >
    </div>
    <div class="form-group">
    <label for="olid">Identifiant :</label>
    <input type="text" id="olid" name="olid" maxlength="15">
    </div>

    <div class="form-group">
    <label for="liencanonique">Lien canonique :</label>
    <input type="text" id="liencanonique">
    </div>
    <div class="form-group">
    <label for="etat">État :</label>
    <input type="text" id="etat" name="etat">
    <label for="genre">Genre :</label>
    <select id="genre" name="genre">
        <?php foreach ($genres as $g): ?>
            <option value="<?= $g['choix_cleunik'] ?>" <?= $g['choix_cleunik'] == $genre ? 'selected' : '' ?>><?= $g['choix_valeur_texte_01'] ?></option>
        <?php endforeach; ?>
    </select>
    <label for="daterecuperation">Date de Mise à jour :</label>
    <input type="datetime-local" id="daterecuperation" name="daterecuperation" >
    </div>
    <div class="form-group">
    <label for="description">Description :</label>
    <textarea id="description" readonly></textarea>
    </div>
    <div class="textarea-group">
        <label for="jsonOutput">JSON importé:</label>
        <textarea class="textarea-proportionnelle" id="jsonOutput" name="jsonOutput" rows="20" cols="125" readonly></textarea>
    </div>
</form>
<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque_recherche_isbn.js.php?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>    
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
</body>
</html>
