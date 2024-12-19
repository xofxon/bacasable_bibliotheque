// Afficher la modale
if (typeof window.timeoutIdPourModale === "undefined") {
    window.timeoutIdPourModale = 0;
}
function fa_showModal(message, title = "Information", showButtons = {yes: true, no: true, cancel: true}, buttonLabels = {yes: 'Oui', no: 'Non', cancel: 'Abandonner'}, timeout = 5000) {
    const modal = document.getElementById("customModal");
    const modalText = document.getElementById("modal-text");
    const modalTitle = document.getElementById("modal-title");

    // Mettre à jour le contenu de la modale
    modalText.innerHTML = message;
    modalTitle.innerHTML = title;

    // Mettre à jour les libellés des boutons
    document.getElementById('yes-btn').innerText = buttonLabels.yes;
    document.getElementById('no-btn').innerText = buttonLabels.no;
    document.getElementById('cancel-btn').innerText = buttonLabels.cancel;

    // Afficher ou masquer les boutons en fonction de showButtons
    document.getElementById('yes-btn').style.display = showButtons.yes ? 'inline-block' : 'none';
    document.getElementById('no-btn').style.display = showButtons.no ? 'inline-block' : 'none';
    document.getElementById('cancel-btn').style.display = showButtons.cancel ? 'inline-block' : 'none';

    // Afficher la modale
    modal.style.display = "flex";

    return new Promise((resolve) => {
        // Ajouter des écouteurs sur les boutons
        document.getElementById("yes-btn").onclick = function() {
            pa_closeModal();
            resolve(1); // Valeur 1 si 'Oui' est cliqué
        };
        document.getElementById("no-btn").onclick = function() {
            pa_closeModal();
            resolve(0); // Valeur 0 si 'Non' est cliqué
        };
        document.getElementById("cancel-btn").onclick = function() {
            pa_closeModal();
            resolve(-1); // Valeur -1 si 'Abandonner' est cliqué
        };
        if (timeout!=-1){
            // Simule l'abandon après n millisecondes secondes (5 par défaut)
            //  Si -1 alors pas de timeout
            timeoutIdPourModale = setTimeout(() => {
                pa_closeModal();
                resolve(-1); // Resolve with -1 after timeout
            }, timeout);
            // Supprime le timeout si jamais on clique sur un bouton
            modal.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn')) { // Check if clicked element has 'btn' class
                clearTimeout(timeoutIdPourModale);
                }
           });
        };    
    });
};

// Fermer la modale
function pa_closeModal() {
    if (timeoutIdPourModale!=0){
        clearTimeout(timeoutIdPourModale);
    };    
    document.getElementById("customModal").style.display = "none";
};

function fa_utf8_to_b64(str) {
    //  Permet de gérer les caractères accentués avant d'encoder en base 64. Pas sûr qu'il faille utiliser decodeURIComponent à la place ... Toutdoux : à explorer
    return window.btoa(unescape(encodeURIComponent(str)));
};

