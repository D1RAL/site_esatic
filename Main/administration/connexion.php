<?php
session_start(); // Toujours mettre ça en haut

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dsn = "pgsql:host=localhost;dbname=site_esatic";
    $username = "samuel";
    $password_db = "cedric225";

    try {
        $connexion = new PDO($dsn, $username, $password_db);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // === CONNEXION ===
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $sql = "SELECT * FROM administration WHERE admin_email = :email";
            $stmt = $connexion->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['motdepasse_admin'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['nom_admin'] = $admin['nom_admin'];
                $_SESSION['admin_email'] = $admin['admin_email']; //🔥 On stocke l'email
                echo "<script>window.location.href='administration.php';</script>";
                exit();
            } else {
                echo "<script>alert('Email ou mot de passe incorrect');</script>";
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Connexion Administrateur</h1>
            <div class="divider"></div>
            <p class="subtitle">ESATIC - École Supérieure Africaine des TIC</p>
        </div>

        <form id="authForm" method="POST" action="">
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

        <div class="toggle-mode">
            <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous</a></p>
        </div>
    </div>
</body>
</html>
