<?php
// Vous devez vous assurer que la variable $perimetre est définie
$perimetre = $_GET['perimetre'] ?? 'unitaire'; // Valeur par défaut si $perimetre n'est pas passé
header('Content-Type: application/javascript');
?>
const table = document.querySelector("#bibliotheque_TableDesLivres");
/* On ne peut pas industrialiser car le nombre de colonnes varie */
const entetes = document.querySelectorAll("#bibliotheque_TableDesLivres th");
let lastSelectedRowIndex = null; // Suivre l'indice de la dernière ligne sélectionnée
entetes.forEach((header, index) => {
    // Ajouter un champ de recherche dans les colonnes de texte
    <?php if ($perimetre === 'unitaire'): ?>
        if (index === 1 || index === 2 || index === 3 || index === 4 || index === 5 || index === 6 || index === 7 || index === 8) { // Colonnes avec du texte
    <?php else: ?>     
        if (index === 1 || index === 2 || index === 3 || index === 4 || index === 5 || index === 6 || index === 7 || index === 8) { // Colonnes avec du texte
    <?php endif; ?>    
        let input = document.createElement("input");
        input.type = "text";
        input.placeholder = "Rechercher...";
        input.style.marginTop = "5px";
        input.style.width = "100%";
        input.id="recherche_"+index;
        header.appendChild(input);

        input.addEventListener("keyup", function() {
            pl_filtrerLaTable(index, input.value);
        });
    }
});
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
                        pl_MajLivresInfo(livresData.length);
                        pl_initialiserLesTris();
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
<?php if ($perimetre === 'unitaire'  || $perimetre === 'listeDesCourses' || $perimetre === 'maListeDesCourses'): ?>
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
        row.insertCell(1).textContent = livre['Nom'];
        var adresseCell = row.insertCell(2);
        adresseCell.textContent = livre['Titre'];
        if (livre['textSnippet']) {
            adresseCell.title = livre['textSnippet'];
        };    
        var adresseCell = row.insertCell(3);
        adresseCell.textContent = livre['description'];
        //document.getElementById('auteurs').value=JSON.parse('"' + livre.auteurs + '"');
        if (livre['auteurs'] !== undefined && livre['auteurs'] !== null && livre['auteurs'] !== '') {
            row.insertCell(4).textContent = JSON.parse('"' + livre['auteurs'] + '"');
        } else {
            row.insertCell(4).textContent = '';
        }    
        row.insertCell(5).textContent = livre['ISBN13'];
        row.insertCell(6).textContent = livre['DateRecuperation'];
        row.insertCell(7).textContent = livre['ISBN10'];
        //row.insertCell(8).textContent = livre['Etat'];    // Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque (livre + liste de courses)
        //row.insertCell(9).textContent = livre['Genre'];  // Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque
        /*
        var adresseCell = row.insertCell(10);
        if (livre['lienReference'] !== undefined && livre['lienReference'] !== null && livre['lienReference'] !== '') {
            var link = document.createElement('a');
            link.href = livre['lienReference']
            link.textContent = livre['lienReference'];
            link.target = '_blank'; // Ouvrir dans un nouvel onglet
            adresseCell.appendChild(link);
        }    
        */
        // Rendre la ligne sélectionnable
        row.addEventListener('click', function() {
            pl_selectionneLaLigne(row);
        });
        rows = Array.from(tableBody.querySelectorAll('tr'));
    });
};    
<?php else: ?>
    function pl_AfficheLaTableDesLivres(livresData) {
    var tableBody = document.getElementById('bibliotheque_TableDesLivres').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = ''; // Vider le contenu existant
    livresData.forEach(function(livre) {
        var row = tableBody.insertRow();
        var idCell = row.insertCell(0);
        row.insertCell(1).textContent = livre['Nom'];
        idCell.textContent = livre['IDLivres'];
        idCell.style.display = 'none'; // Rendre la cellule invisible
        row.insertCell(2).textContent = livre['Titre'];
        if (livre['auteurs'] !== undefined && livre['auteurs'] !== null && livre['auteurs'] !== '') {
            row.insertCell(3).textContent = JSON.parse('"' + livre['auteurs'] + '"');
        } else {
            row.insertCell(3).textContent = '';
        }    
        row.insertCell(4).textContent = livre['ISBN13'];
        row.insertCell(5).textContent = livre['DateRecuperation'];
        row.insertCell(6).textContent = livre['DateDerniereRecherche'];
        row.insertCell(7).textContent = livre['ISBN10'];
        //row.insertCell(8).textContent = livre['Etat'];    // Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque (livre + liste de courses)
        //row.insertCell(9).textContent = livre['Genre'];  // Toutdoux : Mieux gérer cette colonne qui ne devrait être visible que lorsque l'on demande à voir l'intégralité de la bibliothèque
        //row.insertCell(10).textContent = livre['OLID'];
        // Rendre la ligne sélectionnable
        rows = Array.from(tableBody.querySelectorAll('tr'));
    });
    

    

    rows.forEach((row, index) => {
    row.addEventListener('click', function(event) {
        if (event.shiftKey && lastSelectedRowIndex !== null) {
            // Shift+clic : sélectionner un intervalle de lignes
            const start = Math.min(lastSelectedRowIndex, index);
            const end = Math.max(lastSelectedRowIndex, index);

            rows.slice(start, end + 1).forEach(r => r.classList.add('selected'));
        } else if (event.ctrlKey || event.metaKey) {
            // Ctrl/Cmd+clic : ajouter ou retirer une ligne de la sélection
            row.classList.toggle('selected');
        } else {
            // Clic simple : déselectionner toutes les lignes et sélectionner celle-ci
            rows.forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');
        }

        // Mettre à jour la dernière ligne sélectionnée
        lastSelectedRowIndex = index;
    });
});

}
<?php endif; ?>


