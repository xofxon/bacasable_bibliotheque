function pl_init() {
    const sgetISBN = document.getElementById('getISBN');
    if (sgetISBN) {
        sgetISBN.addEventListener('click', function () {
            const url = 'bibliotheque_recherche_isbn.php';
            const tabTitle = 'Recherche dans API(s)';
            parent.pl_createDynamicTab(tabTitle, url);
        });
    }
    const isbnButton = document.getElementById('importationCSV');
    if (isbnButton) {
        isbnButton.addEventListener('click', function () {
            const url = 'bibliotheque_telechargement_csv.php';
            const tabTitle = 'Téléchargement ISBN13';
            parent.pl_createDynamicTab(tabTitle, url);
        });
    }
    const iskm2 = document.getElementById('km2');
    if (iskm2) {
        iskm2.addEventListener('click', function () {
        const url = 'bibliotheque_les_livres.php?genre=110146&perimetre=KM&condition=-2';
        const tabTitle = 'BD ISBN13 Nok ISBN10 Ok';
        parent.pl_createDynamicTab(tabTitle, url);
        });
    }
    const iskm3 = document.getElementById('km3');
    if (iskm3) {
        iskm3.addEventListener('click', function () {
        const url = 'bibliotheque_les_livres.php?genre=110146&perimetre=KM&condition=-3';
        const tabTitle = 'BD ISBN13 Ok ISBN10 nok';
        parent.pl_createDynamicTab(tabTitle, url);
        });
    }

};
document.addEventListener('DOMContentLoaded', function() {
    pl_init();
});
