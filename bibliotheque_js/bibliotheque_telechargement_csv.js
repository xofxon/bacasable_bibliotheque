function pl_uploadCSV() {
    const formData = new FormData();
    const fileInput = document.getElementById('csvfile');
    const file = fileInput.files[0];
    
    if (!file) {
        fa_showModal('Veuillez sélectionner un fichier CSV.', "Erreur", { yes: false, no: false, cancel: true }, { yes: "Continuer", no: "Annuler", cancel: "Je recommence..." });
        return;
    }
    
    formData.append('csvfile', file);
    formData.append('originalFileName', file.name); // Ajout du nom d'origine du fichier

    // Première requête : Envoi du CSV pour importation
    const uploadRequest = new XMLHttpRequest();
    let etat = document.getElementById('etat');
    uploadRequest.open('POST', 'bibliotheque_traitement_csv.php?etat=' + etat, true);
    pa_afficherSablier();
    
    uploadRequest.onreadystatechange = function() {
        if (uploadRequest.readyState === 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            
            if (uploadRequest.status === 200) {
                const response = JSON.parse(uploadRequest.responseText);
                
                // Vérifier si la réponse est positive pour continuer
                if (response.succes === true) {
                    const messageCSV = response.message;
                    const selectedIds = response.tableauIds;

                    if (selectedIds.length > 0) {
                        // Deuxième requête : Requête de traitement avec les IDs importés
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
                    // Afficher le message d'erreur retourné et arrêter le traitement
                    fa_showModal(response.message, "Erreur", { yes: false, no: false, cancel: true }, { yes: "Continuer", no: "Annuler", cancel: "Je recommence..." });
                }
            } else {
                pa_retour_erreur_ajax(uploadRequest.status);
            }
        }
    };
    uploadRequest.send(formData);
}
