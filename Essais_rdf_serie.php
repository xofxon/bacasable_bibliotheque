<?php
require_once 'bibliotheque_informationsdecons.php';

// Vérifier si l'utilisateur a l'autorisation de suppression, modification, ajout
$autorisationSuppression = isset($_SESSION['AutorisationSuppressionBibliotheque']) && $_SESSION['AutorisationSuppressionBibliotheque'];
$autorisationModification = isset($_SESSION['AutorisationModificationBibliotheque']) && $_SESSION['AutorisationModificationBibliotheque'];
$autorisationAjout = isset($_SESSION['AutorisationAjoutBibliotheque']) && $_SESSION['AutorisationAjoutBibliotheque'];

// Requête SQL pour récupérer les données des séries
$srequete = <<<EOD
SELECT
   Idseries,
   Nom,
   Adresse as 'Adresse sur site',
   Commentaire as 'Commentaires',
   CASE WHEN etat = 1 THEN 'Incomplète' ELSE 'Complète' END as 'Etat',
   CASE WHEN `daterecuperation` = '0000-00-00 00:00:00' THEN '' ELSE DATE_FORMAT(daterecuperation, 'Le %d/%m/%Y à %T') END as 'DateRecuperation'
FROM
   `series`
ORDER BY nom;
EOD;

$query = $pdo->query($srequete);
$series = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Séries</title>
    <style>
        /* Styles similaires à la version précédente */
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
        }

        .button-container {
            position: sticky;
            top: 0;
            background-color: white;
            padding: 10px 0;
            z-index: 1000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            cursor: pointer;
            background-color: #f2f2f2;
            position: sticky;
            top: 45px;
        }

        th.sort-asc::after {
            content: ' 🔼';
        }

        th.sort-desc::after {
            content: ' 🔽';
        }

        input[type="text"] {
            width: 90%;
            padding: 5px;
            margin: 10px;
        }

        .modal-background {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1001;
        }

        .modal-buttons {
            text-align: center;
        }

        .modal-buttons button {
            margin: 5px;
        }
    </style>
