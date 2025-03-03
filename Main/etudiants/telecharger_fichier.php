<?php
// Vérifie si l'ID du fichier est passé dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $fichier_id = $_GET['id'];

    var_dump($fichier_id);

    var_dump($_GET);

    // Connexion à la base de données
    try {
        $pdo = new PDO('pgsql:host=localhost;dbname=site_esatic', 'postgres', 'admin');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Récupération des informations du fichier
        $sql = "SELECT nom_fichier, chemin_fichier FROM fichiers WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $fichier_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Vérifie si le fichier existe
        $fichier = $stmt->fetch(PDO::FETCH_ASSOC);

        var_dump($fichier);
        
        if ($fichier) {
            $chemin_fichier = $fichier['chemin_fichier'];
            $nom_fichier = $fichier['nom_fichier'];
            
            // Vérifie si le fichier existe sur le serveur
            if (file_exists($chemin_fichier)) {
                // Définir les en-têtes HTTP pour le téléchargement
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($nom_fichier) . '"');
                header('Content-Length: ' . filesize($chemin_fichier));
                readfile($chemin_fichier);
                exit;
            } else {
                echo 'Le fichier n\'existe pas.';
            }
        } else {
            echo 'Fichier non trouvé.';
        }
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    echo 'ID de fichier invalide.';
}
?>
