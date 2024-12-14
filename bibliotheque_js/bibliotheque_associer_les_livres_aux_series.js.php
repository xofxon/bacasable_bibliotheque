<?php
// Vous devez vous assurer que la variable $perimetre est définie
header('Content-Type: application/javascript');
?>
const tableLivres = document.querySelector("#bibliotheque_TableDesLivres");
const entetesLivres = tableLivres.querySelectorAll("th");

entetesLivres.forEach((header, index) => {
    if (index === 1 || index === 2 || index === 3 || index === 4) { // Colonnes avec du texte
        let input = document.createElement("input");
        input.type = "text";
        input.placeholder = "Rechercher...";
        input.style.marginTop = "5px";
        input.style.width = "100%";
        input.id = "recherche_livres_" + index;
        header.appendChild(input);

        input.addEventListener("keyup", function () {
            pl_filtrerLaTabledesLivres(index, input.value);
        });
    }
});

const tableSeries = document.querySelector("#bibliotheque_seriesTable");
const entetesSeries = tableSeries.querySelectorAll("th");

entetesSeries.forEach((header, index) => {
    if (index === 1 || index === 2 || index === 3 || index === 4) { // Colonnes avec du texte
        let input = document.createElement("input");
        input.type = "text";
        input.placeholder = "Rechercher...";
        input.style.marginTop = "5px";
        input.style.width = "100%";
        input.id = "recherche_series_" + index;
        header.appendChild(input);

        input.addEventListener("keyup", function () {
            pl_filtrerLaTabledesSeries(index, input.value);
        });
    }
});

const separator = document.querySelector('.separator');
const tableContainers = document.querySelectorAll('.table-container');

let isResizing = false;
let initialX = 0;
let leftWidth = 0;

separator.addEventListener('mousedown', (e) => {
  isResizing = true;
  initialX = e.clientX;
  leftWidth = tableContainers[0].offsetWidth;
});

document.addEventListener('mousemove', (e) => {
  if (!isResizing) return;

  const offsetX = e.clientX - initialX;
  const newLeftWidth = leftWidth + offsetX;

  tableContainers[0].style.width = newLeftWidth + 'px';
  tableContainers[1].style.width = (tableContainers[1].parentElement.offsetWidth - newLeftWidth - separator.offsetWidth) + 'px';
});

document.addEventListener('mouseup', () => {
  isResizing = false;
});





function pl_filtrerLaTabledesLivres(columnIndex, query) {
    const rowsLivres = Array.from(tableLivres.querySelectorAll("tbody tr"));
    if (query.length < 2) {
        rowsLivres.forEach(row => {
            row.style.display = ""; // Réinitialise l'affichage des lignes
            row.children[columnIndex].innerHTML = row.children[columnIndex].textContent; // Réinitialise les contenus des cellules
        });
        return;
    }
    const queryNormalized = query.normalize('NFD').toLowerCase().replace(/[\u0300-\u036f]/g, ""); // Normalise et supprime les accents de la requête

    rowsLivres.forEach(row => {
        const cell = row.children[columnIndex];
        const originalText = cell.textContent; // Conserve le texte original
        const normalizedText = originalText.normalize('NFD').toLowerCase().replace(/[\u0300-\u036f]/g, ""); // Supprime les accents pour comparaison

        if (normalizedText.includes(queryNormalized)) {
            row.style.display = ""; // Affiche la ligne
            const highlightedText = originalText.replace(new RegExp(`(${queryNormalized})`, "gi"), match => `<span class="highlight">${match}</span>`);
            cell.innerHTML = highlightedText; // Remplace le contenu par la version surlignée
        } else {
            row.style.display = "none"; // Masque la ligne
            cell.innerHTML = originalText; // Réinitialise le contenu de la cellule
        }
    });
}

