<?php
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'postgres';
$password = 'admin';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : null;
    $matricule = htmlspecialchars($_POST['matricule'] ?? '');
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $matieres = $_POST['matiere'] ?? [];
    $classes = $_POST['classe'] ?? [];

    if (empty($matricule) || empty($nom) || empty($prenom) || empty($email) || empty($matieres) || empty($classes)) {
        die("Erreur : Tous les champs obligatoires ne sont pas remplis !");
    }

    if ($password !== $confirm_password) {
        die("Erreur : Les mots de passe ne correspondent pas !");
    }

    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    try {
        $conn->beginTransaction();

        if ($action == 'modifier' && $professeur_id) {
            $sql = "UPDATE professeurs SET prenom_professeur = ?, nom_professeur = ?, email_professeur = ?, matricule_professeur = ?";
            $params = [$prenom, $nom, $email, $matricule];

            if ($hashed_password) {
                $sql .= ", mot_de_passe_professeur = ?";
                $params[] = $hashed_password;
            }

            $sql .= " WHERE id = ?";
            $params[] = $professeur_id;
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // Suppression des anciennes matières et classes associées
            $conn->prepare("DELETE FROM professeur_matiere WHERE professeur_id = ?")->execute([$professeur_id]);
        }

        // Réinsertion des matières et classes
        $sql_matiere = "INSERT INTO professeur_matiere (professeur_id) VALUES (?, ?, ?)";
        $stmt_matiere = $conn->prepare($sql_matiere);

        

        $conn->commit();
        echo "<div class='overlay'>
                <div class='loader'></div>
                <p class='message'>Modification en cours...</p>
              </div>
              <script>
                  setTimeout(() => {
                      document.querySelector('.message').textContent = 'Professeur modifié avec succès ✅';
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

$matieres = $conn->query("SELECT id, nom_matiere FROM matieres")->fetchAll(PDO::FETCH_ASSOC);
$classes = $conn->query("SELECT id, nom_classe FROM classes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification Professeur</title>
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
        <h1>Formulaire de Modification de Professeur</h1>
        <p>ESATIC - École Supérieure Africaine des TIC</p>
    </div>

    <form method="POST" action="">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="professeur_id" value="<?php echo $_GET['id'] ?? ''; ?>">
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
        <button type="submit" class="submit-btn">Modifier le professeur</button>
    </form>
</div>
</body>
</html>
