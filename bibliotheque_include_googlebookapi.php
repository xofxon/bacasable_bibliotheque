<?php
try {
    $query = $pdo->prepare("SELECT idorigines,Api FROM origines WHERE codeinterne = :codeinterne");
    $query->execute(['codeinterne' => 'GB']);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $idoriginesGoogle = $result['idorigines'];
        $Api = $result['Api'];
    } else {
        throw new Exception('Origine google introuvable.');
    }
 } catch (Exception $e) {
        // Gestion des erreurs
        $response['succes'] = false;
        $response['message'] = $e->getMessage();
        echo json_encode($response);
        exit;
}
if (empty($Api)) {
    $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn";
} else {
    $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn&key=$Api";
};
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Récupérer le code HTTP
$googleData = json_decode($result, true);
curl_close($ch);
?>