function pa_retour_erreur_ajax(scodeerreur){
    switch (scodeerreur) {
        case 404:
            fa_showModal("Erreur 404 : Ressource non trouvée.","Erreur",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Dont acte"})
            break;
        case 403:
            fa_showModal("Erreur 403 : Accès refusé.","Erreur",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Dont acte"})    
            break;
        case 500:
            fa_showModal("Erreur 500 : Erreur interne du serveur.","Erreur",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Dont acte"})    
            break;
        default:
            fa_showModal("Erreur "+scodeerreur+" Essayez de prendre contact avec l'administrateur.","Erreur",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Ok"})    
    }
};
function pa_ouvreLeSite(url) {
    if (url) {
        window.open(url, "_blank"); // Ouvre l'URL dans un nouvel onglet
    } else {
        fa_showModal('Avertissement', 'Aucune URL disponible.',{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
    }
}    
function pa_showToast(stext,nduree) {
    var toast = document.getElementById("toast");
    toast.innerText = stext;
    toast.className = "show";
    // Après ndurée (en millisecondes), enlever la classe pour cacher le toast
    setTimeout(function() {
        toast.className = toast.className.replace("show", "");
    }, nduree);
};

function pa_afficherSablier(){
    document.getElementById('overlay').style.display = 'flex';
};

function pa_effacerSablier(){
    document.getElementById('overlay').style.display = 'none'; 
};


// Fonction pour charger le contenu d'un script PHP dans l'onglet
/*  Attention cela injecte uniquement le HTML dans la page, mais cela n'exécute pas automatiquement les scripts JavaScript liés à ce contenu ... 
    ToutDoux : ce serait quand même plus propre, AMHA, que des frames. 
    Essayer de trouver un expert quand le code sera sur github
*/     
function loadScriptContent(tabId, scriptName) {
    const contentDiv = document.getElementById('tab-content-' + tabId);
    fetch(scriptName)
        .then(response => response.text())
        .then(data => {
            contentDiv.innerHTML = data;

            // Ajouter les événements aux boutons du contenu chargé
            const buttons = contentDiv.querySelectorAll('.add-tab-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const scriptParam = this.dataset.script;
                    const titre = this.dataset.titre;
                    tabCount++;
                    createTab(tabCount, scriptParam, true,titre);
                });
            });
            
            if (scriptName) {    
                // Injecter dynamiquement le script JS associé après chargement du contenu
                const script = document.createElement('script');
                // Extraire le nom de fichier sans l'extension
                const nomdefichiersansextension = scriptName.split('.').slice(0, -1).join('.');
                script.src = 'bibliotheque_js/'+nomdefichiersansextension+'.js?v=<?= time(); ?>';
                document.body.appendChild(script);
            } 
        })
        .catch(error => console.error('Erreur lors du chargement du script:', error));
};

// Fonction pour supprimer un onglet
function removeTab(tabId) {
    const tab = document.querySelector(`.tab[data-tab-id="${tabId}"]`);
    const tabContent = document.getElementById('tab-content-' + tabId);
    
    if (tab && tabContent) {
        tab.remove();
        tabContent.remove();

        // Si l'onglet supprimé était actif, activer le dernier onglet restant
        if (tab.classList.contains('active')) {
            const remainingTabs = document.querySelectorAll('.tab');
            if (remainingTabs.length > 0) {
                const lastTabId = remainingTabs[remainingTabs.length - 1].dataset.tabId;
                setActiveTab(lastTabId);
            }
        }
    }
};
function fa_bonjourLeMonde(){
    fa_showModal("Bonjour le monde.", title = "Information", showButtons = {yes: false, no: false, cancel: true},{yes: "Oui", no: "Non", cancel: "Bonjour, vous."});
};
function pa_trierLaTable(table, columnIndex, ascending) {
    const rows = Array.from(table.querySelectorAll("tbody tr"));
    const visibleRows = rows.filter(row => row.style.display !== "none"); // Ne trier que les lignes visibles

    // Trier les lignes visibles
    const sortedRows = visibleRows.sort((rowA, rowB) => {
        const cellA = rowA.children[columnIndex]?.textContent.trim() || "";
        const cellB = rowB.children[columnIndex]?.textContent.trim() || "";

        let comparison = 0;

        // Vérifier si les valeurs sont des dates au format JJ/MM/AAAA HH:MM:SS
        const dateA = fa_parseFrenchDate(cellA);
        const dateB = fa_parseFrenchDate(cellB);

        if (dateA && dateB) {
            comparison = dateA.getTime() - dateB.getTime(); // Comparaison de dates
        } else if (!isNaN(cellA) && !isNaN(cellB)) {
            comparison = parseFloat(cellA) - parseFloat(cellB); // Comparaison numérique
        } else {
            comparison = cellA.localeCompare(cellB); // Comparaison textuelle
        }

        return ascending ? comparison : -comparison;
    });

    // Réordonner les lignes triées dans le DOM
    const tbody = table.querySelector("tbody");
    sortedRows.forEach(row => tbody.appendChild(row));
}


// Fonction pour convertir une date au format JJ/MM/AAAA HH:MM:SS en objet Date
function fa_parseFrenchDate(dateStr) {
    const regex = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})$/;
    const match = dateStr.match(regex);
    if (match) {
        const [_, day, month, year, hours, minutes, seconds] = match;
        return new Date(`${year}-${month}-${day}T${hours}:${minutes}:${seconds}`);
    }
    return null; // Retourne null si le format ne correspond pas
}




function convertirEnDateTimeLocal(dateString) {
    // Séparer la date et l'heure
    const [datePart, timePart] = dateString.split(' ');

    // Séparer le jour, le mois et l'année
    const [day, month, year] = datePart.split('/');

    // Construire la date au format YYYY-MM-DDTHH:MM
    const dateTimeLocal = `${year}-${month}-${day}T${timePart.slice(0, 5)}`; // On garde seulement HH:MM

    return dateTimeLocal;
}

function pa_traite_CSV(perimetre,formdataFichierCSV) {
    const traitementRequest = new XMLHttpRequest();
    traitementRequest.open('POST', perimetre, true);
    pa_afficherSablier();

    traitementRequest.onreadystatechange = function() {
        if (traitementRequest.readyState === 4) {
            pa_effacerSablier();
            if (traitementRequest.status === 200) {
                const response = JSON.parse(traitementRequest.responseText);
                if (response.succes === true) {
                    const messageCSV = response.message;
                    const selectedIds = response.tableauIds;

                    if (selectedIds.length > 0) {
                        // Lancer la requête vers Google Books
                        const googleBooksRequest = new XMLHttpRequest();
                        googleBooksRequest.open('POST', 'bibliotheque_cherche_infos_ws_google_sur_id.php?perimetre=ISBN13', true);
                        googleBooksRequest.setRequestHeader('Content-Type', 'application/json');
                        pa_afficherSablier();

                        googleBooksRequest.onreadystatechange = function() {
                            if (googleBooksRequest.readyState === 4) {
                                pa_effacerSablier();
                                if (googleBooksRequest.status === 200) {
                                    fa_showModal(messageCSV, "Information", { yes: false, no: false, cancel: true }, { yes: "Continuer", no: "Annuler", cancel: "Dont acte" });
                                } else {
                                    pa_retour_erreur_ajax(googleBooksRequest.status);
                                }
                            }
                        };
                        googleBooksRequest.send(JSON.stringify({ selectedIds: selectedIds }));
                    } else {
                        fa_showModal(response.message, "Information", { yes: false, no: false, cancel: true }, { yes: "Continuer", no: "Annuler", cancel: "Dont acte" });
                    }
                } else {
                    fa_showModal(response.message, "Erreur", { yes: false, no: false, cancel: true }, { yes: "Continuer", no: "Annuler", cancel: "Je recommence..." });
                }
            } else {
                pa_retour_erreur_ajax(traitementRequest.status);
            }
        }
    };
    traitementRequest.send();
};
function pa_afficheOuCacheLesFiltres(){
    const tableHeaders = document.querySelectorAll('table thead');
    // Parcourez chaque `thead` pour activer/désactiver la position sticky
    tableHeaders.forEach(header => {
        if (header.classList.contains('sticky')) {
            header.classList.remove('sticky');
        } else {
            header.classList.add('sticky');
        }
    });

    const filterInputs = document.querySelectorAll('table input');
    if (filterInputs.length === 0) {
        console.warn('Aucun input de filtre trouvé dans les tables.');
        return; // Quitter la fonction si aucun input n'est trouvé
    }
    
    filterInputs.forEach(input => {
        if (input.style.display === 'none') {
            input.style.display = 'block';
        } else {
            input.style.display = 'none';
        }
    });
};
function pa_chargerLAssociation(id,bSuppressionPossible){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_associer_les_livres_aux_series_data.php?id='+id, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if(xhr.status == 200) {
                var seriesData = JSON.parse(xhr.responseText);
                pa_AfficheLAssociation(seriesData,bSuppressionPossible);
            } else {
            pa_retour_erreur_ajax(xhr.status); 
            }
        }    
    };
    xhr.send();
}

