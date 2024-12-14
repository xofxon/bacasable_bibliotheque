<?php
session_start();

// Rediriger si les variables de session n'existent pas
if (!isset($_SESSION['AutorisationSuppressionBibliotheque']) || 
    !isset($_SESSION['AutorisationModificationBibliotheque']) || 
    !isset($_SESSION['AutorisationAjoutBibliotheque'])) {
    header('Location: https://www.google.com/maps/space/mars/');
    exit;
}
require_once 'bibliotheque_informationsdecons.php';

// Vérification de l'état de l'enregistrement (éviter doublons)
$serieId = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'consultation'; // consultation par défaut
$serieBloquee = isset($_SESSION['SerieEnCours']) && $_SESSION['SerieEnCours'] == $serieId && ($action == 'modification' || $action == 'suppression');

if ($serieBloquee) {
    $blocageMessage = "Enregistrement déjà en cours de modification ou suppression.";
}

if ($serieId && ($action == 'modification' || $action == 'suppression')) {
    $_SESSION['SerieEnCours'] = $serieId;
}

// Récupérer les informations de la série si nécessaire
$serieData = null;
if ($serieId) {
    $stmt = $pdo->prepare("SELECT * FROM `series` WHERE `IDSeries` = ?");
    $stmt->execute([$serieId]);
    $serieData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Déterminer l'action et le libellé associé
switch ($action) {
    case 'consultation':
        $libelle = 'Consultation';
        break;
    case 'creation':
        $libelle = 'Création';
        break;
    case 'modification':
        $libelle = 'Modification';
        break;
    case 'suppression':
        $libelle = 'Suppression';
        break;
    default:
        $libelle = 'Consultation';
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $libelle ?> de la Série</title>
    <style>
        /* Styles intégrés */
        body {
            font-family: Arial, sans-serif;
        }

        .button-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .button-bar button {
            padding: 10px;
            margin: 5px;
            cursor: pointer;
        }

        .info-bar {
            text-align: right;
            font-weight: bold;
            padding: 10px;
        }

        .info-bar.clignotant {
            animation: clignotement 1s linear infinite;
        }

        @keyframes clignotement {
            50% {
                opacity: 0;
            }
        }

        .form-field {
            margin-bottom: 10px;
        }

        .form-field label {
            display: block;
            font-weight: bold;
        }

        .form-field input, .form-field textarea {
            width: 100%;
            padding: 5px;
            font-size: 1em;
        }

        .form-field textarea {
            height: 100px;
        }

        .modal {
            display: none;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .modal-buttons {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2><?= $libelle ?> de la Série</h2>
    
    <div class="button-bar">
        <?php if ($action != 'consultation') : ?>
            <button id="btn-raz">RAZ</button>
            <button id="btn-validation"><?= $libelle ?></button>
        <?php endif; ?>
        <button id="btn-sortir">Sortir</button>
    </div>

    <div class="info-bar <?= $serieBloquee ? 'clignotant' : '' ?>">
        <?= $serieBloquee ? "Modification en cours..." : $libelle ?>
    </div>

    <form id="serie-form">
        <div class="form-field">
            <label for="bibliotheque_serie_nom">Nom de la série</label>
            <input type="text" id="bibliotheque_serie_nom" name="nom" value="<?= htmlspecialchars($serieData['Nom'] ?? '') ?>" <?= $action == 'consultation' ? 'readonly' : '' ?>>
        </div>

        <div class="form-field">
            <label for="bibliotheque_serie_adresse">Site de référence</label>
            <input type="text" id="bibliotheque_serie_adresse" name="adresse" value="<?= htmlspecialchars($serieData['Adresse'] ?? '') ?>" <?= $action == 'consultation' ? 'readonly' : '' ?>>
        </div>

        <div class="form-field">
            <label for="bibliotheque_serie_etat">État</label>
            <input type="number" id="bibliotheque_serie_etat" name="etat" value="<?= htmlspecialchars($serieData['Etat'] ?? 0) ?>" <?= $action == 'consultation' ? 'readonly' : '' ?>>
        </div>

        <div class="form-field">
            <label for="bibliotheque_serie_etat_date">Date</label>
            <input type="date" id="bibliotheque_serie_etat_date" name="date" value="<?= isset($serieData['DateRecuperation']) ? substr($serieData['DateRecuperation'], 0, 10) : '' ?>" <?= $action == 'consultation' ? 'readonly' : '' ?>>
        </div>

        <div class="form-field">
            <label for="bibliotheque_serie_etat_heure">Heure</label>
            <input type="time" id="bibliotheque_serie_etat_heure" name="heure" value="<?= isset($serieData['DateRecuperation']) ? substr($serieData['DateRecuperation'], 11) : '' ?>" <?= $action == 'consultation' ? 'readonly' : '' ?>>
        </div>

        <div class="form-field">
            <label for="bibliotheque_serie_commentaire">Commentaires</label>
            <textarea id="bibliotheque_serie_commentaire" name="commentaire" <?= $action == 'consultation' ? 'readonly' : '' ?>><?= htmlspecialchars($serieData['Commentaire'] ?? '') ?></textarea>
        </div>
    </form>

    <?php include 'bibliotheque_include_modale.php'; ?>

    <script src="bibliotheque_les_series_crud.js"></script>
</body>
</html>
