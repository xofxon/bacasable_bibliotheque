<?php
require_once '../bibliotheque_informationsdecons.php';
try {
    $query = $pdo->prepare("SELECT idorigines FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'GB']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesGB = $result['idorigines'];
    } else {
        $idoriginesGB = 0;
    }

} catch (Exception $e) {
    //echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
}
try {
    $query = $pdo->prepare("SELECT idorigines FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'OL']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesOL = $result['idorigines'];
    } else {
        $idoriginesOL=0;
    }

} catch (Exception $e) {
    //echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
}

header("Content-Type: application/javascript");
?>
var perimetre="";
var pourQuoi="";
var idlivre=0;
var genre=1;
var idOrigine=0;
function pl_searchISBN() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'bibliotheque_recherche_isbn_google_api_recherche.php?isbn=' + document.getElementById("isbn").value, true);
    pa_afficherSablier();
    //  On recherche d'abord avec l'API google
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                if (xhr.responseText) {
                    try {
                        const reponseGoogleBooks = JSON.parse(xhr.responseText);
                        const jsonPart = reponseGoogleBooks.json;
                        const livreDetails = JSON.parse(xhr.responseText);
                        document.getElementById('jsonOutput').value = JSON.stringify(jsonPart, null, 2);
                        if (livreDetails.succes==true) {
                            idOrigine=<?= $idoriginesGB ?>;
                            pl_afficherLivreDetails(livreDetails);
                        } else {
                            xhr.open('GET', 'bibliotheque_recherche_isbn_openlibrairy_recherche.php?isbn=' + document.getElementById("isbn").value, true);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState == 4) { // La requête est terminée
                                    pa_effacerSablier(); // Cacher le sablier
                                    if (xhr.status == 200) {
                                        if (xhr.responseText) {
                                            try {
                                                const reponseOpenLibrairy = JSON.parse(xhr.responseText);
                                                const jsonPart = reponseOpenLibrairy.json;
                                                const livreDetails = JSON.parse(xhr.responseText);
                                                document.getElementById('jsonOutput').value = JSON.stringify(jsonPart, null, 2);
                                                if (livreDetails.succes==true) {
                                                    idOrigine=<?= $idoriginesOL ?>;
                                                    pl_afficherLivreDetails(livreDetails);
                                                } else {
                                                    idlivre=0;
                                                    document.getElementById('voirExistant').style.display = 'none';
                                                    fa_showModal(livreDetails.message,"Attention",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Recommencer"},-1)
                                                    pl_resetForm();
                                                };
                                            }   catch (e) {
                                            };    
                                        };    
                                    } else {
                                        pa_retour_erreur_ajax(xhr.status);
                                    };
                                };
                            }    
                            xhr.send();

                        };
                    }   catch (e) {
                    };    
                };    
            } else {
                fa_showModal(livreDetails.message,"Attention",{yes:false,no:false,cancel:true},{yes: "Continuer", no: "Annuler", cancel: "Recommencer"})
                pl_resetForm();
            };
        };
    };    
    xhr.send();
}
async function pl_afficherLivreDetails(livre) {
    document.getElementById('titre').value = livre.data.titre+' '+livre.data.soustitre;
    document.getElementById('isbn10').value = livre.data.isbn10;
    document.getElementById('isbn13').value = livre.data.isbn13;
    document.getElementById('liencanonique').value = livre.data.liencanonique;
    document.getElementById('auteurs').value=livre.data.auteurs;
    document.getElementById('description').value = livre.data.description;
    document.getElementById('olid').value = livre.data.id;
    if (document.getElementById('isbn13').value == "" ){
        document.getElementById('isbn13').value=document.getElementById("isbn").value;
    }
    if (livre.existe==true) {
        idlivre=livre.idlivre;
        document.getElementById('voirExistant').style.display = 'inline-block';
        console.log(livre.idlivre)
        valeurRetour=await fa_showModal("Ce livre est déjà référencé. Voulez-vous le modifier?","Question",{yes:true,no:true,cancel:false},{yes: "Oui", no: "Non", cancel: "Abandonner"});
        switch (valeurRetour) {
            case 1:
                perimetre="Modification";
                pl_sauveLivreDetails();
                break;
            case 0:
                break;
            default:
        }
    } else {
        idlivre=0;
        document.getElementById('voirExistant').style.display = 'none';
        valeurRetour = await fa_showModal("Ce livre n'est pas encore référencé. Voulez-vous le créer?","Question",{yes:true,no:true,cancel:false},{yes: "Oui", no: "Non", cancel: "Abandonner"},-1);
        switch (valeurRetour) {
            case 1:
                perimetre="Creation";
                pl_sauveLivreDetails();
                break;
            case 0:
                break;
            default:
        }
    };
}

