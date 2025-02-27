<?php
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'postgres';
$password = 'admin';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricule = isset($_POST['matricule']) ? htmlspecialchars($_POST['matricule']) : null;
    $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : null;
    $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
    $matiere_id = isset($_POST['matiere']) ? htmlspecialchars($_POST['matiere']) : null;
    $classe_id = isset($_POST['classe']) ? htmlspecialchars($_POST['classe']) : null;

    if (empty($matricule) || empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password) || empty($matiere_id) || empty($classe_id)) {
        die("Erreur : Tous les champs sont obligatoires !");
    }

    if ($password !== $confirm_password) {
        die("Erreur : Les mots de passe ne correspondent pas !");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $conn->beginTransaction();

        // Insertion du professeur
        $sql = "INSERT INTO professeurs (prenom_professeur, nom_professeur, email_professeur, mot_de_passe_professeur, matricule_professeur) 
                VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$prenom, $nom, $email, $hashed_password, $matricule]);
        $professeur_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        // Insertion dans la table professeur_matiere
        $sql_matiere = "INSERT INTO professeur_matiere (professeur_id, matiere_id) VALUES (?, ?)";
        $stmt_matiere = $conn->prepare($sql_matiere);
        $stmt_matiere->execute([$professeur_id, $matiere_id]);

        // Insertion dans la table professeur_classe
        $sql_classe = "INSERT INTO professeur_classe (professeur_id, classe_id) VALUES (?, ?)";
        $stmt_classe = $conn->prepare($sql_classe);
        $stmt_classe->execute([$professeur_id, $classe_id]);

        $conn->commit();

        echo "<div class='overlay'>
                <div class='loader'></div>
                <p class='message'>Veuillez patienter...</p>
              </div>
              <script>
                  setTimeout(() => {
                      document.querySelector('.message').textContent = 'Professeur enregistré avec succès ✅';
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
    <title>Inscription Professeur</title>
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
        <h1>Formulaire d'enregistrement de Professeur</h1>
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
            <select name="matiere" class="input-field" required>
                <option value="">Sélectionnez une matière</option>
                <?php foreach ($matieres as $matiere) { ?>
                    <option value="<?php echo $matiere['id']; ?>"><?php echo $matiere['nom_matiere']; ?></option>
                <?php } ?>
            </select>
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
        <button type="submit" class="submit-btn">Ajouter le professeur</button>
    </form>
</div>
</body>
</html>