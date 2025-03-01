<?php
session_start();
include('../dbconnection.php');

// Vérification de la session de l'étudiant
if (!isset($_SESSION['email_etudiant'])) {
    echo "<script>alert('Veuillez vous connecter d\'abord'); window.location.href='connexion.php';</script>";
    exit();
}

$email = $_SESSION['email_etudiant'];

try {
    // Récupération de l'ID de l'étudiant
    $sql = "SELECT id FROM etudiants WHERE email_etudiant = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        echo "<script>alert('Étudiant introuvable'); window.location.href='etudiants.php';</script>";
        exit();
    }

    $etudiant_id = $etudiant['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $date_calendrier = $_POST['date'];
        $heure_debut = $_POST['heure_debut'];
        $heure_fin = $_POST['heure_fin'];

        // Insertion dans la base de données
        $sql_insert = "
            INSERT INTO calendrier_etudiants (etudiant_id, titre, description, date_calendrier, heure_debut, heure_fin) 
            VALUES (:etudiant_id, :titre, :description, :date_calendrier, :heure_debut, :heure_fin)
        ";

        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindParam(':etudiant_id', $etudiant_id);
        $stmt_insert->bindParam(':titre', $titre);
        $stmt_insert->bindParam(':description', $description);
        $stmt_insert->bindParam(':date_calendrier', $date_calendrier);
        $stmt_insert->bindParam(':heure_debut', $heure_debut);
        $stmt_insert->bindParam(':heure_fin', $heure_fin);
        $stmt_insert->execute();

        echo "<script>alert('Événement ajouté avec succès !'); window.location.href='etudiants.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>