function pl_filtrerLaTabledesSeries(columnIndex, query) {
    const rowsSeries = Array.from(tableSeries.querySelectorAll("tbody tr"));

    if (query.length < 2) {
        rowsSeries.forEach(row => {
            row.style.display = ""; // Réinitialise l'affichage des lignes
            row.children[columnIndex].innerHTML = row.children[columnIndex].textContent; // Réinitialise les contenus des cellules
        });
        return;
    }

    const queryNormalized = query.normalize('NFD').toLowerCase().replace(/[\u0300-\u036f]/g, ""); // Normalise et supprime les accents de la requête

    rowsSeries.forEach(row => {
        const cell = row.children[columnIndex];
        const originalText = cell.textContent; // Conserve le texte original
        const normalizedText = originalText.normalize('NFD').toLowerCase().replace(/[\u0300-\u036f]/g, ""); // Supprime les accents pour comparaison

        if (normalizedText.includes(queryNormalized)) {
            row.style.display = ""; // Affiche la ligne

            // Surlignage des fragments correspondants
            const matchPositions = [];
            let startIndex = normalizedText.indexOf(queryNormalized);
            while (startIndex !== -1) {
                matchPositions.push([startIndex, startIndex + queryNormalized.length]);
                startIndex = normalizedText.indexOf(queryNormalized, startIndex + 1);
            }

            let highlightedText = '';
            let lastIndex = 0;

            matchPositions.forEach(([start, end]) => {
                // Conserve le texte avant la correspondance
                highlightedText += originalText.substring(lastIndex, start);
                // Ajoute le fragment surligné
                highlightedText += `<span class="highlight">${originalText.substring(start, end)}</span>`;
                // Met à jour l'indice de fin
                lastIndex = end;
            });

            // Ajoute le reste du texte après les correspondances
            highlightedText += originalText.substring(lastIndex);
            cell.innerHTML = highlightedText; // Remplace le contenu par la version surlignée
        } else {
            row.style.display = "none"; // Masque la ligne
            cell.innerHTML = originalText; // Réinitialise le contenu de la cellule
        }
    });
}
function pl_chargerLivresTable() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_les_livres_data.php?genre='+genre+'&condition='+condition, true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        var livresData = JSON.parse(xhr.responseText);
                        pl_AfficheLaTableDesLivres(livresData);
                        pl_initialiserLesTrisLivres();
                    }   catch (e) {
                    };    
                };    
            } else {
                pa_retour_erreur_ajax(xhr.status);
            };
        };
    }    
    xhr.send();
}
function pl_OuvrePSeudoMenuContextuel(event) {
    event.stopPropagation(); // Empêche la fermeture immédiate lors du clic sur le bouton
    const menu = document.getElementById("contextMenu");
    // Positionne le menu en fonction de la position du bouton
    menu.style.left = `${event.pageX}px`;
    menu.style.top = `${event.pageY}px`;
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
// Ferme le menu si on clique en dehors de celui-ci
document.addEventListener("click", function() {
    document.getElementById("contextMenu").style.display = "none";
});
function pl_afficheoumasqueLaDescription(columnIndex) {
        const table = document.getElementById("bibliotheque_TableDesLivres");
        const rows = table.rows;

        for (let i = 0; i < rows.length; i++) {
            const cell = rows[i].cells[columnIndex];
            if (cell.style.display === "none") {
                cell.style.display = ""; // Affiche la colonne
            } else {
                cell.style.display = "none"; // Masque la colonne
            }
        }
    }
function pl_AfficheLaTableDesLivres(livresData) {
    var tableBody = document.getElementById('bibliotheque_TableDesLivres').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = ''; // Vider le contenu existant
    livresData.forEach(function(livre) {
        var row = tableBody.insertRow();
        var idCell = row.insertCell(0);
        idCell.textContent = livre['IDLivres'];
        idCell.style.display = 'none'; // Rendre la cellule invisible
        var adresseCell = row.insertCell(1);
        adresseCell.textContent = livre['Titre'];
        if (livre['textSnippet']) {
            adresseCell.title = livre['textSnippet'];
        };    
        var adresseCell = row.insertCell(2);
        adresseCell.textContent = livre['description'];
        if (livre['auteurs'] !== undefined && livre['auteurs'] !== null && livre['auteurs'] !== '') {
            row.insertCell(3).textContent = JSON.parse('"' + livre['auteurs'] + '"');
        } else {
            row.insertCell(3).textContent = '';
        }    
        row.insertCell(4).textContent = livre['ISBN13'];
        // Rendre la ligne sélectionnable
        row.addEventListener('click', function() {
            pl_selectionneLaLigneLivre(row);
        });
        rows = Array.from(tableBody.querySelectorAll('tr'));
    });
};
function pl_AfficheLaTableDesSeries(seriesData) {
    var tableBody = document.getElementById('bibliotheque_seriesTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = ''; // Vider le contenu existant
    seriesData.forEach(function(series) {
        var row = tableBody.insertRow();

        var idCell = row.insertCell(0);
        idCell.textContent = series['Idseries'];
        idCell.style.display = 'none'; // Rendre la cellule invisible
        
        row.insertCell(1).textContent = series['Nom'];

        var adresseCell = row.insertCell(2);
        var link = document.createElement('a');
        link.href = series['Adresse sur site'];
        link.textContent = series['Adresse sur site'];
        link.target = '_blank'; // Ouvrir dans un nouvel onglet
        adresseCell.appendChild(link);
        row.insertCell(3).textContent = series['Etat'];
        // Rendre la ligne sélectionnable
        //row.addEventListener('click', function() {
        //    selectRow(row);
        //});
        rows = Array.from(tableBody.querySelectorAll('tr'));
    });
}

function pl_enregistreAssociation(tag) {
    // Récupérer les valeurs des attributs depuis le tag
    const livreId = tag.getAttribute("idlivre");
    const serieId = tag.getAttribute("idserie");

    if (!livreId || !serieId) {
        console.error("Impossible d'enregistrer l'association : ID du livre ou de la série manquant.");
        return;
    }

    // Ajoute des données supplémentaires qui ne sont pas dans le formulaire
    const data = new FormData();
    data.append('perimetre', 'Creation');
    data.append('livreid', livreId); // ID du livre depuis l'attribut
    data.append('serieid', serieId); // ID de la série depuis l'attribut

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'bibliotheque_associer_les_livres_aux_series_crud.php', true);

    // Afficher le sablier
    pa_afficherSablier();

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (!response.succes) {
                            fa_showModal(
                                response.message,
                                title = "Avertissement",
                                showButtons = { yes: false, no: false, cancel: true },
                                { yes: "Continuer", no: "Annuler", cancel: "Vu" }
                            );
                        }
                    } catch (e) {
                        console.error("Erreur lors de l'analyse de la réponse :", e);
                    }
                }
            } else {
                pa_retour_erreur_ajax(xhr.status);
            }
        }
    };

    // Envoyer les données
    xhr.send(data);
};
    function pl_selectionneLaLigneLivre(row) {
        // Désélectionner toutes les autres lignes
        var rows = document.querySelectorAll('#bibliotheque_TableDesLivres tbody tr');
        rows.forEach(function(r) {
            r.classList.remove('selected');
        });

        // Sélectionner la ligne cliquée
        row.classList.add('selected');
    };

    // Fonction pour récupérer l'ID de la ligne sélectionnée
    function fl_RecupereLaLigneSelectionnee() {
        // Trouver la ligne sélectionnée
        const selectedRow = document.querySelector(".TableDesLivres tbody tr.selected");
        
        if (selectedRow) {
            // Récupérer la première cellule de la ligne sélectionnée (Idseries)
            const idSeries = selectedRow.cells[0].textContent.trim();
            return idSeries;
        } else {
            console.log("Aucune ligne sélectionnée.");
            return null;
        }
    };

