function pl_ChargerOrigines() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_les_origines_data.php', true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        var OriginesData = JSON.parse(xhr.responseText);
                        pl_afficheLaTableDesOrigines(OriginesData);
                        pl_MajOriginesInfo(OriginesData.length);
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
function pl_afficheLaTableDesOrigines(OriginesData) {
    var tableBody = document.getElementById('bibliotheque_TableDesorigines').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = ''; // Vider le contenu existant

    OriginesData.forEach(function(origine) {
        var row = tableBody.insertRow();

        var idCell = row.insertCell(0);
        idCell.textContent = origine['IDOrigines'];
        idCell.style.display = 'none'; // Rendre la cellule invisible
        
        row.insertCell(1).textContent = origine['Nom'];
        row.insertCell(2).textContent = origine['Adresse'];
        row.insertCell(3).textContent = origine['CodeInterne'];
        row.insertCell(4).textContent = origine['Etat'];
        row.insertCell(5).textContent = origine['Gestion'];
        // Rendre la ligne sélectionnable
        row.addEventListener('click', function() {
            pl_selectionneLaLigne(row);
        });
    });
}

function pl_MajOriginesInfo(count) {
    var infoElement = document.getElementById('originesNombre');
    var currentDate = new Date();
    var formattedDate = currentDate.toLocaleDateString() + ' ' + currentDate.toLocaleTimeString();
    infoElement.textContent = `${count} origines, dernière mise à jour : ${formattedDate}`;
}

// Charger les données initiales lors du chargement de la page
window.onload = function() {
    pl_ChargerOrigines();
};

function pl_selectionneLaLigne(row) {
    // Désélectionner toutes les autres lignes
    var rows = document.querySelectorAll('#bibliotheque_TableDesorigines tbody tr');
    rows.forEach(function(r) {
        r.classList.remove('selected');
    });

    // Sélectionner la ligne cliquée
    row.classList.add('selected');
    const selectedRow = document.querySelector(".TableDesOrigines tbody tr.selected");
    const Gestion = selectedRow.cells[5].textContent.trim();
    const modifyOrigineBtn = document.getElementById("modifyOrigineBtn");
    const deleteOrigineBtn = document.getElementById("deleteOrigineBtn");
    if (Gestion === "Interne") {
        modifyOrigineBtn.disabled = true;
        deleteOrigineBtn.disabled = true;
        modifyOrigineBtn.classList.add("boutonGrisé");
        deleteOrigineBtn.classList.add("boutonGrisé");
    } else {
        modifyOrigineBtn.disabled = false;
        deleteOrigineBtn.disabled = false;
        modifyOrigineBtn.classList.remove("boutonGrisé");
        deleteOrigineBtn.classList.remove("boutonGrisé");

    }
}

// Fonction pour récupérer l'ID de la ligne sélectionnée
function fl_RecupereLaLigneSelectionnee() {
    // Trouver la ligne sélectionnée
    const selectedRow = document.querySelector(".TableDesOrigines tbody tr.selected");
    
    if (selectedRow) {
        // Récupérer la première cellule de la ligne sélectionnée (Idseries)
        const idOrignes = selectedRow.cells[0].textContent.trim();
        return idOrignes;
    } else {
        console.log("Aucune ligne sélectionnée.");
        return null;
    }
};


document.getElementById('consultOrigineBtn').addEventListener('click', function () {
    const selectedOrigineId = fl_RecupereLaLigneSelectionnee();
    if (selectedOrigineId) {
        const url = 'bibliotheque_l_origine.php?id=' + selectedOrigineId + '&perimetre=Consultation';
        const tabTitle = 'Consultation origine';
        parent.pl_createDynamicTab(tabTitle, url);
    } else {
        alert("Rien n'est sélectionné.")
    }
});

document.getElementById('modifyOrigineBtn').addEventListener('click', function () {
    const selectedOrigineId = fl_RecupereLaLigneSelectionnee();
    if (selectedOrigineId) {
        const url = 'bibliotheque_l_origine.php?id=' + selectedOrigineId + '&perimetre=Modification';
        const tabTitle = 'Modification origine';
        parent.pl_createDynamicTab(tabTitle, url);
    }
});

document.getElementById('deleteOrigineBtn').addEventListener('click', function () {
    const selectedOrigineId = fl_RecupereLaLigneSelectionnee();
    if (selectedOrigineId) {
        const url = 'bibliotheque_l_origine.php?id=' + selectedOrigineId + '&perimetre=Suppression';
        const tabTitle = 'Suppression origine';
        parent.pl_createDynamicTab(tabTitle, url);
    }
});

document.getElementById('newOrigineBtn').addEventListener('click', function () {
    const url = 'bibliotheque_l_origine.php?id=0&perimetre=Creation';
    const tabTitle = 'Nouvelle origine';
    parent.pl_createDynamicTab(tabTitle, url);
});
