<?php
session_start();

// Connexion à la base de données
$pdo = new PDO('pgsql:host=localhost;dbname=site_esatic', 'postgres', 'admin');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Pour activer les exceptions PDO

if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion ou afficher une erreur
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}

// Vérification de la présence des variables dans la requête POST
if (isset($_POST['classe_id'], $_POST['matiere_id'], $_POST['type_evaluation'])) {
    
    $classe_id = $_POST['classe_id'];
    $matiere_id = $_POST['matiere_id'];
    $type_evaluation = $_POST['type_evaluation']; // CC ou Examen

    try {
        // Vérifier si l'évaluation existe déjà
        $stmt = $pdo->prepare("
            SELECT id FROM evaluations WHERE matiere_id = :matiere_id AND classe_id = :classe_id AND type_evaluation = :type_evaluation
        ");
        $stmt->execute([
            'matiere_id' => $matiere_id,
            'classe_id' => $classe_id,
            'type_evaluation' => $type_evaluation
        ]);
        $evaluation = $stmt->fetch();

        if (!$evaluation) {
            // Si l'évaluation n'existe pas, l'ajouter à la table evaluations
            $stmt_evaluation = $pdo->prepare("
                INSERT INTO evaluations (matiere_id, professeur_id, classe_id, type_evaluation, date_evaluation)
                VALUES (:matiere_id, :professeur_id, :classe_id, :type_evaluation, NOW())
                RETURNING id
            ");
            $stmt_evaluation->execute([
                'matiere_id' => $matiere_id,
                'professeur_id' => $_SESSION['user_id'], // Professeur connecté
                'classe_id' => $classe_id,
                'type_evaluation' => $type_evaluation
            ]);
            $evaluation_id = $stmt_evaluation->fetchColumn(); // Récupérer l'ID de l'évaluation
        } else {
            $evaluation_id = $evaluation['id'];
        }

        // Boucle pour enregistrer les notes
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'note_') === 0) {
                preg_match('/note_(\d+)/', $key, $matches);
                $etudiant_id = $matches[1];

                // Validation des notes (par exemple, s'assurer que c'est bien un nombre entre 0 et 20)
                if (is_numeric($value) && $value >= 0 && $value <= 20) {
                    $stmt_note = $pdo->prepare("
                        INSERT INTO notes (etudiant_id, evaluation_id, note)
                        VALUES (:etudiant_id, :evaluation_id, :note)
                        ON CONFLICT (etudiant_id, evaluation_id)
                        DO UPDATE SET note = :note
                    ");

                    $stmt_note->execute([
                        'etudiant_id' => $etudiant_id,
                        'evaluation_id' => $evaluation_id,
                        'note' => $value
                    ]);
                } else {
                    echo "Erreur : La note pour l'étudiant $etudiant_id est invalide.";
                    exit;
                }
            }
        }

        // Stocker les paramètres dans la session
        $_SESSION['classe_id'] = $classe_id;
        $_SESSION['matiere_id'] = $matiere_id;

        // Redirection après l'enregistrement des notes avec les paramètres dans l'URL
        header("Location: note.php");
        exit;

    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
} else {
    var_dump($_POST);
    exit;
}
?>
