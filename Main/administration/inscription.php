<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST["nom"]));
    $prenom = htmlspecialchars(trim($_POST["prenom"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($password)) {
        try {
            $conn = new PDO("pgsql:host=localhost;dbname=site_esatic", "samuel", "cedric225");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO administration (nom_admin, prenom_admin, admin_email, motdepasse_admin) VALUES (:nom, :prenom, :email, :password)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();

            header("Location: connexion2.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription ESATIC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title" id="formTitle">Inscription Professeur</h1>
            <div class="divider"></div>
            <p class="subtitle">ESATIC - École Supérieure Africaine des TIC</p>
        </div>

        <form id="authForm" method="post" action="inscription.php">
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
                <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
            </div>

            <button type="submit" class="submit-btn">
                <span>S'inscrire</span>
            </button>
        </form>

        <div class="toggle-mode">
            <p>Déjà inscrit ? <a href="connexion.php">Connectez-vous</a></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
