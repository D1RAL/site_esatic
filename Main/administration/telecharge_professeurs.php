<?php
session_start();
include('../dbconnection.php'); // Connexion à la base de données

// Vérification de la session
if (!isset($_SESSION['admin_email'])) {
    header('Location: ../connexion.php');
    exit();
}

// Récupération des professeurs
$sql = "SELECT prenom_professeur, nom_professeur, email_professeur, matricule_professeur FROM professeurs";
$stmt = $pdo->query($sql);
$professeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inclusion de Dompdf
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Instanciation de la classe Dompdf
$dompdf = new Dompdf();

// Création du contenu HTML avec styles
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Liste des Professeurs</h1>
    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>';

foreach ($professeurs as $professeur) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($professeur['matricule_professeur']) . '</td>';
    $html .= '<td>' . htmlspecialchars($professeur['nom_professeur']) . '</td>';
    $html .= '<td>' . htmlspecialchars($professeur['prenom_professeur']) . '</td>';
    $html .= '<td>' . htmlspecialchars($professeur['email_professeur']) . '</td>';
    $html .= '</tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

// Chargement du contenu HTML dans Dompdf
$dompdf->loadHtml($html);

// (Optionnel) Configurer le format et l'orientation du papier
$dompdf->setPaper('A4', 'landscape');

// Rendu du PDF
$dompdf->render();

// Envoi du PDF au navigateur
$dompdf->stream("liste_professeurs.pdf", ["Attachment" => true]);
?>