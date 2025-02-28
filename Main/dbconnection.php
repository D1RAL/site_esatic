<?php
// Informations de connexion
$host = "localhost";  // Adresse du serveur PostgreSQL
$dbname = "site_esatic";  // Nom de la base de données
$username = "samuel";  // Nom d'utilisateur PostgreSQL
$password = "cedric225";  // Remplace par ton vrai mot de passe
$port = "5432"; // Port par défaut de PostgreSQL

try {
    // Création de la connexion avec PDO
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    
    // Activer les erreurs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Définir l'encodage des caractères
    $pdo->exec("SET NAMES 'utf8'");
    
    // Message en cas de succès
    // echo "Connexion réussie à PostgreSQL";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>