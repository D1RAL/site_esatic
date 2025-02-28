<?php
session_start();
include('../dbconnection.php'); // Connexion à la base de données

// Vérification de la session
if (!isset($_SESSION['admin_email'])) {
    header('Location: ../connexion.php');
    exit();
}

// Récupération des étudiants
$sql = "SELECT prenom_etudiant, nom_etudiant, email_etudiant, numero_matricule FROM etudiants";
$stmt = $pdo->query($sql);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <h1>Liste des Étudiants</h1>
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

foreach ($etudiants as $etudiant) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($etudiant['numero_matricule']) . '</td>';
    $html .= '<td>' . htmlspecialchars($etudiant['nom_etudiant']) . '</td>';
    $html .= '<td>' . htmlspecialchars($etudiant['prenom_etudiant']) . '</td>';
    $html .= '<td>' . htmlspecialchars($etudiant['email_etudiant']) . '</td>';
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
$dompdf->stream("liste_etudiants.pdf", ["Attachment" => true]);
?>