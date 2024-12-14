document.addEventListener('DOMContentLoaded', function() {
    chargerListeFichiers();

    document.getElementById('importCSVButton').addEventListener('click', function() {
        const url = 'bibliotheque_telechargement_csv.php';
        const tabTitle = 'Importation CSV';
        parent.pl_createDynamicTab(tabTitle, url);
    });
    document.getElementById('refreshButton').addEventListener('click', chargerListeFichiers);

    document.getElementById('TraitementCSVButton').addEventListener('click', function() {
        // Obtenir l'ID de la ligne sélectionnée
        const idFichierImporte = this.dataset.selectedId;

    if (!idFichierImporte) {
        fa_showModal('Veuillez sélectionner une ligne dans le tableau.', 'Erreur');
        return;
    }
    // Appel AJAX pour bibliotheque_traitement_csv.php avec le périmètre "traitement"
    pa_traite_CSV(`bibliotheque_traitement_csv.php?perimetre=retraitement&IDFichierImporte=${idFichierImporte}`,null);
    });
});

function chargerListeFichiers() {
    fetch('bibliotheque_fichiersimportes_data.php?perimetre=liste')
        .then(response => response.json())
        .then(data => afficherListeFichiers(data))
        .catch(error => fa_showModal('Erreur lors du chargement des fichiers importés : ' + error, 'Erreur'));
}

function afficherListeFichiers(fichiers) {
    const tableBody = document.getElementById('fichiersTableBody');
    tableBody.innerHTML = '';
    fichiers.forEach(fichier => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${fichier.IdFichierImporte}</td>
            <td>${fichier.NomDuFichier}</td>
            <td>${fichier.Dateimportation}</td>
            <td>${fichier.Caracteristiques}</td>
            <td>${fichier.Typedefichier}</td>
        `;
        row.addEventListener('click', () => {
            // Retirer la classe "selected" de toutes les lignes
            document.querySelectorAll('#fichiersTableBody tr').forEach(tr => tr.classList.remove('selected'));

            // Ajouter la classe "selected" à la ligne cliquée
            row.classList.add('selected');

            // Stocker l'ID de la ligne sélectionnée pour un usage ultérieur
            document.getElementById('TraitementCSVButton').dataset.selectedId = fichier.IdFichierImporte;
            chargerDetailFichier(fichier.IdFichierImporte)
        });
        tableBody.appendChild(row);
    });
}

function chargerDetailFichier(id) {
    fetch(`bibliotheque_fichiersimportes_data.php?perimetre=détail&IDFichierImporte=${id}`)
        .then(response => response.json())
        .then(data => afficherDetailFichier(data))
        .catch(error => fa_showModal('Erreur lors du chargement du détail du fichier : ' + error, 'Erreur'));
}

function afficherDetailFichier(fichier) {
    const detailDiv = document.getElementById('fichierDetail');
    detailDiv.innerHTML = `
        <p><strong>ID:</strong> ${fichier.IdFichierImporte}</p>
        <p><strong>Nom du Fichier:</strong> ${fichier.NomDuFichier}</p>
        <p><strong>Date d'Importation:</strong> ${fichier.Dateimportation}</p>
        <p><strong>Description:</strong> ${fichier.DescriptionCourte}</p>
        <p><strong>Type de Fichier:</strong> ${fichier.Typedefichier === 1 ? 'CSV' : 'Autre'}</p>
        <p><strong>Caractéristiques:</strong> ${fichier.Caracteristiques}</p>
    `;

    // Créer un conteneur en grille pour afficher les informations sur deux colonnes
    const gridContainer = document.createElement('div');
    gridContainer.style.display = 'grid';
    gridContainer.style.gridTemplateColumns = '1fr 1fr';
    gridContainer.style.gap = '10px';

    // Contenu du fichier (Colonne de gauche)
    const contenuDiv = document.createElement('div');
    contenuDiv.innerHTML = `<strong>Contenu du Fichier (ISBN):</strong>`;
    const ul = document.createElement('ul');
    if (Array.isArray(fichier.ContenuDuFichier) && fichier.ContenuDuFichier.length > 0) {
        fichier.ContenuDuFichier.forEach((isbn, index) => {
            const li = document.createElement('li');
            li.textContent = `${index + 1}. ${isbn}`;
            ul.appendChild(li);
        });
    } else {
        ul.innerHTML = `<li>Aucun ISBN disponible</li>`;
    }
    contenuDiv.appendChild(ul);
    gridContainer.appendChild(contenuDiv);

    // Compte rendu d'importation (Colonne de droite)
    const compteRenduDiv = document.createElement('div');
    compteRenduDiv.innerHTML = `<strong>Compte Rendu d'Importation:</strong>`;
    
    // Diviser CompteRenduImportation en lignes et afficher chaque ligne
    const compteRenduLines = fichier.CompteRenduImportation.split('\n');
    compteRenduLines.forEach(line => {
        const lineDiv = document.createElement('div');
        lineDiv.textContent = line; // Chaque ligne est un div séparé
        compteRenduDiv.appendChild(lineDiv);
    });
    
    gridContainer.appendChild(compteRenduDiv);

    // Ajouter le conteneur grille au détail
    detailDiv.appendChild(gridContainer);
}
