let tabCount = 0;
let activeTab = null;

function pl_createTab(tabTitle, scriptName, isRemovable) {
    tabCount++;
    // Créer l'onglet
    const tabContainer = document.getElementById('tab-container');
    const tabContentContainer = document.getElementById('tab-content-container');

    if (!tabContainer || !tabContentContainer) {
        //console.error("1 Les conteneurs d'onglets ou de contenu ne sont pas trouvés.");
        return;
    }

    const tabButton = document.createElement('div');
    tabButton.className = 'tab';
    tabButton.innerText = tabTitle;
    tabButton.id = 'tab-' + tabCount;

    // Ajouter le contenu correspondant dans une iframe
    const tabContentDiv = document.createElement('div');
    tabContentDiv.className = 'tab-content-container';
    tabContentDiv.id = 'tab-content-' + tabCount;

    const iframe = document.createElement('iframe');
    iframe.src = scriptName;
    iframe.width = '100%';
    iframe.height = '1000px'; // Hauteur ajustable selon le besoin
    iframe.frameBorder = '0';
    tabContentDiv.appendChild(iframe);

    tabContentContainer.appendChild(tabContentDiv);

    // Ajouter l'événement de changement d'onglet
    tabButton.addEventListener('click', function() {
        const clickedId = event.target.id;
        const lastIndex = clickedId.lastIndexOf('-');
        if (lastIndex !== -1) {
            const result = clickedId.substring(lastIndex + 1);
            pl_activateTab(result);
        }
    });

    // Si l'onglet est supprimable, ajouter un bouton de suppression
    if (isRemovable) {
        const closeButton = document.createElement('button');
        closeButton.innerText = 'X';
        closeButton.title = 'Fermer cet onglet';
        closeButton.className = 'close-tab-btn';
        closeButton.onclick = function(event) {
            event.stopPropagation();
            tabContainer.removeChild(tabButton);
            tabContentContainer.removeChild(tabContentDiv);

            // Si on ferme l'onglet actif, activer le premier onglet
            if (activeTab === tabCount) {
                const firstTab = tabContainer.querySelector('.tab');
                if (firstTab) {
                    const firstTabId = firstTab.id.split('-')[1];
                    pl_activateTab(firstTabId);
                }
            }
        };
        tabButton.appendChild(closeButton);
    }

    tabContainer.appendChild(tabButton);

    // Activer automatiquement le dernier onglet créé
    //  Pas très joli car on les voit tous s'afficher mais je ne vois pas comment faire autrement...
    pl_activateTab(tabCount);

}

// Fonction pour activer un onglet
function pl_activateTab(tabId) {
    // Désactiver tous les onglets et masquer leur contenu
    const allTabs = document.querySelectorAll('.tab');
    const allTabContents = document.querySelectorAll('.tab-content-container');

    allTabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    allTabContents.forEach(content => {
        content.classList.remove('active');
    });

    // Activer l'onglet cliqué et afficher son contenu
    const tabToActivate = document.getElementById('tab-' + tabId);
    const contentToActivate = document.getElementById('tab-content-' + tabId);

    if (tabToActivate && contentToActivate) {
        tabToActivate.classList.add('active');
        contentToActivate.classList.add('active');
    }

    activeTab = tabId;
}


// Fonction pour créer des onglets dynamiques (supprimables)
function pl_createDynamicTab(tabTitle, scriptName) {
    pl_createTab(tabTitle, scriptName, true);
}

function pl_init() {
    const scriptArray = ['bibliotheque_les_livres.php?genre=110146&perimetre=unitaire&condition=0','bibliotheque_les_livres.php?genre=110147&perimetre=unitaire&condition=0','bibliotheque_les_livres.php?genre=110148&perimetre=unitaire&condition=0', 
        'bibliotheque_les_livres.php?genre=-1&perimetre=listeDesCourses&condition=1',
        'bibliotheque_les_series.php',
        'bibliotheque_associer_les_livres_aux_series.php',
        'bibliotheque_les_autres_tables.php', 'bibliotheque_les_outils.php'];
    const titleArray = ['BD','Littérature','Bibliophilie', 'Liste des courses','Séries','Associations','Autres tables','Outils'];
    
    for (let i = 0; i < scriptArray.length; i++) {
        pl_createTab(titleArray[i], scriptArray[i], false);
    }
    pl_activateTab(1);
};

// Création des onglets au chargement de la page à partir des tableaux
document.addEventListener('DOMContentLoaded', function() {
    pl_init();
});
