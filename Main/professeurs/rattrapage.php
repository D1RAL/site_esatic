**<?php
session_start();
require '../dbconnection.php'; // Fichier de connexion à la base de données

// Vérifier si l'utilisateur est connecté et si son rôle est 'professeur'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professeur') {
    die("Erreur : Vous devez être connecté en tant que professeur pour accéder à cette page.");
}

$professeur_id = $_SESSION['user_id']; 

// Récupérer les données du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classe_id = $_POST['classe_id']; // Utiliser directement l'ID de la classe
    $jour = $_POST['jour']; 
    $heure_debut = $_POST['heure_debut']; 
    $heure_fin = $_POST['heure_fin']; 

    // Vérifier si les champs sont remplis
    if (!empty($classe_id) && !empty($jour) && !empty($heure_debut) && !empty($heure_fin)) {

        // Insertion dans la table des rattrapages
        $query_rattrapage = "
            INSERT INTO rattrapages (professeur_id, classe_id, jour, heure_debut, heure_fin)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt_rattrapage = $pdo->prepare($query_rattrapage); // Utilisation de $pdo
        $stmt_rattrapage->execute([$professeur_id, $classe_id, $jour, $heure_debut, $heure_fin]);

        echo "Rattrapage programmé avec succès.";
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>