</head>
<body>
    <h2>Gestion des Séries</h2>

    <div class="button-container">
        <?php if ($autorisationAjout) : ?>
            <button id="add-button">Ajouter une série</button>
        <?php endif; ?>
        <?php if ($autorisationSuppression) : ?>
            <button id="delete-button">Supprimer une série</button>
        <?php endif; ?>
        <!-- Autres boutons si nécessaire -->
    </div>

    <table id="series-table">
        <thead>
            <tr>
                <th data-column="Nom">Titre</th>
                <th data-column="Adresse sur site">Adresse</th>
                <th data-column="Commentaires">Commentaires</th>
                <th data-column="Etat">État</th>
                <th data-column="DateRecuperation">Date de Récupération</th>
                <th>Actions</th>
            </tr>
            <tr>
                <th><input type="text" id="filter-title" placeholder="Filtrer par titre"></th>
                <th><input type="text" id="filter-address" placeholder="Filtrer par adresse"></th>
                <th><input type="text" id="filter-comments" placeholder="Filtrer par commentaires"></th>
                <th><input type="text" id="filter-state" placeholder="Filtrer par état"></th>
                <th><input type="text" id="filter-date" placeholder="Filtrer par date"></th>
                <th></th> <!-- Pas de filtre pour les actions -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($series as $serie) : ?>
                <tr>
                    <td><?= htmlspecialchars($serie['Nom']) ?></td>
                    <td><?= htmlspecialchars($serie['Adresse sur site']) ?></td>
                    <td><?= htmlspecialchars($serie['Commentaires']) ?></td>
                    <td><?= htmlspecialchars($serie['Etat']) ?></td>
                    <td><?= htmlspecialchars($serie['DateRecuperation']) ?></td>
                    <td>
                        <?php if ($autorisationModification) : ?>
                            <button class="action-btn modify">Modifier</button>
                        <?php endif; ?>
                        <?php if ($autorisationSuppression) : ?>
                            <button class="action-btn delete">Supprimer</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="modal-background"></div>
    <div class="modal">
        <h3 id="modal-title">Confirmation</h3>
        <p id="modal-message">Voulez-vous continuer ?</p>
        <div class="modal-buttons">
            <button id="modal-yes">Oui</button>
            <button id="modal-no">Non</button>
            <button id="modal-cancel">Annuler</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gestion du tri des colonnes
            const table = document.getElementById('series-table');
            const headers = table.querySelectorAll('th[data-column]');
            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.getAttribute('data-column');
                    const isAscending = this.classList.contains('sort-asc');
                    sortTable(column, isAscending ? 'desc' : 'asc');
                    this.classList.toggle('sort-asc', !isAscending);
                    this.classList.toggle('sort-desc', isAscending);
                });
            });

            function sortTable(column, order) {
                const rowsArray = Array.from(table.querySelectorAll('tbody tr'));
                rowsArray.sort((rowA, rowB) => {
                    const cellA = rowA.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;
                    const cellB = rowB.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;
                    return order === 'asc' ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                });
                rowsArray.forEach(row => table.querySelector('tbody').appendChild(row));
            }

            function getColumnIndex(columnName) {
                const thArray = Array.from(headers);
                return thArray.findIndex(th => th.getAttribute('data-column') === columnName) + 1;
            }
            const filters = {
                title: document.getElementById('filter-title'),
                address: document.getElementById('filter-address'),
                comments: document.getElementById('filter-comments'),
                state: document.getElementById('filter-state'),
                date: document.getElementById('filter-date')
            };

            // Ajout d'un événement "input" sur chaque champ de filtre
            for (let key in filters) {
                filters[key].addEventListener('input', filterTable);
            }

            function filterTable() {
                const rows = document.querySelectorAll('#series-table tbody tr');
                rows.forEach(row => {
                    const title = row.children[0].textContent.toLowerCase();
                    const address = row.children[1].textContent.toLowerCase();
                    const comments = row.children[2].textContent.toLowerCase();
                    const state = row.children[3].textContent.toLowerCase();
                    const date = row.children[4].textContent.toLowerCase();

                    const filterTitle = filters.title.value.toLowerCase();
                    const filterAddress = filters.address.value.toLowerCase();
                    const filterComments = filters.comments.value.toLowerCase();
                    const filterState = filters.state.value.toLowerCase();
                    const filterDate = filters.date.value.toLowerCase();

                    // Vérifie si la ligne correspond aux filtres
                    const isVisible =
                        title.includes(filterTitle) &&
                        address.includes(filterAddress) &&
                        comments.includes(filterComments) &&
                        state.includes(filterState) &&
                        date.includes(filterDate);

                    row.style.display = isVisible ? '' : 'none';
                });
            }



            // Ajout des boutons modaux
            const modalBackground = document.querySelector('.modal-background');
            const modal = document.querySelector('.modal');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const modalYes = document.getElementById('modal-yes');
            const modalNo = document.getElementById('modal-no');
            const modalCancel = document.getElementById('modal-cancel');

            function fa_showModal(title, message, callback) {
                modalTitle.textContent = title;
                modalMessage.textContent = message;
                modalBackground.style.display = 'block';
                modal.style.display = 'block';

                modalYes.onclick = () => { callback(1); pa_closeModal(); };
                modalNo.onclick = () => { callback(2); pa_closeModal(); };
                modalCancel.onclick = () => { callback(3); pa_closeModal(); };
            }

            function pa_closeModal() {
                modalBackground.style.display = 'none';
                modal.style.display = 'none';
            }

            document.getElementById('delete-button').addEventListener('click', function() {
                fa_showModal('Confirmation', 'Êtes-vous sûr de vouloir supprimer cette série ?', function(result) {
                    console.log('Résultat du choix :', result);
                    // Traiter le choix ici
                });
            });
        });
    </script>
</body
