<?php
require_once 'bibliotheque_informationsdecons.php';

// Récupération des paramètres
$perimetre = $_GET['perimetre'] ?? 'Consultation';
$idLivre = $_GET['id'] ?? 0;
$genre = $_GET['genre'] ?? null;
$perimetreDOrigine = $_GET['perimetreDOrigine'] ?? ''; // Valeur par défaut si $perimetre n'est pas passé
// Chargement des genres pour le formulaire de sélection
$requeteGenres = $pdo->query("SELECT choix_cleunik, choix_valeur_texte_01 FROM _choix WHERE critere_cleunik = 161");
$genres = $requeteGenres->fetchAll(PDO::FETCH_ASSOC);
// Chargement des origines pour le formulaire de sélection
$requeteOrigines = $pdo->query("SELECT IDOrigines, Nom FROM origines");
$origines = $requeteOrigines->fetchAll(PDO::FETCH_ASSOC);

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
<?php include 'bibliotheque_include_modale.php';?>    
<div style="display: flex; align-items: center;">    
<h2><?= ucfirst($perimetre) ?> du livre (<?= $idLivre ?>)</h2>
<div class="button-container" style="margin-left: auto;">
    <button type="button" id="retourBtn">Retour</button>
    <?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
        <button type="button" id="enregistrerBtn">Enregistrer</button>
        <button type="button" id="resetBtn">Réinitialiser</button>
    <?php elseif ($perimetre === 'Suppression'): ?>
        <button type="button" id="enregistrerBtn">Confirmer la Suppression</button>
    <?php endif; ?>
</div>
</div> 
<form id="livreFormulaire" method="POST">
    <div class="form-group">
        <label for="isbn13">ISBN13 :</label>
        <input type="text" required id="isbn13" name="isbn13" maxlength="23" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
        <button type="button" onclick="pl_LanceGoogleISBN13()" class="Bouton_Filtre" id="LanceGoogleISBN13" title="ISBN13->Google" data-titre="ISBN13->Google" aria-label="ISBN13->Google">ISBN13->Google</button>
        <button type="button" class="Bouton_Filtre" id="LanceGoogleTitre" style="display:none;" title="Titre->Google" data-titre="Titre->Google" aria-label="Titre->Google">Titre->Google</button>
        <button type="button" onclick="pl_LanceAmazonISBN13()" class="Bouton_Filtre" id="LanceAmazonISBN13" title="ISBN13->Amazon" data-titre="ISBN13->Amazon" aria-label="ISBN13->Amazon">ISBN13->Amazon</button>
        <button type="button" onclick="pl_LanceAmazonetgoogleISBN13()" class="Bouton_Filtre" id="LanceAmazonetgoogleISBN13" title="ISBN13->Google+Amazon" data-titre="ISBN13->Google+Amazon" aria-label="ISBN13->Google+Amazon">ISBN13->Google+Amazon (Nok)</button>
        <label for="isbn10">ISBN10 :</label>
        <input type="text" required id="isbn10" name="isbn10" maxlength="20" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
        <button type="button" onclick="pl_LanceGoogleISBN10()" class="Bouton_Filtre" id="LanceGoogleISBN10" title="ISBN10->Google" data-titre="ISBN10->Google" aria-label="ISBN10->Google">ISBN10->Google</button>
        <button type="button" onclick="pl_LanceAmazonISBN10(document.getElementById('isbn10').value)" class="Bouton_Filtre" id="LanceAmazonISBN10" title="ISBN10->Amazon" data-titre="ISBN10->Amazon" aria-label="ISBN10->Amazon">ISBN10->Amazon</button>
    </div>
    <div class="form-group">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" maxlength="150" required<?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
        <label for="auteurs">Auteurs :</label>
        <input type="text" id="auteurs" name="auteurs" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?> >
    </div>
    <div class="form-group">
        <label for="genre">Origine :</label>
        <select id="idorigines" name="idorigines" disabled>
            <?php foreach ($origines as $o): ?>
                <option value="<?= $o['IDOrigines'] ?>"><?= $o['Nom'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="olid">Identifiant Importation :</label>
        <input type="text" id="olid" name="olid" maxlength="15" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' || $perimetre === 'Modification' ? 'readonly' : '' ?>>
        <label id="forlienReference" for="lienReference">Référence  :</label>
        <input type="url" id="lienReference" name="lienReference" class="pseudoLien">
        
    </div>
    <div class="form-group">
        <label for="etat">État :</label>
        <select id="etat" name="etat" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'disabled' : '' ?>>
            <option value="1">Présent</option>
            <option value="0">Liste de courses</option>
        </select>
        <label for="genre">Genre :</label>
        <select id="genre" name="genre" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'disabled' : '' ?>>
            <?php foreach ($genres as $g): ?>
                <option value="<?= $g['choix_cleunik'] ?>" <?= $g['choix_cleunik'] == $genre ? 'selected' : '' ?>><?= $g['choix_valeur_texte_01'] ?></option>
            <?php endforeach; ?>
        </select>
        <label for="daterecuperation">Date de Mise à jour :</label>
        <input type="datetime-local" id="daterecuperation" name="daterecuperation" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
    </div>
    <div class="form-group">
    <label for="description">Description :</label>
    <textarea id="description" name="description" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>></textarea>
    </div>
    <div class="form-group">
    <label for="commentaire">Commentaires :</label>
    <textarea id="commentaire" name="commentaire" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>></textarea>
    </div>
    <?php if ($perimetre === 'Creation'): ?>
    <?php else: ?>
        <div class="form-group-dessus">
            <div class="textarea-group">
                <label for="jsonOutput">JSON importé:</label>
                <textarea class="textarea-proportionnelle" id="jsonOutput" name="jsonOutput" rows="20" cols="125" readonly></textarea>
            </div>
            <div class="textarea-group">
                <label for="jsonInput">JSON modifié:</label>
                <textarea class="textarea-proportionnelle" id="jsonInput" name="jsonInput" rows="20" cols="125" readonly></textarea>
            </div>
        </div>
    <?php endif; ?>
</form>
<?php if ($perimetre === 'Creation'): ?>
<?php else: ?>
    <div class="form-group" id="groupeDeLaSerie">
            <label for="serie">Série :</label>
            <input type="texte" id="serie" name="serie" readonly>
            <label for="site">Site :</label>
            <input type="url" id="site" name="site" readonly>
            <button type="button" onclick="pa_ouvreLeSite(document.getElementById('site').value)">Ouvrir dans un nouvel onglet</button>
        </div>
        <!-- Toutdoux Pouvoir placer le tagsContainer n'importe où.
         Pour l'instant (13/12/2024), tous les objets HTML suivants sont invisibles BUG programmation...
        -->
    <div class="tags-container" id="tagsContainer" readonly>
    <div>    
<?php endif; ?>
<script src="bibliotheque_js/bibliotheque_le_livre.js.php?v=<?= time(); ?>&perimetre=<?= $perimetre ?>&idLivre=<?= $idLivre ?>&genre=<?= $genre ?>&perimetreDOrigine=<?= $perimetreDOrigine ?>"></script>
<script src="bibliotheque_js/bibliotheque_menu.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
