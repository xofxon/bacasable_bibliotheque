<?php
// Vous devez vous assurer que la variable $perimetre est définie
$perimetre = $_GET['perimetre'] ?? 'Consultation'; // Valeur par défaut si $perimetre n'est pas passé
$idSerie = $_GET['idSerie'] ?? 0; // Valeur par défaut si $perimetre n'est pas passé
header('Content-Type: application/javascript');
?>
let ancienEtat=0;
let ancienGenre=0;
// Charger les détails de la série
function pl_chargeSerieDetails(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_les_series_data.php?id=' + id, true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        var serieDetails = JSON.parse(xhr.responseText);
                        pl_afficherSerieDetails(serieDetails);
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
function pl_afficherSerieDetails(serie) {
    document.getElementById('nom').value = serie.Nom;
    document.getElementById('site').value = serie['Adresse sur site'];
    document.getElementById('commentaire').value = serie.Commentaires;
    document.getElementById('genre').value = serie.Genre;
    document.getElementById('presents').value = serie['Nombre livres présents'];
    document.getElementById('courses').value = serie['Nombre liste de courses'];
    pa_chargerLAssociation(<?= $idSerie ?>,true);
    var selectElement = document.getElementById('etat');
    selectElement.value=serie.Etat
}    
// Réinitialiser les champs du formulaire
function pl_resetForm() {
    document.getElementById('serieFormulaire').reset();
    document.getElementById('etat').value=ancienEtat;
    document.getElementById('genre').value=ancienGenre;
}
<?php if ($perimetre === 'Creation'): ?>
    // Réinitialiser les champs si nécessaire
    document.getElementById('resetBtn').addEventListener('click', function () {
        pl_resetForm();
    });
<?php endif; ?>
<?php if ($perimetre === 'Modification' || $perimetre === 'Consultation'): ?>    
    // Réinitialiser les champs si nécessaire
    document.getElementById('resetBtn').addEventListener('click', function () {
        pl_chargeSerieDetails(<?= $idSerie ?>);
    });

<?php endif; ?>    
<?php if ($perimetre === 'Suppression'): ?>  
    document.getElementById('enregistrerBtn').addEventListener('click', function () {
        if (fl_VerificationsDuFormulaire()) {
        pl_SuppressionSerie();  
        } else {
            fa_showModal('Avertissement', 'On ne peut pas supprimer cette série.',{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
        }
    });
function fl_VerificationsDuFormulaire() {
    let presents = document.getElementById('presents').value;
    let courses = document.getElementById('courses').value;
    presents = Number(presents);
    courses = Number(courses);
    // Tester si les deux sont égales à 0
    if (presents === 0 && courses === 0) {
        return true;
    } else {
        return false;
    }
}

function pl_SuppressionSerie(){
    const xhr = new XMLHttpRequest();
    const data = new FormData();
    data.append('perimetre', '<?= $perimetre ?>');
    data.append('id', <?= $idSerie ?>);
    xhr.open('POST', 'bibliotheque_la_serie_crud.php', true);
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
            pl_enregistreSerieDetails(); 
        }
    });
    // Validation du formulaire
function fl_VerificationsDuFormulaire() {
    let nom = document.getElementById('nom').value;
    // Vérifie que tous les champs requis ne sont pas vides
    if (nom.trim() === "") {
        fa_showModal('Avertissement', "Le champ 'nom' est obligatoire.",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Continuer"});
        var champObligatoire = document.getElementById('nom');
        champObligatoire.focus(); // Donne le focus au champ
        return false;
    }
    // Si tout est valide
    return true;
}
    // Sauvegarder les détails de la série (création ou modification)
function pl_enregistreSerieDetails() {
    ancienEtat=document.getElementById('etat').value;
    const xhr = new XMLHttpRequest();
    var form = document.getElementById("serieFormulaire");
    const data = new FormData(form);
    // Ajoute des données supplémentaires qui ne sont pas dans le formulaire
    data.append('perimetre', '<?= $perimetre ?>');
    data.append('id', <?= $idSerie ?>);
    xhr.open('POST', 'bibliotheque_la_serie_crud.php', true);
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
                                pl_chargeSerieDetails(<?= $idSerie ?>);
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
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($perimetre === 'Consultation' || $perimetre === 'Modification' || $perimetre === 'Suppression'): ?>
        pl_chargeSerieDetails(<?= $idSerie ?>);
    <?php endif; ?>
    // Gestion du bouton "Retour"
    document.getElementById('retourBtn').addEventListener('click', function () {
        pl_unlockAndCloseTab();
    });
});