function pa_AfficheLAssociation(seriesData,bSuppressionPossible) {
    const tagsContainer = document.getElementById("tagsContainer");

    // Vider le conteneur des tags
    tagsContainer.innerHTML = "";

    // Ajouter un tag pour chaque livre associé à la série
    seriesData.forEach(data => {
        const tag = document.createElement("div");
        tag.className = "tag";
        if (!isNaN(Number(data.Etat)) && Number(data.Etat) === 1) {
            tag.style.backgroundColor = "#b3d9ff"; // Bleu clair
        } else {
            tag.style.backgroundColor = "#ffa500"; // Orange    
        }    
        tag.style.color = "#000"; // Texte noir

        // Définir les attributs pour le tag
        tag.setAttribute("idlivre", data.IDLivres);
        tag.setAttribute("idlivreparserie", data.idlivreparserie);
        tag.setAttribute("idserie", data.IDSeries);
        tag.setAttribute("genre", data.genre);
            if (bSuppressionPossible === true){
            // Ajouter le contenu du tag
            tag.innerHTML = `
                <span>${data.Titre}</span>
                <span class="close">&times;</span>
            `;
            // Ajouter un événement pour supprimer le tag
            tag.querySelector(".close").addEventListener("click", function () {
                // Ajoutez ici une logique pour dissocier le livre de la série si nécessaire,
                // par exemple envoyer une requête AJAX au serveur pour mettre à jour la base de données.
                const idlivreparserie = tag.getAttribute("idlivreparserie");
                const idserie = tag.getAttribute("idserie");
                pa_supprimeLAssociation(idlivreparserie,idserie);
            });
        } else {
            tag.innerHTML = `
                <span>${data.Titre}</span>
            `;
        }
        tag.addEventListener("dblclick", function () {
            const selectedBookId = tag.getAttribute("idlivre");
            const genre = tag.getAttribute("genre");

            if (selectedBookId && genre) {
                const url = `bibliotheque_le_livre.php?id=${selectedBookId}&genre=${genre}&perimetre=Modification`;
                const tabTitle = "Modification livre";
                parent.pl_createDynamicTab(tabTitle, url);
            } else {
                console.error("Les attributs nécessaires (idlivre ou genre) sont manquants sur le tag.");
            }
        });

        // Ajouter le tag au conteneur
        tagsContainer.appendChild(tag);
    });
}
function pa_supprimeLAssociation(livreserieid,idserie){
    const data = new FormData();
    data.append('perimetre', 'Suppression');
    data.append('livreserieid', livreserieid); // ID du livre depuis l'attribut
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'bibliotheque_associer_les_livres_aux_series_crud.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if(xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.succes) {
                            pa_chargerLAssociation(idserie,true);
                        } else {
                            fa_showModal(response.message, title = "Avertissement", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Vu"});
                        };
                    }   catch (e) {
                    };    
                };    
            } else {
            pa_retour_erreur_ajax(xhr.status); 
            }
        }
    };
    xhr.send(data);
}


function pa_chargerSeriesTable() {
    pa_afficherSablier();
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_les_series_data.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4){
            pa_effacerSablier();
            if (xhr.status == 200) {
                // Réponse du serveur
                var seriesData = JSON.parse(xhr.responseText);
                pl_AfficheLaTableDesSeries(seriesData);
                pl_initialiserLesTrisSeries();
            }else {
                pa_retour_erreur_ajax(xhr.status);
            };
        }
    };

    xhr.send();
}
function fa_supprimerAvantPremiereLettre(chaine) {
    return chaine.replace(/^[^a-zA-Z]*([a-zA-Z].*)$/, "$1");
}
