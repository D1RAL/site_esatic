<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Informations de connexion
$host = "localhost";  
$dbname = "site_esatic";  
$username = "samuel";  
$password = "cedric225";  
$port = "5432"; 

try {
    // Création de la connexion avec PDO
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    
    // Activer les erreurs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Définir l'encodage des caractères
    $pdo->exec("SET NAMES 'utf8'");
    
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
