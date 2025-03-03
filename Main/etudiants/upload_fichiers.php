<?php
session_start();
include('../dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $etudiant_id = $_SESSION['user_id'];  // L'ID de l'étudiant connecté
    $fileType = $_POST['fileType'];            // Type de fichier (TD, Cours, Exercice)
    $classes = $_POST['classes'];             // Classes sélectionnées
    $file = $_FILES['file'];                  // Fichier à télécharger

    // Validation du fichier
    if ($file['error'] === 0) {
        $filePath = 'fichiers_etudiants/' . basename($file['name']);
        
        // Déplacer le fichier dans le dossier de destination
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Insertion du fichier dans la table `fichiers_etudiants`
            $stmt = $pdo->prepare("INSERT INTO fichiers_etudiants (etudiant_id, type_fichier, nom_fichier, chemin_fichier) VALUES (?, ?, ?, ?)");
            $stmt->execute([$etudiant_id, $fileType, $file['name'], $filePath]);

            // Récupérer l'ID du fichier inséré
            $fichier_id = $pdo->lastInsertId();

            // Lier le fichier aux classes sélectionnées
            foreach ($classes as $classe_id) {
                $stmt = $pdo->prepare("INSERT INTO fichier_classe_etudiant (fichier_id, classe_id) VALUES (?, ?)");
                $stmt->execute([$fichier_id, $classe_id]);
            }

            // Affichage du message de succès
            echo "Fichier téléchargé avec succès.";
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
    } else {
        echo "Erreur de téléchargement.";
    }
}
?>
