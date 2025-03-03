<?php
session_start();

// Vérifier si le professeur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professeur') {
    die("Erreur : Vous devez être connecté en tant que professeur pour accéder à cette page.");
}

$professeur_id = $_SESSION['user_id']; // Récupérer l'ID du professeur

// Connexion à la base de données
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'samuel';
$password = 'cedric225';

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si une classe est sélectionnée
if (!isset($_GET['classe_id'])) {
    die("Erreur : Classe non spécifiée.");
}

$classe_id = $_GET['classe_id'];

// Vérifier si le professeur enseigne bien dans cette classe
$query_verif = "
    SELECT 1 FROM professeur_classe
    WHERE professeur_id = ? AND classe_id = ?
";
$stmt_verif = $conn->prepare($query_verif);
$stmt_verif->execute([$professeur_id, $classe_id]);

if ($stmt_verif->rowCount() === 0) {
    die("Erreur : Vous n'êtes pas autorisé à voir cette classe.");
}

// Récupérer les informations de la classe
$query_classe = "SELECT nom_classe FROM classes WHERE id = ?";
$stmt_classe = $conn->prepare($query_classe);
$stmt_classe->execute([$classe_id]);
$classe = $stmt_classe->fetch(PDO::FETCH_ASSOC);

if (!$classe) {
    die("Erreur : Classe introuvable.");
}

// Récupérer la liste des étudiants de cette classe
$query_etudiants = "
    SELECT numero_matricule, prenom_etudiant, nom_etudiant, email_etudiant
    FROM etudiants
    WHERE classe_id = ?
    ORDER BY nom_etudiant ASC, prenom_etudiant ASC
";
$stmt_etudiants = $conn->prepare($query_etudiants);
$stmt_etudiants->execute([$classe_id]);
$etudiants = $stmt_etudiants->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des étudiants</title>
    <link rel="stylesheet" href="style2.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>

<h2>Direction de la pedagogie</h2>
    <table class="table-title">
        <tr>
            <th><img class="logo-esatic" src="download.png" alt="logo-esatic"></th>
            <th>Liste des étudiants - <?= htmlspecialchars($classe['nom_classe']) ?></th>
        </tr>
    </table>
    <?php if (!empty($etudiants)) : ?>
        <table class="table-list">
            <tr>
                <th>N</th>
                <th>Matricule</th>
                <th>Prénom</th>
                <th>Nom</th>
            </tr>
            <?php 
            $num = 1; // Initialisation du compteur
            foreach ($etudiants as $etudiant) : ?>
            <tr>
                    <td><?= $num++ ?></td> <!-- Incrémentation dynamique -->
                    <td><?= htmlspecialchars($etudiant['numero_matricule']) ?></td>
                    <td><?= htmlspecialchars($etudiant['prenom_etudiant']) ?></td>
                    <td><?= htmlspecialchars($etudiant['nom_etudiant']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p>Aucun étudiant inscrit dans cette classe.</p>
    <?php endif; ?>

    <button id="download-pdf">Télécharger la liste</button>
    <script>
        document.getElementById("download-pdf").addEventListener("click", function () {
            var button = document.getElementById("download-pdf"); 
            button.style.display = "none"; // Cacher le bouton avant la capture

            var element = document.body; // Capture toute la page

            html2pdf()
                .from(element)
                .save()
                .then(() => {
                    button.style.display = "block"; // Réafficher le bouton après le téléchargement
                });
        });
    </script>


</body>
</html>
