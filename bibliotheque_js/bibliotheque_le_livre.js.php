<?php
// Vous devez vous assurer que la variable $perimetre est définie
$perimetreDOrigine = $_GET['perimetreDOrigine'] ?? ''; // Valeur par défaut si $perimetre n'est pas passé
$perimetre = $_GET['perimetre'] ?? 'Consultation'; // Valeur par défaut si $perimetre n'est pas passé
$idLivre = $_GET['idLivre'] ?? 0; // Valeur par défaut si $perimetre n'est pas passé
$genre = $_GET['genre'] ?? 0; // Valeur par défaut si $perimetre n'est pas passé
header('Content-Type: application/javascript');
?>
let ancienEtat=0;
let ancienGenre=0;
// Charger les détails du livre
function pl_chargeLivreDetails(id, genre) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_les_livres_data.php?id=' + id + '&genre=' + genre, true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        var livreDetails = JSON.parse(xhr.responseText);
                        pl_afficherLivreDetails(livreDetails);
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
function pl_unlockAndCloseTab() {
    // Simuler le déblocage et fermer l'onglet
    //  Mais cela ne fonctionne pas //Toutdoux : comprendre et faire fonctionner
    window.close(); 
}
// Validation du formulaire
function fl_VerificationsDuFormulaire() {
    let titre = document.getElementById('titre').value;
    let isbn10 = document.getElementById('isbn10').value;
    let isbn13 = document.getElementById('isbn13').value;

    // Vérifie que tous les champs requis ne sont pas vides
    if (titre.trim() === "") {
        fa_showModal('Avertissement', "Le champ 'Titre' est obligatoire.",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
        var champObligatoire = document.getElementById('titre');
        champObligatoire.focus(); // Donne le focus au champ
        return false;
    }
    if (isbn10.trim() === "") {
        fa_showModal('Avertissement', "Le champ 'ISBN10' est obligatoire.",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
        var champObligatoire = document.getElementById('isbn10');
        champObligatoire.focus(); // Donne le focus au champ
        return false;
    }
    if (isbn13.trim() === "") {
        fa_showModal('Avertissement', "Le champ 'ISBN13' est obligatoire.",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
        var champObligatoire = document.getElementById('isbn13');
        champObligatoire.focus(); // Donne le focus au champ
        return false;
    }
    // Si tout est valide
    return true;
}
function pl_afficherLivreDetails(livre) {
    document.getElementById('titre').value = livre.Titre;
    document.getElementById('isbn10').value = livre.ISBN10;
    document.getElementById('isbn13').value = livre.ISBN13;
    document.getElementById('olid').value = livre.OLID;
    document.getElementById('commentaire').value = livre.Commentaires;
    document.getElementById('genre').value = livre.Genre;
    document.getElementById('idorigines').value = livre.idorigines;
    document.getElementById('description').value = livre.description;
    document.getElementById('auteurs').value=JSON.parse('"' + livre.auteurs + '"');
    document.getElementById('daterecuperation').value = convertirEnDateTimeLocal(livre.DateRecuperation)
    document.getElementById('lienReference').value = livre.lienReference;
    var selectElement = document.getElementById('etat');
    selectElement.value=livre.Etat
    if (livre.serieID === 0 || livre.serieID === null) {
        document.getElementById('groupeDeLaSerie').style.display = 'none'; // Cache le groupe de la série
    } else {
        document.getElementById('site').value = livre.serieSite;
        document.getElementById('serie').value = livre.serieNom;
        pa_chargerLAssociation(livre.serieID,false);
    }    
    var jsonString = "";
    var formattedJson ="";
    if (livre.jsonmodifie.trim() !== ""){    
        jsonString = livre.jsonmodifie;
        formattedJson = JSON.stringify(JSON.parse(jsonString), null, 2);
        document.getElementById('jsonInput').value = formattedJson;
    }    
    jsonString = "";
    formattedJson ="";
    if (livre.jsonimporte.trim() !== ""){
        jsonString = livre.jsonimporte;
        formattedJson = JSON.stringify(JSON.parse(jsonString), null, 2);
        document.getElementById('jsonOutput').value = formattedJson;
    } else {
        if (livre.OLJson.trim() !== ""){
            jsonString = livre.OLJson;
            formattedJson = JSON.stringify(JSON.parse(jsonString), null, 2);
            document.getElementById('jsonOutput').value = formattedJson; 
        }    
    }
}
// Réinitialiser les champs du formulaire
function pl_resetForm() {
    document.getElementById('livreFormulaire').reset();
    document.getElementById('etat').value=ancienEtat;
    document.getElementById('genre').value=ancienGenre;
}
<?php if ($perimetre === 'Creation'): ?>
    // Réinitialiser les champs si nécessaire
    document.getElementById('resetBtn').addEventListener('click', function () {
        pl_resetForm();
    });
<?php endif; ?>
<?php if ($perimetre === 'Modification'): ?>    
    // Réinitialiser les champs si nécessaire
    document.getElementById('resetBtn').addEventListener('click', function () {
        pl_chargeLivreDetails(<?= $idLivre ?>, <?= $genre ?>);
    });

<?php endif; ?>    
<?php if ($perimetre === 'Suppression'): ?>  
    document.getElementById('enregistrerBtn').addEventListener('click', function () {
        if (fl_VerificationsDuFormulaire()) {
        pl_SuppressionLivre();  
        } else {
            fa_showModal('Avertissement', 'On ne peut pas supprimer ce titre.',{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
        }
    });
function pl_SuppressionLivre(){
    const xhr = new XMLHttpRequest();
    var form = document.getElementById("livreFormulaire");
    const data = new FormData(form);
    // Ajoute des données supplémentaires qui ne sont pas dans le formulaire
    data.append('perimetre', '<?= $perimetre ?>');
    data.append('id', <?= $idLivre ?>);
    data.delete('jsonOutput');
    xhr.open('POST', 'bibliotheque_le_livre_crud.php', true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.succes) {
                            fa_showModal(response.message, title = "Information", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Super"});
                            pl_resetForm();
                        };
                        if (!response.succes) {
                            fa_showModal(response.message, title = "Avertissement", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Vu"});
                        };
                    }   catch (e) {
                    };    
                };    
            } else {
                pa_retour_erreur_ajax(xhr.status);
            };
        };
    }    
    xhr.send(data);
}    

<?php endif; ?>    

<?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
    // Gestion du bouton "Enregistrer" pour Création
    document.getElementById('enregistrerBtn').addEventListener('click', function () {
        if (fl_VerificationsDuFormulaire()) {
            pl_enregistreLivreDetails(); 
        }
    });
    // Sauvegarder les détails du livre (création ou modification)
function pl_enregistreLivreDetails() {
    ancienEtat=document.getElementById('etat').value;
    ancienGenre=document.getElementById('genre').value;
    const xhr = new XMLHttpRequest();
    var form = document.getElementById("livreFormulaire");
    const data = new FormData(form);
    // Ajoute des données supplémentaires qui ne sont pas dans le formulaire
    data.append('perimetre', '<?= $perimetre ?>');
    data.append('id', <?= $idLivre ?>);
    data.append('sansJsonOutput','Oui');
    <?php if ($perimetre === 'Creation'): ?>
        data.append('idorigines',6)
    <?php else: ?>    
        data.append('idorigines',document.getElementById('idorigines').value)   //  A ajouter au formulaire car la comboliste est désactivée, donc pas prise en compte dans le formulaire
    <?php endif; ?>    
    data.delete('jsonOutput');
    xhr.open('POST', 'bibliotheque_le_livre_crud.php', true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.succes) {
                            fa_showModal(response.message, title = "Information", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Super"});
                            <?php if ($perimetre === 'Creation'): ?>
                                pl_resetForm(); // Réinitialise le formulaire en cas de succès
                            <?php else: ?>    
                                pl_chargeLivreDetails(<?= $idLivre ?>, <?= $genre ?>);
                            <?php endif; ?>    
                        };
                        if (!response.succes) {
                            fa_showModal(response.message, title = "Avertissement", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Vu"});
                        };
                    }   catch (e) {
                    };    
                };    
            } else {
                pa_retour_erreur_ajax(xhr.status);
            };
        };
    }    
    xhr.send(data);
}
<?php endif; ?>
function pl_LanceGoogleISBN13(){
    const LanceGoogleISBN13=document.getElementById('LanceGoogleISBN13');
    if (LanceGoogleISBN13) {
        const selectedisbn13 = document.getElementById('isbn13').value
        if (selectedisbn13) {
            const url = 'https://www.google.com/search?q=' + selectedisbn13;
            pa_ouvreLeSite(url);
        }
    };
}
function pl_LanceGoogleISBN10(){
    const LanceGoogleISBN10=document.getElementById('LanceGoogleISBN10');
    if (LanceGoogleISBN10) {
        const selectedisbn10 = document.getElementById('isbn10').value
        if (selectedisbn10) {
            const url = 'https://www.google.com/search?q=' + selectedisbn10;
            pa_ouvreLeSite(url);
        }
    };
}
function pl_LanceAmazonISBN13(){
    const LanceAmazonISBN13=document.getElementById('LanceAmazonISBN13');
    if (LanceAmazonISBN13) {
        const selectedisbn13 = document.getElementById('isbn13').value
        if (selectedisbn13) {
            let url = 'https://www.amazon.fr/s?k=' + selectedisbn13;
            pa_ouvreLeSite(url);
        }

    };
}
function pl_LanceAmazonISBN10(selectedisbn10){
        if (selectedisbn10) {
            let url = 'https://www.amazon.fr/s?k=' + selectedisbn10;
            pa_ouvreLeSite(url);
        }
}

