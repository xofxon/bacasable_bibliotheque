<?php
require_once 'bibliotheque_informationsdecons.php';
// Récupération des paramètres
$perimetre = $_GET['perimetre'] ?? 'Consultation';
$idSerie = $_GET['id'] ?? 0;
// Chargement des genres pour le formulaire de sélection
$requeteGenres = $pdo->query("SELECT choix_cleunik, choix_valeur_texte_01 FROM _choix WHERE critere_cleunik = 161");
$genres = $requeteGenres->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($perimetre) ?> d'un Livre</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<div style="display: flex; align-items: center;">    
<h2><?= ucfirst($perimetre) ?> de la série (<?= $idSerie ?>)</h2>
<div class="button-container" style="margin-left: auto;">
    <button type="button" id="retourBtn">Retour</button>
    <?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
        <button type="button" id="enregistrerBtn">Enregistrer</button>
    <?php endif; ?>
    <?php if ($perimetre === 'Creation' || $perimetre === 'Modification' || $perimetre === 'Consultation'): ?>    
        <button type="button" id="resetBtn">Réinitialiser</button>
        <?php endif; ?>
    <?php if ($perimetre === 'Suppression'): ?>
        <button type="button" id="enregistrerBtn">Confirmer la Suppression</button>
    <?php endif; ?>
</div>
</div>    
<form id="serieFormulaire" method="POST">
    <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" maxlength="150" required<?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
    </div>
    <div class="form-group">
        <label for="site">Site :</label>
        <input type="url" id="site" name="site" placeholder="https://bedetheque.com" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
        <button type="button" onclick="pa_ouvreLeSite(document.getElementById('site').value)">Ouvrir dans un nouvel onglet</button>
    </div>
    <div class="form-group">
        <label for="etat">État :</label>
        <select id="etat" name="etat" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'disabled' : '' ?>>
            <option value="1">Incomplete</option>
            <option value="2">Complète</option>
        </select>
        <label for="genre">Genre :</label>
        <select id="genre" name="genre" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'disabled' : '' ?>>
            <?php foreach ($genres as $g): ?>
                <option value="<?= $g['choix_cleunik'] ?>"><?= $g['choix_valeur_texte_01'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
    <label for="commentaire">Commentaires :</label>
    <textarea id="commentaire" name="commentaire" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>></textarea>
    </div>
    <div class="form-group">
        <label for="presents">Présents :</label>
        <input type="text" id="presents" name="presents" readonly >
        <label for="courses">Courses :</label>
        <input type="text" id="courses" name="courses" readonly >

    </div>

    <div class="tags-container" id="tagsContainer" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
    
    </div>
</form>
<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque_la_serie.js.php?v=<?= time(); ?>&idSerie=<?= $idSerie ?>&perimetre=<?= $perimetre ?>"></script>
<script src="bibliotheque_js/bibliotheque_menu.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
