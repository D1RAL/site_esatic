<?php
session_start(); // Démarre la session

// Détruire toutes les variables de session
$_SESSION = array(); // Réinitialiser la session

// Si vous souhaitez détruire complètement la session, utilisez cette ligne :
session_destroy(); // Détruire la session

// Redirection vers la page de connexion
header('Location: connexion.php');
exit();
?>