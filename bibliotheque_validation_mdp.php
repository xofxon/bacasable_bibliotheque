<?php
session_start();
// Paramètres de connexion à la base de données
$host = "localhost";  // ou l'adresse IP de votre serveur MariaDB
$dbname = "jxcjrxkx_bibliotheque"; // Nom de la base de données
$username = "jxcjrxkx_bisounours"; // Nom d'utilisateur
$password = "6B5K{@giDp!p57"; // Mot de passe
try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
    //die("Erreur de connexion : ";
}

/*
// Vérification du CAPTCHA
$recaptchaResponse = $_POST['g-recaptcha-response'];
$secret = 'votre_clé_secrète_recaptcha'; // Remplacez par votre clé secrète

$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = array(
    'secret' => $secret,
    'response' => $recaptchaResponse
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$result = json_decode($result);

if (!$result->success) {
    // Le CAPTCHA est invalide
    echo "Erreur : CAPTCHA invalide.";
    exit;
}
*/
// Validation des informations d'identification
$utilisateur = $_POST['utilisateur'];
$motdepasse = $_POST['motdepasse'];

// Hacher le mot de passe avec SHA-256
$motdepasse_hache = hash('sha256', $motdepasse);

try {
    // Requête préparée pour éviter les injections SQL
    $requete = $pdo->prepare('SELECT prenom, patronyme FROM _utilisateur WHERE codealphanum15 = :utilisateur AND motdepasse = :motdepasse');
    $requete->execute(array('utilisateur' => $utilisateur, 'motdepasse' => $motdepasse_hache));
    $resultat = $requete->fetch();

    if ($resultat) {
        // Authentification réussie
        session_start();
        // Concaténer le prénom et le patronyme avec un espace entre les deux
        $_SESSION['cestmoi'] = $resultat['prenom'];
        // Rediriger l'utilisateur vers la page d'accueil ou une autre page
        header('Location: bibliotheque_menu.php');
        exit;
    } else {
        // Échec de l'authentification
        echo "Erreur : nom d'utilisateur ou mot de passe incorrect.";
    }
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    die('Erreur lors de la validation des informations d\'identification : ' . $e->getMessage());
}

?>