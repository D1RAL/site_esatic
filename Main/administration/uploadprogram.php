<?php
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'postgres';
$password = 'admin';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$classes = $conn->query("SELECT id, nom_classe FROM classes")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $classe_id = htmlspecialchars($_POST['classe'] ?? '');
    $file = $_FILES['emploi_pdf'];

    if (empty($classe_id) || empty($file)) {
        die("Erreur : Tous les champs sont obligatoires !");
    }

    if ($file['type'] !== 'application/pdf') {
        die("Erreur : Seuls les fichiers PDF sont acceptés !");
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $path = 'uploads/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        try {
            $sql = "INSERT INTO emploi_du_temps (fichier_pdf, classe_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$path, $classe_id]);

            echo "<div class='overlay'>
                    <div class='loader'></div>
                    <p class='message'>Emploi du temps envoyé avec succès ✅</p>
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
    <title>Upload Emploi du Temps</title>
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
        <h1>Upload Emploi du Temps</h1>
        <p>ESATIC - École Supérieure Africaine des TIC</p>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="input-group">
            <label>Classe :</label>
            <select name="classe" required>
                <option value="">Sélectionnez la classe</option>
                <?php foreach ($classes as $classe) { ?>
                    <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom_classe']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="input-group">
            <label>Fichier PDF :</label>
            <input type="file" name="emploi_pdf" accept=".pdf" required>
        </div>
        <button type="submit" class="submit-btn">Uploader</button>
    </form>
</div>
</body>
</html>