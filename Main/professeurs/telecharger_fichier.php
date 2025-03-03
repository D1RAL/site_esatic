<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le bon rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professeur') {
    die("Erreur : Vous devez être connecté en tant que professeur pour accéder à ce fichier.");
}

// Vérifier si le paramètre 'file' existe dans l'URL
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    echo "Nom du fichier reçu : " . $file . "<br>"; // Affiche le nom du fichier pour le débogage

    // Définir le chemin complet pour accéder au fichier
    $file_path = '../administration/' . $file;  // Chemin relatif vers le dossier de fichiers
    echo "Chemin complet du fichier : " . realpath($file_path) . "<br>"; // Affiche le chemin absolu du fichier

    // Vérifier si le fichier existe
    if (file_exists($file_path)) {
        // Forcer le téléchargement
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        flush(); // Vider le tampon de sortie
        readfile($file_path);
        exit;
    } else {
        echo "Erreur : Le fichier n'existe pas.";
    }
} else {
    echo "Erreur : Aucun fichier sélectionné.";
}
?>