function pl_initialiserLesTrisLivres() {
    const table = document.querySelector("#bibliotheque_TableDesLivres");
    const headers = table.querySelectorAll("th");
    const sortDirection = {}; // Stocker la direction de tri pour chaque colonne
    headers.forEach((header, index) => {
        header.addEventListener("click", function () {
            // Inverser la direction de tri pour la colonne
            sortDirection[index] = !sortDirection[index];
            // Supprimer les classes de tri des autres en-têtes
            headers.forEach(h => h.classList.remove("sorted-asc", "sorted-desc"));

            // Ajouter la classe appropriée au header cliqué
            header.classList.add(sortDirection[index] ? "sorted-asc" : "sorted-desc");

            // Appeler la fonction générique avec la table cible
            pa_trierLaTable(table, index, sortDirection[index]);
        });
    });
}

function pl_initialiserLesTrisSeries() {
    const table = document.querySelector("#bibliotheque_seriesTable");
    const headers = table.querySelectorAll("th");
    let sortDirection = {}; // Gérer la direction du tri pour chaque colonne

    headers.forEach((header, index) => {
        header.addEventListener("click", function () {
            // Inverser la direction de tri pour la colonne
            sortDirection[index] = !sortDirection[index];

            // Supprimer les classes de tri des autres en-têtes
            headers.forEach(h => h.classList.remove("sorted-asc", "sorted-desc"));

            // Ajouter la classe appropriée au header cliqué
            header.classList.add(sortDirection[index] ? "sorted-asc" : "sorted-desc");

            // Appeler la fonction générique de tri
            pa_trierLaTable(table, index, sortDirection[index]);
        });
    });
}


