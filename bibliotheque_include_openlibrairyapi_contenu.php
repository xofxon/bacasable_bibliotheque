<?php
$book = $responseData["ISBN:$isbn"];
// Extraction des informations
$title = $book['title'] ?? 'Titre non disponible';
$sousTitre= $book['subtitle'] ?? '';
$authors = isset($book['authors']) ? implode(", ", array_column($book['authors'], 'name')) : 'Auteur(s) non disponible(s)';
$previsualisation=$book['url'] ?? '';
$book = $responseData["ISBN:$isbn"];
// Extraction des informations
$isbn10 = $isbn13 = $id = '';
if (isset($book['identifiers'])) {
    $isbn10 = $book['identifiers']['isbn_10'][0] ?? $isbn.'???';
    $isbn13 = $book['identifiers']['isbn_13'][0] ?? $isbn;
    $id = $book['identifiers']['openlibrary'][0] ?? '';
}
$liencanonique=$book['url'] ?? 'Lien non disponible';
$publish_date = $book['publish_date'] ?? 'Date de publication non disponible';
$number_of_pages = $book['number_of_pages'] ?? 'Nombre de pages non disponible';
?>