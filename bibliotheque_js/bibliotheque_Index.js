let tabCount = 3;
const initialScripts = ['script1.php', 'script2.php', 'script3.php'];

// Créer les trois onglets initiaux
initialScripts.forEach((script, index) => {
    createTab(index + 1, script, false); // Onglets par défaut (non supprimables)
});

function createTab(tabId, scriptName, isDynamic = true) {
    // Créer un nouvel onglet
    const newTab = document.createElement('div');
    newTab.classList.add('tab');
    newTab.innerText = 'Onglet ' + tabId + ' (' + scriptName + ')';
    newTab.dataset.tabId = tabId;
    newTab.dataset.script = scriptName;

    // Ajouter la croix de fermeture si l'onglet est dynamique
    if (isDynamic) {
        newTab.classList.add('dynamic');
        const closeBtn = document.createElement('span');
        closeBtn.innerHTML = '✖';
        closeBtn.classList.add('close-btn');
        closeBtn.addEventListener('click', function(event) {
            event.stopPropagation(); // Empêche le clic de changer l'onglet actif
            removeTab(tabId);
        });
        newTab.appendChild(closeBtn);
    }

    newTab.addEventListener('click', function () {
        setActiveTab(newTab.dataset.tabId, newTab.dataset.script);
    });

    // Ajouter l'onglet à la barre des onglets
    document.getElementById('tab-container').appendChild(newTab);

    // Créer un nouveau contenu d'onglet
    const newTabContent = document.createElement('div');
    newTabContent.classList.add('tab-content');
    newTabContent.id = 'tab-content-' + tabId;

    // Ajouter le contenu de l'onglet (chargement dynamique du script PHP)
    document.getElementById('tab-content-container').appendChild(newTabContent);
    
    // Charger le contenu du script PHP
    loadScriptContent(tabId, scriptName);

    // Activer le premier onglet par défaut
    if (tabId === 1) {
        setActiveTab(tabId, scriptName);
    }
}

function setActiveTab(tabId, scriptName) {
    // Désactiver tous les onglets et contenus
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    // Activer l'onglet et son contenu correspondant
    document.querySelector(`.tab[data-tab-id="${tabId}"]`).classList.add('active');
    document.getElementById('tab-content-' + tabId).classList.add('active');
}

function loadScriptContent(tabId, scriptName) {
    const contentDiv = document.getElementById('tab-content-' + tabId);
    fetch(scriptName)
        .then(response => response.text())
        .then(data => {
            contentDiv.innerHTML = data;

            // Ajouter les événements pour les boutons dans le contenu chargé
            const buttons = contentDiv.querySelectorAll('.add-tab-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const scriptParam = this.dataset.script;
                    tabCount++;
                    createTab(tabCount, scriptParam);
                });
            });
        })
        .catch(error => console.error('Erreur lors du chargement du script:', error));
}

function removeTab(tabId) {
    // Supprimer l'onglet et son contenu correspondant
    const tab = document.querySelector(`.tab[data-tab-id="${tabId}"]`);
    const tabContent = document.getElementById('tab-content-' + tabId);
    
    if (tab && tabContent) {
        tab.remove();
        tabContent.remove();

        // Si l'onglet supprimé était actif, activer un autre onglet (le dernier par défaut)
        if (tab.classList.contains('active')) {
            const remainingTabs = document.querySelectorAll('.tab');
            if (remainingTabs.length > 0) {
                const lastTabId = remainingTabs[remainingTabs.length - 1].dataset.tabId;
                setActiveTab(lastTabId);
            }
        }
    }
}
