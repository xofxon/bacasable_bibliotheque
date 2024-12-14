<?php
        $url = "https://openlibrary.org/api/books?bibkeys=ISBN:" . urlencode($isbn) . "&format=json&jscmd=data";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false); // Capturer toutes les erreurs
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Récupérer le code HTTP
        curl_close($ch);
?>