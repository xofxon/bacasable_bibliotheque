<?php
// Démarrer la session
session_start();
$domaine = $_SERVER['HTTP_HOST'];
if (!isset($_SESSION['cestmoi'])) { 
    echo "<script type='text/javascript'>
    if (window.top !== window.self) {
        // Si la page est chargée dans une frame, redirige la page parente
        window.top.location.href = 'index.php';
    } else {
        // Sinon, redirige la page actuelle
        window.location.href = 'index.php';
    }
    </script>";
    exit(); // Arrête l'exécution du script après la redirection
  }
// Définir la durée d'inactivité en secondes (10 minutes = 600 secondes)
$host = "localhost";  // ou l'adresse IP de votre serveur MariaDB
$dbname = "connais pas"; // Nom de la base de données  
$password = "motde passe"; // Mot de passe
$username = "utilisateurs"; // Nom d'utilisateur
$inactivity_time = 600;
// Vérifier si la variable de session existe
if (isset($_SESSION['last_activity'])) {
  // Calculer le temps écoulé depuis la dernière activité
  $time_diff = time() - $_SESSION['last_activity'];

  // Si le temps écoulé est supérieur à la durée d'inactivité, détruire la session
  if ($time_diff > $inactivity_time) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit(); // Arrête l'exécution du script après la redirection
  }
}
// Mettre à jour la variable de session avec l'heure actuelle
$_SESSION['last_activity'] = time();
$quiSuisJe=$_SESSION['cestmoi'];  
include_once('bibliotheque_fonctions_et_procedures_application.php');
try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
    //die("Erreur de connexion : ";
}
$autorisationSuppression = isset($_SESSION['AutorisationSuppressionBibliotheque']) && $_SESSION['AutorisationSuppressionBibliotheque'];
$AutorisationModificationBibliotheque= isset($_SESSION['AutorisationModificationBibliotheque']) && $_SESSION['AutorisationModificationBibliotheque'];
$AutorisationAjoutBibliotheque= isset($_SESSION['AutorisationAjoutBibliotheque']) && $_SESSION['AutorisationAjoutBibliotheque'];
?>
