<?php
session_start(); // Démarre la session

// Détruire toutes les variables de session
$_SESSION = array();

// Supprimer le cookie de session si nécessaire
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion ou d'accueil
header("Location: connexion.php"); // Change "login.php" par la page que tu veux
exit;
?>
