<?php
session_start();

// Connexion à la base de données PostgreSQL
$host = "localhost";
$dbname = "site_esatic";
$user = "samuel";
$password = "cedric225";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = trim($_POST["matricule"]);
    $password = trim($_POST["password"]);

    if (!empty($matricule) && !empty($password)) {
        $tables = [
            "professeurs" => "matricule_professeur",
            "etudiants" => "matricule_etudiant"
        ];
        $user = null;
        $user_table = "";

        foreach ($tables as $table => $matricule_col) {
            $stmt = $pdo->prepare("SELECT id, mot_de_passe FROM $table WHERE $matricule_col = :matricule");
            $stmt->bindParam(":matricule", $matricule, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Message de débogage
            var_dump($user);  // Vérifie ici si l'utilisateur est trouvé dans la base de données

            if ($user) {
                $user_table = $table;
                break;
            }
        }

        if ($user && password_verify($password, $user["mot_de_passe"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["matricule"] = $matricule;
            $_SESSION["user_type"] = $user_table;
            header("Location: note.html"); // Redirige vers la page d'accueil
            exit;
        } else {
            $error = "Matricule ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
</body>
</html>
