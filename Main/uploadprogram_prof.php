<?php 
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'samuel';
$password = 'cedric225';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer la liste des professeurs
$professeurs = $conn->query("SELECT id, nom_professeur, prenom_professeur FROM professeurs")->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un fichier a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $professeur_id = htmlspecialchars($_POST['professeur'] ?? '');
    $file = $_FILES['fichier_pdf'];

    // Vérification des champs
    if (empty($professeur_id) || empty($file['name'])) {
        die("Erreur : Tous les champs sont obligatoires !");
    }

    // Vérifier le type de fichier
    if ($file['type'] !== 'application/pdf') {
        die("Erreur : Seuls les fichiers PDF sont acceptés !");
    }

    // Créer un nom de fichier unique
    $filename = uniqid() . '_' . basename($file['name']);
    $path = 'uploads_prof/' . $filename;

    // Déplacer le fichier vers le dossier "uploads_prof"
    if (move_uploaded_file($file['tmp_name'], $path)) {
        try {
            // Insérer le fichier dans la base de données associé au professeur
            $sql = "INSERT INTO fichier_prof (fichier_pdf, professeur_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$path, $professeur_id]);

            echo "<div class='overlay'>
                    <div class='loader'></div>
                    <p class='message'>Fichier envoyé avec succès ✅</p>
                  </div>
                  <script>
                      setTimeout(() => {
                          window.location.href='administration.php';
                      }, 3000);
                  </script>";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Erreur lors du téléchargement du fichier";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Fichier professeur</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            z-index: 9999;
        }
        .loader {
            width: 50px;
            height: 50px;
            border: 6px solid #007bff;
            border-top: 6px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        .message {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: bold;
        }
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Upload Fichier Professeur</h1>
        <p>ESATIC - École Supérieure Africaine des TIC</p>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="input-group">
            <label>Professeur :</label>
            <select name="professeur" required>
                <option value="">Sélectionnez un professeur</option>
                <?php foreach ($professeurs as $professeur) { ?>
                    <option value="<?php echo $professeur['id']; ?>">
                        <?php echo $professeur['nom_professeur'] . ' ' . $professeur['prenom_professeur']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="input-group">
            <label>Fichier PDF :</label>
            <input type="file" name="fichier_pdf" accept=".pdf" required>
        </div>
        <button type="submit" class="submit-btn">Uploader</button>
    </form>
</div>
</body>
</html>