function pl_sauveLivreDetails() {
    const xhr = new XMLHttpRequest();
    var form = document.getElementById("livreFormulaire");
    const data = new FormData(form);
    // Ajoute des données supplémentaires qui ne sont pas dans le formulaire
    data.append('perimetre', perimetre);
    data.append('idorigines', idOrigine);
    data.append('etat', 1);
    data.append('id', idlivre);
    var chaineEncodee=fa_utf8_to_b64(document.getElementById("jsonOutput").value);
    data.append('jsonOutput',chaineEncodee);
    data.append('Pourquoi',pourQuoi);
    xhr.open('POST', 'bibliotheque_le_livre_crud.php', true);
    pa_afficherSablier();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) { // La requête est terminée
            pa_effacerSablier(); // Cacher le sablier
            if (xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.succes) {
                    fa_showModal(response.message, title = "Information", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Ok"});
                    pl_resetForm(); // Réinitialise le formulaire en cas de succès
                };
                if (!response.succes) {
                    fa_showModal(response.message, title = "Avertissement", showButtons = {yes: false, no: false, cancel: true},{yes: "Continuer", no: "Annuler", cancel: "Retour"});
                };
            } else {
                pa_retour_erreur_ajax(xhr.status);
            };
        };
    }    
    xhr.send(data);
};
function pl_ouvreLivreExistant(){
    if (idlivre!==0){
        const selectGenre = document.getElementById('genre');
        const selectedValue = selectGenre.value;
        const url = `bibliotheque_le_livre.php?id=${idlivre}&${genre}=selectedValue&perimetre=Modification`;
        console.log(url)
        const tabTitle = "Modification livre";
        parent.pl_createDynamicTab(tabTitle, url);
    }    

}
function pl_resetForm() {
        const selectGenre = document.getElementById('genre');
        const selectedValue = selectGenre.value;
        document.getElementById('livreFormulaire').reset();
        selectGenre.value = selectedValue;
        document.getElementById('isbn').value='';
        document.getElementById('isbn').focus();
}

function pl_init() {
    //  Code nécessaire si utilisation d'une douchette, programmée pour scanner le code barre et faire un retour chariot
    const input = document.getElementById('isbn');
    input.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Empêche le comportement par défaut
            pl_searchISBN(); // Appelle la fonction de recherche
        }
    });

    const sgetISBN_ws01 = document.getElementById('getISBN_ws01');
    if (sgetISBN_ws01) {
        sgetISBN_ws01.addEventListener('click', function () {
            pourQuoi="Bibliotheque";
            pl_searchISBN();
        });
    };
    const sgetISBN_ws02 = document.getElementById('getISBN_ws02');
    if (sgetISBN_ws02) {
        document.getElementById('getISBN_ws02').addEventListener('click', function () {
            pourQuoi="LesCourses";
            pl_searchISBN();
        });
    };
    const sresetBtn = document.getElementById('resetBtn');
    if (sresetBtn) {
        document.getElementById('resetBtn').addEventListener('click', function () {
            pl_resetForm();
    });
    };
    const senregistrerBtn = document.getElementById('enregistrerBtn');
    if (senregistrerBtn) {
        document.getElementById('enregistrerBtn').addEventListener('click', function () {
            let sisbn13 = document.getElementById("isbn13").value;
            if (sisbn13) { // Cette condition vérifie si champ est "vrai" (null, undefined, "", 0, false)
                if (idlivre!=0) {
                    perimetre="Modification";
                } else {
                    perimetre="Creation";
                }
                pl_sauveLivreDetails();
            }
        });
    }
}    
document.addEventListener('DOMContentLoaded', function() {
    pl_init();
});
