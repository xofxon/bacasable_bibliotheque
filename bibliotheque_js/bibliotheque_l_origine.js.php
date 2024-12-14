<?php
// Vous devez vous assurer que la variable $perimetre est définie
$perimetre = $_GET['perimetre'] ?? 'Consultation'; // Valeur par défaut si $perimetre n'est pas passé
$idOrigine = $_GET['idOrigine'] ?? 0; // Valeur par défaut si $perimetre n'est pas passé
header('Content-Type: application/javascript');
?>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($perimetre === 'Consultation' || $perimetre === 'Modification' || $perimetre === 'Suppression'): ?>
        pl_ChargeOrigineDetails(<?= $idOrigine ?>);
    <?php endif; ?>   

    // Gestion du bouton "Retour"
    document.getElementById('retourBtn').addEventListener('click', function () {
        unlockAndCloseTab(idLivre);
    });
});
// Charger les détails du livre
function pl_ChargeOrigineDetails(id, genre) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_les_origines_data.php?id=' + id, true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        var origineDetails = JSON.parse(xhr.responseText);
                        pl_afficherOrigineDetails(origineDetails);
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
function unlockAndCloseTab(id) {
    // Simuler le déblocage et fermer l'onglet
    window.close(); 
}
<?php if ($perimetre === 'Consultation' || $perimetre === 'Suppression'): ?>
    function pl_afficherOrigineDetails(origine) {
    document.getElementById('CodeInterne').value = origine.CodeInterne;
    document.getElementById('Nom').value = origine.Nom;
    document.getElementById('Adresse').value = origine.Adresse;
    document.getElementById('Etat').value=origine.Etat;
    document.getElementById('Gestion').value=origine.Gestion;
}
<?php endif; ?>
<?php if ($perimetre === 'Creation'): ?>
    // Réinitialiser les champs si nécessaire
    document.getElementById('resetBtn').addEventListener('click', function () {
        pl_resetForm();
    });
<?php endif; ?>
<?php if ($perimetre === 'Modification'): ?>    
    // Réinitialiser les champs si nécessaire
    document.getElementById('resetBtn').addEventListener('click', function () {
        pl_ChargeOrigineDetails(<?= $idOrigine ?>);
    });

<?php endif; ?>    
<?php if ($perimetre === 'Creation' || $perimetre === 'Modification'): ?>
function pl_afficherOrigineDetails(origine) {
    document.getElementById('CodeInterne').value = origine.CodeInterne;
    document.getElementById('Nom').value = origine.Nom;
    document.getElementById('Adresse').value = origine.Adresse;
    if (origine.Etat=='Actif') {
        etatSelect.value=0;
    } else {
        etatSelect.value=1;
    }    
}
    // Validation du formulaire
function fl_VerificationsDuFormulaire() {
    let CodeInterne = document.getElementById('CodeInterne').value;
    return CodeInterne.trim() !== ""; // Vérifie que le CodeInterne n'est pas vide
}

// Sauvegarder les détails de l'origine (création ou modification)
function pl_sauveOrigineDetails() {
    const xhr = new XMLHttpRequest();
    var form = document.getElementById("origineFormulaire");
    const data = new FormData(form);

    // Ajoute des données supplémentaires qui ne sont pas dans le formulaire
    data.append('perimetre', '<?= $perimetre ?>');
    data.append('id', <?= $idOrigine ?>);
    xhr.open('POST', 'bibliotheque_l_origine_crud.php', true);
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
                            pl_resetForm(); // Réinitialise le formulaire en cas de succès
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
            pl_sauveOrigineDetails();
        } else {
            fa_showModal('Erreur', 'Veuillez remplir tous les champs obligatoires.',{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "D'accord?"});
        }
    });
    // Réinitialiser les champs du formulaire
    function pl_resetForm() {
        document.getElementById('origineFormulaire').reset();
    }

<?php endif; ?>
