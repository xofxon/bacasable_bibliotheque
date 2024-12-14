const isbnButton = document.getElementById('lesOrigines');
if (isbnButton) {
    isbnButton.addEventListener('click', function () {
        const url = 'bibliotheque_les_origines.php';
        const tabTitle = 'Les origines';
        parent.pl_createDynamicTab(tabTitle, url);
    });
}
const fichiersImportesButton = document.getElementById('lesFichiersImportes');
if (fichiersImportesButton) {
    fichiersImportesButton.addEventListener('click', function () {
        const url = 'bibliotheque_fichiersimportes.php';
        const tabTitle = 'Fichiers Importés';
        parent.pl_createDynamicTab(tabTitle, url);
    });
}