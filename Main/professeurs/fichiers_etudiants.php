<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Vérifier si le professeur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professeur') {
    die("Erreur : Professeur non connecté.");
}

$professeur_id = $_SESSION['user_id'];  // Récupérer l'ID du professeur depuis la session

// Connexion à la base de données
try {
    $conn = new PDO("pgsql:host=localhost;dbname=site_esatic", "samuel", "cedric225");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si un fichier a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $classes = $_POST['departments'] ?? [];
    $fileType = htmlspecialchars($_POST['fileType'] ?? '');
    $file = $_FILES['file'];

    // Vérification des champs
    if (empty($classes) || empty($fileType) || empty($file['name'])) {
        die("Erreur : Tous les champs sont obligatoires !");
    }

    // Vérifier si le fichier a bien été uploadé
    if (!is_uploaded_file($file['tmp_name'])) {
        die("Erreur : Le fichier n'a pas été correctement uploadé.");
    }

    // Vérification du type de fichier (PDF, XLS, XLSX)
    $allowedTypes = ['application/pdf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if (!in_array($file['type'], $allowedTypes)) {
        die("Erreur : Le fichier doit être un PDF, XLS ou XLSX.");
    }

    // Définir un répertoire d'upload
    $uploadDir = 'uploads_etudiants/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Créer un nom de fichier unique
    $filename = uniqid() . '_' . basename($file['name']);
    $filePath = $uploadDir . $filename;

    // Déplacer le fichier vers le dossier "uploads_etudiants"
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        echo "Fichier déplacé avec succès : " . $filePath;

        try {
            // Insérer le fichier dans la table fichiers et récupérer son ID
            $sql = "INSERT INTO fichiers (professeur_id, type_fichier, nom_fichier, chemin_fichier) 
                    VALUES (?, ?, ?, ?) RETURNING id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$professeur_id, $fileType, $filename, $filePath]);

            $fichier_id = $stmt->fetchColumn();
            
            if (!$fichier_id) {
                die("Erreur : Impossible de récupérer l'ID du fichier inséré.");
            }

            // Insérer les associations fichier_classe
            $sql = "INSERT INTO fichier_classe (fichier_id, classe_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);

            foreach ($classes as $classe) {
                // Assurez-vous que chaque classe existe dans la base de données avant l'insertion
                $stmt->execute([$fichier_id, $classe]);
            }

            // Afficher le message de succès
            echo "<div class='overlay'>
                    <div class='loader'></div>
                    <p class='message'>Fichier envoyé avec succès ✅</p>
                  </div>
                  <script>
                      setTimeout(() => {
                          window.location.href='professeurs.php';
                      }, 3000);
                  </script>";

        } catch (PDOException $e) {
            die("Erreur SQL : " . $e->getMessage());
        }
    } else {
        die("Erreur lors du déplacement du fichier.");
    }
} else {
    die("Erreur : La méthode de requête n'est pas POST.");
}
?>
