<?php
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'postgres';
$password = 'admin';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricule = htmlspecialchars($_POST['matricule'] ?? '');
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $classe_id = htmlspecialchars($_POST['classe'] ?? '');

    if (empty($matricule) || empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password) || empty($classe_id)) {
        die("Erreur : Tous les champs sont obligatoires !");
    }

    if ($password !== $confirm_password) {
        die("Erreur : Les mots de passe ne correspondent pas !");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $conn->beginTransaction();

        // Insertion de l'étudiant
        $sql = "INSERT INTO etudiants (prenom_etudiant, nom_etudiant, email_etudiant, mot_de_passe_etudiant, numero_matricule, classe_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$prenom, $nom, $email, $hashed_password, $matricule, $classe_id]);

        $conn->commit();
        echo "<div class='overlay'>
                <div class='loader'></div>
                <p class='message'>Veuillez patienter...</p>
              </div>
              <script>
                  setTimeout(() => {
                      document.querySelector('.message').textContent = 'Étudiant enregistré avec succès ✅';
                  }, 2000);
                  setTimeout(() => {
                      window.location.href='administration.php';
                  }, 4000);
              </script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
}

$classes = $conn->query("SELECT id, nom_classe FROM classes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Étudiant</title>
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
            font-family: Arial, sans-serif;
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
        <h1>Formulaire d'Inscription d'Étudiant</h1>
        <p>ESATIC - École Supérieure Africaine des TIC</p>
    </div>

    <form method="POST" action="">
        <div class="input-group">
            <input type="text" name="nom" class="input-field" placeholder="Nom" required>
        </div>
        <div class="input-group">
            <input type="text" name="prenom" class="input-field" placeholder="Prénom" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" class="input-field" placeholder="Email" required>
        </div>
        <div class="input-group">
            <input type="text" name="matricule" class="input-field" placeholder="Numéro Matricule" required>
        </div>
        <div class="input-group">
            <select name="classe" class="input-field" required>
                <option value="">Sélectionnez une classe</option>
                <?php foreach ($classes as $classe) { ?>
                    <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom_classe']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="input-group">
            <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" class="input-field" placeholder="Confirmer Mot de passe" required>
        </div>
        <button type="submit" class="submit-btn">Ajouter l'étudiant</button>
    </form>
</div>
</body>
</html>
