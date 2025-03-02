<?php
session_start(); // Assurez-vous que la session est démarrée pour pouvoir utiliser $_SESSION

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérification des classes sélectionnées
    if (isset($_POST['classes']) && count($_POST['classes']) > 0) {
        $classes = $_POST['classes'];
        echo "Classes sélectionnées : " . implode(", ", $classes) . "<br>";
    } else {
        die("Erreur : Aucune classe sélectionnée. Veuillez en sélectionner au moins une.");
    }

    // Récupération du type de fichier
    $fileType = $_POST['fileType'] ?? null;
    if (!$fileType) {
        die("Veuillez sélectionner un type de fichier.");
    }
    echo "Type de fichier sélectionné : $fileType<br>";

    // Vérification du fichier téléchargé
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];

        // Définir un répertoire de téléchargement
        $uploadDir = '../uploads_etudiants/';
        $fileName = basename($file['name']);
        $filePath = $uploadDir . $fileName;

        echo "Nom du fichier : $fileName<br>";
        echo "Chemin du fichier pour téléchargement : $filePath<br>";

        // Vérifiez si le fichier peut être déplacé vers le répertoire de téléchargement
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            echo "Fichier téléchargé avec succès !<br>";

            try {
                // Connexion à la base de données
                $pdo = new PDO('pgsql:host=localhost;dbname=site_esatic', 'samuel', 'cedric225');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Assurez-vous que $professeur_id contient l'ID du professeur authentifié
                $professeur_id = $_SESSION['user_id'];

                // Insérer les informations du fichier dans la table "fichiers"
                $sql = "INSERT INTO fichiers (professeur_id, type_fichier, nom_fichier, chemin_fichier) 
                        VALUES (:professeur_id, :type_fichier, :nom_fichier, :chemin_fichier)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':professeur_id' => $professeur_id,
                    ':type_fichier' => $fileType,
                    ':nom_fichier' => $fileName,
                    ':chemin_fichier' => $filePath
                ]);

                if ($stmt->rowCount() > 0) {
                    $fichier_id = $pdo->lastInsertId();
                    echo "Fichier inséré avec ID : $fichier_id<br>";
                } else {
                    echo "Erreur lors de l'insertion du fichier dans la base de données.<br>";
                }

                // Insérer les relations fichier-classe
                foreach ($classes as $classe_id) {
                    $sqlRelation = "INSERT INTO fichier_classe (fichier_id, classe_id) 
                                    VALUES (:fichier_id, :classe_id)";
                    $stmt = $pdo->prepare($sqlRelation);
                    $stmt->execute([
                        ':fichier_id' => $fichier_id,
                        ':classe_id' => $classe_id
                    ]);
                }

                // Message de succès et redirection vers la page des fichiers
                $_SESSION['success_message'] = "Fichier téléchargé et classes associées avec succès.";
                exit(); // Toujours appeler exit après header() pour s'assurer que le script s'arrête ici

            } catch (PDOException $e) {
                die("Erreur lors de l'insertion des données dans la base de données : " . $e->getMessage());
            }
        } else {
            die("Erreur lors du téléchargement du fichier.");
        }
    } else {
        die("Aucun fichier téléchargé ou erreur dans le téléchargement.");
    }
}
?>