function pl_LanceAmazonetGoogleISBN13(){
    const LanceAmazonetGoogleISBN13=document.getElementById('LanceAmazonetGoogleISBN13');
    if (LanceAmazonetGoogleISBN13) {
        document.getElementById('LanceAmazonetGoogleISBN13').addEventListener('click', function () {
        const selectedisbn13 = document.getElementById('isbn13').value
        if (selectedisbn13) {
            //  ToutDoux
            //  Code non fonctionnel, car n'ouvre qu'un seul onglet ou nouveau navigateur (selon usage de focus=no ou pas)
            let urlAmazon = 'https://www.amazon.fr/s?k=' + selectedisbn13;
            let urlGoogle = 'https://www.google.com/search?q=' + selectedisbn13;
            pa_ouvreLeSite(urlAmazon);
            setTimeout(() => {
                pa_ouvreLeSite(urlGoogle);
            }, 100); // 100 ms de délai
        }
    }    
    )};
}

document.addEventListener('DOMContentLoaded', function () {
    <?php if ($perimetre === 'Consultation' || $perimetre === 'Modification' || $perimetre === 'Suppression'): ?>
        pl_chargeLivreDetails(<?= $idLivre ?>, <?= $genre ?>);
    <?php endif; ?>
    <?php if ($perimetre === 'Creation'): ?>
        <?php if ($perimetreDOrigine === 'listeDesCourses'): ?>
            document.getElementById('etat').value=0;
        <?php else: ?>
            document.getElementById('etat').value=1;
        <?php endif; ?>
        ancienEtat=document.getElementById('etat').value; 
        <?php if ($genre != '-1'): ?>
            document.getElementById('genre').value=-<?= $genre ?>;
        <?php endif; ?>
        ancienGenre=document.getElementById('genre').value;
    <?php endif; ?>
    // Gestion du bouton "Retour"
    document.getElementById('retourBtn').addEventListener('click', function () {
        pl_unlockAndCloseTab();
    });
    //  Pseudo lien référence
    document.getElementById('lienReference').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        const url = this.value;
        if (filter_var($url, FILTER_VALIDATE_URL) && (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)) {
            if (url) {
                pa_ouvreLeSite(url);
            }
        }    
    }
});
});
