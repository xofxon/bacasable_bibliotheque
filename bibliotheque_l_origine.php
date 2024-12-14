<?php
require_once 'bibliotheque_informationsdecons.php';
// Récupération des paramètres
$perimetre = $_GET['perimetre'] ?? 'Consultation';
$idOrigine = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($perimetre) ?> d'une origine</title>
    <?php include 'bibliotheque_include_styles.php';?>
</head>
<body>
<h2><?= ucfirst($perimetre) ?> de l'origine</h2>

<form id="origineFormulaire" method="POST">
    <div class="form-group">
        <label for="CodeInterne">Code interne :</label>
        <input type="text" id="CodeInterne" name="CodeInterne" maxlength="10" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
    </div>
    <div class="form-group">
        <label for="Nom">Nom :</label>
        <input type="text" id="Nom" name="nomtitre" maxlength="100" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
    </div>
    <div class="form-group">
    <label for="Adresse">Adresse :</label>
    <input type="text" id="Adresse" name="adresse" maxlength="255" <?= $perimetre === 'Consultation' || $perimetre === 'Suppression' ? 'readonly' : '' ?>>
    </div>
    <div class="form-group">
    <label for="Etat">État :</label>
    <?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
        <select id="Etat" name="etat">
            <option value="0">Actif</option>
            <option value="1">Inactif</option>
        </select>
    <?php else: ?>
        <input type="text" id="Etat" name="etat" readonly>
    <?php endif; ?>
    <?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
    <?php else: ?>
        <label for="Gestion">Gestion :</label>
        <input type="text" id="Gestion" name="gestion" readonly>
    <?php endif; ?>
    </div>
    <button type="button" id="retourBtn">Retour</button>
    <?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
        <button type="button" id="enregistrerBtn">Enregistrer</button>
        <button type="button" id="resetBtn">Réinitialiser</button>
    <?php elseif ($perimetre === 'Suppression'): ?>
        <button type="button" id="enregistrerBtn">Confirmer la Suppression</button>
    <?php endif; ?>
</form>
<?php include 'bibliotheque_include_modale.php';?>
<script src="bibliotheque_js/bibliotheque_l_origine.js.php?v=<?= time(); ?>&perimetre=<?= $perimetre ?>&idOrigine=<?= $idOrigine ?>"></script>
<script src="bibliotheque_js/bibliotheque_menu.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque.js?v=<?= time(); ?>"></script>
<script src="bibliotheque_js/bibliotheque_modale.js?v=<?= time(); ?>"></script>
</body>
</html>
