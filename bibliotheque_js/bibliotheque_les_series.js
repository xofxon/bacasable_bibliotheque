const table = document.querySelector("#bibliotheque_seriesTable");
const headers = table.querySelectorAll("#bibliotheque_seriesTable th");
let sortDirection = {}; // Un objet pour stocker la direction de tri pour chaque colonne
let rows = Array.from(table.querySelectorAll("tbody tr"));
headers.forEach((header, index) => {
    // Ajouter un champ de recherche dans les colonnes de texte
    if (index === 1 || index === 2 || index === 5 || index === 6) { // Colonnes avec du texte (Nom, Adresse, Etat, Commentaire)
        let input = document.createElement("input");
        input.type = "text";
        input.placeholder = "Rechercher...";
        input.style.marginTop = "5px";
        input.style.width = "100%";
        input.id="recherche_"+index;
        header.appendChild(input);

        input.addEventListener("keyup", function() {
            pl_filtrerLaTabledesSeries(index, input.value);
        });
    }
});
// Fonction pour récupérer l'ID de la ligne sélectionnée
function fl_RecupereLaLigneSelectionnee() {
    // Trouver la ligne sélectionnée
    const selectedRow = document.querySelector(".TableDesSeries tbody tr.selected");
    
    if (selectedRow) {
        // Récupérer la première cellule de la ligne sélectionnée (Idseries)
        const idSeries = selectedRow.cells[0].textContent.trim();
        return idSeries;
    } else {
        console.log("Aucune ligne sélectionnée.");
        return null;
    }
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
        let nombre = Number(series['Nombre livres présents']);
        if (!isNaN(nombre) && nombre !== 0) {
            row.insertCell(3).textContent = series['Nombre livres présents'];
        } else {
            row.insertCell(3).textContent = '';    
        }
        nombre=0;
        nombre = Number(series['Nombre liste de courses']);
        if (!isNaN(nombre) && nombre !== 0) {
            row.insertCell(4).textContent = series['Nombre liste de courses'];
        } else {
            row.insertCell(4).textContent = '';    
        }
        row.insertCell(5).textContent = series['Etat'];
        row.insertCell(6).textContent = series['Commentaires'];
        // Rendre la ligne sélectionnable
        row.addEventListener('click', function() {
            pl_selectionneLaLigne(row);
        });
        rows = Array.from(tableBody.querySelectorAll('tr'));
    });
    pl_MajSeriesInfo(seriesData.length)
}

// Charger les données initiales lors du chargement de la page
window.onload = function() {
    pa_chargerSeriesTable();
    document.getElementById('deleteSerieBtn').addEventListener('click', function () {
        const selectedSerieId = fl_RecupereLaLigneSelectionnee();
        if (selectedSerieId) {
            const url = 'bibliotheque_la_serie.php?id=' + selectedSerieId + '&perimetre=Suppression';
            const tabTitle = 'Suppression série';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });
    
    document.getElementById('newSerieBtn').addEventListener('click', function () {
        const url = 'bibliotheque_la_serie.php?id=0&perimetre=Creation&perimetreDOrigine=<?= $perimetre ?>';
        const tabTitle = 'Nouvelle série';
        parent.pl_createDynamicTab(tabTitle, url);
    });
    
    
    document.getElementById('printSerieBtn').addEventListener('click', function () {
        pa_showToast('Fonctionnalité en cours de développement...',3000);
    });

    document.getElementById('consultSerieBtn').addEventListener('click', function () {
        const selectedSerieId = fl_RecupereLaLigneSelectionnee();
        if (selectedSerieId) {
            const url = 'bibliotheque_la_serie.php?id=' + selectedSerieId +'&perimetre=Consultation';
            const tabTitle = 'Consultation série';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });
    document.getElementById('modifySerieBtn').addEventListener('click', function () {
        const selectedSerieId = fl_RecupereLaLigneSelectionnee();
        if (selectedSerieId) {
            const url = 'bibliotheque_la_serie.php?id=' + selectedSerieId + '&perimetre=Modification';
            const tabTitle = 'Modification série';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });

};
function pl_MajSeriesInfo(count) {
    var infoElement = document.getElementById('seriesNombre');
    var currentDate = new Date();
    var formattedDate = currentDate.toLocaleDateString() + ' ' + currentDate.toLocaleTimeString();

    infoElement.textContent = `${count} séries, dernière mise à jour : ${formattedDate}`;
}
function pl_selectionneLaLigne(row) {
    // Désélectionner toutes les autres lignes
    var rows = document.querySelectorAll('#bibliotheque_seriesTable tbody tr');
    rows.forEach(function(r) {
        r.classList.remove('selected');
    });

    // Sélectionner la ligne cliquée
    row.classList.add('selected');
}

function pl_initialiserLesTrisSeries() {
    const table = document.querySelector("#bibliotheque_seriesTable");
    const headers = table.querySelectorAll("th");
    let sortDirection = {}; // Gérer la direction du tri pour chaque colonne
    headers.forEach((header, index) => {
        header.addEventListener("click", function () {
            sortDirection[index] = !sortDirection[index];
            pa_trierLaTable(table, index, sortDirection[index]); // Passez la table en paramètre
        });
    });
}

// Fonction pour filtrer les colonnes
function pl_filtrerLaTabledesSeries(columnIndex, query) {
    rows.forEach(row => {
        const cellText = row.children[columnIndex].textContent.toLowerCase();
        row.style.display = cellText.includes(query.toLowerCase()) ? "" : "none";
    });
    rows = Array.from(table.querySelectorAll("tbody tr"));
};

