<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'origin_esatic';
$user = 'samuel';
$password = 'cedric225'; 
$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

// Récupération des données du formulaire
$matricule = $_POST['matricule'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Vérification des mots de passe
if ($password !== $confirm_password) {
    die("Erreur : les mots de passe ne correspondent pas !");
}

// Hachage du mot de passe
$mot_de_passe = password_hash($password, PASSWORD_BCRYPT);

try {
    $conn->beginTransaction();

    // Insérer le professeur
    $stmt = $conn->prepare("INSERT INTO professeurs (prenom_professeur, nom_professeur, email_professeur, mot_de_passe_professeur, matricule_professeur) VALUES (?, ?, ?, ?, ?) RETURNING id");
    $stmt->execute([$prenom, $nom, $email, $mot_de_passe, $matricule]);
    $professeur_id = $stmt->fetchColumn();
    
    $conn->commit();

    // Redirection vers la page d'accueil après l'inscription réussie
    header('Location: administration.php'); // Remplacez par l'URL de votre choix
    exit; // Assurez-vous que le script PHP s'arrête ici

} catch (Exception $e) {
    $conn->rollBack();
    echo "Erreur : " . $e->getMessage();
}
?>