let bInitialisationDuMenu=false;
    document.addEventListener('DOMContentLoaded', function () {
    if(!bInitialisationDuMenu){
        pl_init_unitaire();
    }
    
    const booksTable = document.getElementById("bibliotheque_TableDesLivres");
    const seriesTable = document.getElementById("bibliotheque_seriesTable");
    const tagsContainer = document.getElementById("tagsContainer");

    let selectedSeriesId = null; // Stocke l'idserie de la ligne sélectionnée dans la table des séries

    // Gestion du clic sur une ligne de la table des séries
    seriesTable.addEventListener("click", function (e) {
        const row = e.target.closest("tr");
        if (!row) return;

        const columns = row.querySelectorAll("td");
        selectedSeriesId = columns[0]?.textContent.trim(); // ID de la série (1ère colonne)
        
        if (!selectedSeriesId) {
        //    alert("Sélection de série invalide.");
            return;
        }

        // Désélectionner toutes les autres lignes
        seriesTable.querySelectorAll("tr").forEach(r => r.classList.remove("selected"));

        // Sélectionner la ligne actuelle
        row.classList.add("selected");
        pa_chargerLAssociation(selectedSeriesId,true);
    });

    // Gérer le double-clic sur une ligne de la table des livres
    booksTable.addEventListener("dblclick", function (e) {
        const row = e.target.closest("tr");
        if (!row) return;

        const columns = row.querySelectorAll("td");
        const idLivre = columns[0]?.textContent.trim(); // ID du livre (1ère colonne)
        const titreLivre = columns[1]?.textContent.trim(); // Titre du livre (2ème colonne)

        if (!idLivre || !titreLivre) return;

        if (!selectedSeriesId) {
            alert("Veuillez d'abord sélectionner une série dans la table des séries.");
            return;
        }

        // Créer un tag
        const tag = document.createElement("div");
        tag.className = "tag";
        tag.setAttribute("idlivre", idLivre);
        tag.setAttribute("idlivreparserie", "-1");
        tag.setAttribute("idserie", selectedSeriesId);
        tag.innerHTML = `
            <span>${titreLivre}</span>
            <span class="close">&times;</span>
        `;

        // Ajouter un événement pour supprimer le tag
        tag.querySelector(".close").addEventListener("click", function () {
            tag.remove();

            // Rendre visible la ligne du livre correspondante
            const rows = booksTable.querySelectorAll("tbody tr");
            rows.forEach(r => {
                if (r.querySelector("td")?.textContent.trim() === idLivre) {
                    r.style.display = "";
                }
            });
        });

        // Ajouter le tag dans le conteneur
        tagsContainer.appendChild(tag);
        // Enregistrer l'association
        pl_enregistreAssociation(tag);
        // Masquer la ligne du livre
        row.style.display = "none";
    });







});
function pl_init_unitaire(){
    /*
        Explication de l'xistence de bInitialisationDuMenu    
        Nécessaire pour que la fonction ne s'exécute pas autant de fois qu'il y a de frames
        Et donc ne pas créer de multiples listeners qui feraient que l'on exécute plusieurs fois la création d'un onglet, par exemple.
    */
    bInitialisationDuMenu=true;     
    pl_chargerLivresTable();
    pa_chargerSeriesTable();
}
