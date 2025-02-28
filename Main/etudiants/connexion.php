<?php
session_start(); // Toujours mettre ça en haut

$message = ""; // Variable pour stocker les messages d'erreur

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dsn = "pgsql:host=localhost;dbname=site_esatic";
    $username = "postgres";
    $password_db = "admin";

    try {
        $connexion = new PDO($dsn, $username, $password_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // === CONNEXION ===
        if (isset($_POST['role']) && isset($_POST['email']) && isset($_POST['password'])) {
            $role = $_POST['role'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($role == 'professeur') {
                $sql = "SELECT * FROM professeurs WHERE email_professeur = :email";
                $password_column = 'mot_de_passe_professeur';
            } elseif ($role == 'etudiant') {
                $sql = "SELECT * FROM etudiants WHERE email_etudiant = :email";
                $password_column = 'mot_de_passe_etudiant';
            } else {
                $message = "Rôle invalide.";
                exit();
            }

            $stmt = $connexion->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user[$password_column])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                header("Location: etudiants.php");
                exit();
            } else {
                $message = "Email ou mot de passe incorrect.";
            }
        }
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Connexion</h1>
            <div class="divider"></div>
            <p class="subtitle">ESATIC - École Supérieure Africaine des TIC</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form id="authForm" method="POST" action="">
            <div class="input-group">
                <label for="role">Choisissez votre rôle :</label>
                <select name="role" class="input-field" required>
                    <option value="">Sélectionnez un rôle</option>
                    <option value="professeur">Professeur</option>
                    <option value="etudiant">Étudiant</option>
                </select>
            </div>
            
            <div class="input-group">
                <input type="email" name="email" class="input-field" placeholder="Email" required autocomplete="off">
            </div>

            <div class="input-group">
                <input type="password" name="password" class="input-field" placeholder="Mot de passe" required autocomplete="off">
            </div>

            <div id="rememberMeGroup" class="checkbox-group">
                <input type="checkbox" id="rememberMe">
                <label for="rememberMe">Se souvenir de moi</label>
            </div>

            <button type="submit" class="submit-btn">Se connecter</button>
        </form>
    </div>
</body>
</html>