function pl_MajLivresInfo(count) {
    var infoElement = document.getElementById('livresNombre');
    var currentDate = new Date();
    var formattedDate = currentDate.toLocaleDateString() + ' à ' + currentDate.toLocaleTimeString();
    infoElement.textContent = `Bonjour ${qui}. Il y a ${count} livres, le ${formattedDate}`;
};

<?php if ($perimetre === 'unitaire' || $perimetre === 'listeDesCourses' || $perimetre === 'maListeDesCourses'): ?>
    function pl_selectionneLaLigne(row) {
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
    function fl_getSelectedRowISBN13() {
        // Trouver la ligne sélectionnée
        const selectedRow = document.querySelector(".TableDesLivres tbody tr.selected");
        if (selectedRow) {
            // Récupérer la première cellule de la ligne sélectionnée ()
            const idisbn13 = selectedRow.cells[3].textContent.trim();
            return idisbn13;
        } else {
            console.log("Aucune ligne sélectionnée.");
            return null;
        }
    };
    function fl_getSelectedRowISBN10() {
        // Trouver la ligne sélectionnée
        const selectedRow = document.querySelector(".TableDesLivres tbody tr.selected");
        if (selectedRow) {
            // Récupérer la première cellule de la ligne sélectionnée ()
            const idisbn13 = selectedRow.cells[7].textContent.trim();
            return idisbn13;
        } else {
            console.log("Aucune ligne sélectionnée.");
            return null;
        }
    };

    <?php else: ?>
    function fl_RecupereToutesLesLignesSelectionnees() {
        const table = document.getElementById('bibliotheque_TableDesLivres');
        const selectedRows = table.querySelectorAll('tbody tr.selected');
        const selectedIds = [];

        selectedRows.forEach(row => {
            const idCell = row.querySelector('td:first-child'); // Récupérer la première cellule (ID)
            if (idCell) {
                selectedIds.push(idCell.textContent.trim()); // Ajouter l'ID à la liste
            }
        });

        return selectedIds;
    };
    function fl_getSelectedRowISBN13() {
        // Trouver la ligne sélectionnée
        const selectedRow = document.querySelector(".TableDesLivres tbody tr.selected");
        if (selectedRow) {
            // Récupérer la première cellule de la ligne sélectionnée ()
            const idisbn13 = selectedRow.cells[3].textContent.trim();
            return idisbn13;
        } else {
            console.log("Aucune ligne sélectionnée.");
            return null;
        }
    };
    function fl_getSelectedRowISBN10() {
        // Trouver la ligne sélectionnée
        const selectedRow = document.querySelector(".TableDesLivres tbody tr.selected");
        if (selectedRow) {
            // Récupérer la première cellule de la ligne sélectionnée ()
            const idisbn10 = selectedRow.cells[7].textContent.trim();
            return idisbn10;
        } else {
            console.log("Aucune ligne sélectionnée.");
            return null;
        }
    };

<?php endif; ?>

function pl_initialiserLesTris() {
    const table = document.querySelector("#bibliotheque_TableDesLivres");
    const headers = document.querySelectorAll("#bibliotheque_TableDesLivres th");
    let sortDirection = {}; // Gérer la direction du tri pour chaque colonne

    headers.forEach((header, index) => {
        header.addEventListener("click", function() {
            sortDirection[index] = !sortDirection[index];
            pa_trierLaTable(table,index, sortDirection[index], rows);
        });
    });
};

function pl_filtrerLaTable(columnIndex, query) {
    // Ne pas effectuer la recherche si la chaîne contient moins de 2 caractères
  if (query.length < 2) {
    rows.forEach(row => {
      row.style.display = ""; // Réinitialise l'affichage des lignes
      row.children[columnIndex].innerHTML = row.children[columnIndex].textContent; // Réinitialise les contenus des cellules
    });
    return;
  }
  const queryNormalized = query.normalize('NFD').toLowerCase().replace(/[\u0300-\u036f]/g, ""); // Normalise et supprime les accents de la requête

  rows.forEach(row => {
    const cell = row.children[columnIndex];
    const originalText = cell.textContent; // Conserve le texte original
    const normalizedText = originalText.normalize('NFD').toLowerCase().replace(/[\u0300-\u036f]/g, ""); // Supprime uniquement les accents pour comparaison

    // Vérifie si la cellule contient le texte recherché
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

let bInitialisationDuMenu=false;
<?php if ($perimetre === 'unitaire' || $perimetre === 'listeDesCourses'): ?>
    document.addEventListener('DOMContentLoaded', function () {
    if(!bInitialisationDuMenu){
        pl_init_unitaire();
    }    
});
<?php else: ?>
    <?php if ($perimetre !== 'maListeDesCourses'): ?>
        document.addEventListener('DOMContentLoaded', function () {
        if(!bInitialisationDuMenu){
            pl_init_km();
        }
});        
<?php endif; ?>
<?php endif; ?>
<?php if ($perimetre === 'maListeDesCourses'): ?>
    pl_chargerLivresTable();
<?php else: ?>
<?php if ($perimetre === 'unitaire' || $perimetre === 'listeDesCourses'): ?>
function pl_init_unitaire(){
    /*
        Explication de l'xistence de bInitialisationDuMenu    
        Nécessaire pour que la fonction ne s'exécute pas autant de fois qu'il y a de frames
        Et donc ne pas créer de multiples listeners qui feraient que l'on exécute plusieurs fois la création d'un onglet, par exemple.
    */
    bInitialisationDuMenu=true;     
    
    document.getElementById('consultBookBtn').addEventListener('click', function () {
        const selectedBookId = fl_RecupereLaLigneSelectionnee();
        if (selectedBookId) {
            const url = 'bibliotheque_le_livre.php?id=' + selectedBookId + '&genre=' + genre+'&perimetre=Consultation';
            const tabTitle = 'Consultation livre';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });

    const LanceGoogleISBN13=document.getElementById('LanceGoogleISBN13');
    if (LanceGoogleISBN13) {
        document.getElementById('LanceGoogleISBN13').addEventListener('click', function () {
        const selectedisbn13 = fl_getSelectedRowISBN13();
        if (selectedisbn13) {
            const url = 'https://www.google.com/search?q=' + selectedisbn13;
            window.open(url, '_blank');
        }
    }    
    )};
    const LanceGoogleISBN10=document.getElementById('LanceGoogleISBN10');
    if (LanceGoogleISBN10) {
        document.getElementById('LanceGoogleISBN10').addEventListener('click', function () {
        const selectedisbn10 = fl_getSelectedRowISBN10();
        if (selectedisbn10) {
            const url = 'https://www.google.com/search?q=' + selectedisbn10;
            window.open(url, '_blank');
        }
    }    
    )};
    const LanceGoogleTitre=document.getElementById('LanceGoogleTitre');
    if (LanceGoogleTitre) {
        document.getElementById('LanceGoogleTitre').addEventListener('click', function () {
        const selectedisbn10 = fl_getSelectedRowISBN10();
        if (selectedisbn10) {
            const url = 'https://www.google.com/search?q=' + selectedisbn10;
            window.open(url, '_blank');
        }
    }    
    )};
    
    document.getElementById('deleteBookBtn').addEventListener('click', function () {
        const selectedBookId = fl_RecupereLaLigneSelectionnee();
        if (selectedBookId) {
            const url = 'bibliotheque_le_livre.php?id=' + selectedBookId + '&genre=' + genre+'&perimetre=Suppression';
            const tabTitle = 'Suppression livre';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });
    
    document.getElementById('newBookBtn').addEventListener('click', function () {
        const url = 'bibliotheque_le_livre.php?id=0&genre=' + genre+'&perimetre=Creation&perimetreDOrigine=<?= $perimetre ?>';
        const tabTitle = 'Nouveau livre';
        parent.pl_createDynamicTab(tabTitle, url);
    });
    
    
    document.getElementById('printBookBtn').addEventListener('click', function () {
        pa_showToast('Fonctionnalité en cours de développement...',3000);
    });

    const sgetISBN_ws01 = document.getElementById('getISBN_ws01');
    if (sgetISBN_ws01) {
        sgetISBN_ws01.addEventListener('click', function () {
            const url = 'bibliotheque_recherche_isbn_openLibrairy_saisie.php';
            const tabTitle = 'Recherche Open Librairy';
            parent.pl_createDynamicTab(tabTitle, url);
        });
    };
    const sgetISBN_ws02 = document.getElementById('getISBN_ws02');
    if (sgetISBN_ws02) {
    document.getElementById('getISBN_ws02').addEventListener('click', function () {
        const url = 'bibliotheque_recherche_isbn_google_api.php';
        const tabTitle = 'Recherche Google API';
        parent.pl_createDynamicTab(tabTitle, url);
    });
    };
    document.getElementById('modifyBookBtn').addEventListener('click', function () {
        const selectedBookId = fl_RecupereLaLigneSelectionnee();
        if (selectedBookId) {
            const url = 'bibliotheque_le_livre.php?id=' + selectedBookId + '&genre=' + genre+'&perimetre=Modification';
            const tabTitle = 'Modification livre';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });

    pl_chargerLivresTable();
}
function pl_OuvrirOngletAuKilometre(){
    const url = 'bibliotheque_les_livres.php?genre=' + genre+'&perimetre=KM&condition=0';
        const tabTitle = 'Traitements KM';
        parent.pl_createDynamicTab(tabTitle, url);
};
<?php else: ?>
    function pl_getISBN(perimetre){
        const selectedIds = fl_RecupereToutesLesLignesSelectionnees();
        if (selectedIds.length > 0) {
            const xhr = new XMLHttpRequest();
            switch (perimetre) {
                case 1: 
                    xhr.open('POST', 'bibliotheque_cherche_infos_api_sur_id.php?perimetre=ISBN13', true);
                    break;
                case 2: 
                    xhr.open('POST', 'bibliotheque_cherche_infos_api_sur_id.php?perimetre=ISBN10', true);
                    break;
                };
            xhr.setRequestHeader('Content-Type', 'application/json');
            pa_afficherSablier();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) { // La requête est terminée
                pa_effacerSablier(); // Cacher le sablier
                const response = JSON.parse(xhr.responseText);
                if (xhr.status == 200) {
                    fa_showModal(response.message,"Information",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Dont acte"})
                    //pl_chargerLivresTable();
                } else {
                    pa_retour_erreur_ajax(xhr.status);
                };
            };
            };
            // Envoyer les IDs au serveur sous forme de JSON
            xhr.send(JSON.stringify({ selectedIds: selectedIds }));
        } else {
            alert("Aucune ligne sélectionnée.");
        }
    }

    function pl_getISBN_ws04(){
        const selectedIds = fl_RecupereToutesLesLignesSelectionnees();
    };    

    function pl_init_km(){
    /*
        Explication de l'xistence de bInitialisationDuMenu    
        Nécessaire pour que la fonction ne s'exécute pas autant de fois qu'il y a de frames
        Et donc ne pas créer de multiples listeners qui feraient que l'on exécute plusieurs fois la création d'un onglet, par exemple.
    */
    bInitialisationDuMenu=true;
    const LanceGoogleISBN13=document.getElementById('LanceGoogleISBN13');
    if (LanceGoogleISBN13) {
        document.getElementById('LanceGoogleISBN13').addEventListener('click', function () {
        const selectedisbn13 = fl_getSelectedRowISBN13();
        if (selectedisbn13) {
            const url = 'https://www.google.com/search?q=' + selectedisbn13;
            window.open(url, '_blank');
        }
    }    
    )};
    const LanceGoogleISBN10=document.getElementById('LanceGoogleISBN10');
    if (LanceGoogleISBN10) {
        document.getElementById('LanceGoogleISBN10').addEventListener('click', function () {
        const selectedisbn10 = fl_getSelectedRowISBN10();
        if (selectedisbn10) {
            const url = 'https://www.google.com/search?q=' + selectedisbn10;
            window.open(url, '_blank');
        }
    }    
    )};
    const LanceAmazonISBN13=document.getElementById('LanceAmazonISBN13');
    if (LanceAmazonISBN13) {
        document.getElementById('LanceAmazonISBN13').addEventListener('click', function () {
        const selectedisbn13 = fl_getSelectedRowISBN13();
        if (selectedisbn13) {
            let urlAmazon = 'https://www.amazon.fr/s?k=' + selectedisbn13;
            window.open(urlAmazon, '_blank', 'noopener,noreferrer');
        }
    }    
    )};

    const LanceAmazonetGoogleISBN13=document.getElementById('LanceAmazonetGoogleISBN13');
    if (LanceAmazonetGoogleISBN13) {
        document.getElementById('LanceAmazonetGoogleISBN13').addEventListener('click', function () {
        const selectedisbn13 = fl_getSelectedRowISBN13();
        if (selectedisbn13) {


            //  ToutDoux
            //  Code non fonctionnel, car n'ouvre qu'un seul onglet ou nouveau navigateur (selon usage de focus=no ou pas)
            let urlAmazon = 'https://www.amazon.fr/s?k=' + selectedisbn13;
            let urlGoogle = 'https://www.google.com/search?q=' + selectedisbn13;
            window.open(urlAmazon, '_blank', 'noopener,noreferrer,focus=no');
            //window.open(urlAmazon, '_blank', 'noopener,noreferrer');
            setTimeout(() => {
                window.open(urlGoogle, '_blank', 'noopener,noreferrer,focus=no');
                //window.open(urlGoogle, '_blank', 'noopener,noreferrer');
            }, 100); // 100 ms de délai
        }
    }    
    )};
    document.getElementById('modifyBookBtn').addEventListener('click', function () {
        const selectedBookId = fl_RecupereToutesLesLignesSelectionnees();
        if (selectedBookId) {
            const url = 'bibliotheque_le_livre.php?id=' + selectedBookId + '&genre=' + genre+'&perimetre=Modification';
            const tabTitle = 'Modification livre';
            parent.pl_createDynamicTab(tabTitle, url);
        }
    });
    pl_chargerLivresTable();
};
<?php endif; ?>
<?php endif; ?>