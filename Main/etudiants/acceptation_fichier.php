<?php
session_start();
include('../dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fichier_id = $_POST['fichier_id'];  // ID du fichier
    $etudiant_id = $_SESSION['user_id'];  // L'ID de l'étudiant connecté

    // Vérifier si l'étudiant a déjà accepté ce fichier
    $stmt = $pdo->prepare("SELECT * FROM autorisation_fichier WHERE fichier_id = ? AND etudiant_id = ?");
    $stmt->execute([$fichier_id, $etudiant_id]);
    
    if ($stmt->rowCount() > 0) {
        // Mettre à jour l'autorisation
        $stmt = $pdo->prepare("UPDATE autorisation_fichier SET est_accepte = TRUE WHERE fichier_id = ? AND etudiant_id = ?");
        $stmt->execute([$fichier_id, $etudiant_id]);
    } else {
        // Insérer une nouvelle autorisation
        $stmt = $pdo->prepare("INSERT INTO autorisation_fichier (fichier_id, etudiant_id, est_accepte) VALUES (?, ?, TRUE)");
        $stmt->execute([$fichier_id, $etudiant_id]);
    }

    echo "Vous avez accepté ce fichier.";
}
?>
