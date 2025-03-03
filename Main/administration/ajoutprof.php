<?php
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'samuel';
$password = 'cedric225';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricule = isset($_POST['matricule']) ? htmlspecialchars($_POST['matricule']) : null;
    $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : null;
    $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
    $matieres_ids = isset($_POST['matieres']) ? $_POST['matieres'] : [];
    $classes_ids = isset($_POST['classes']) ? $_POST['classes'] : [];

    if (empty($matricule) || empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password) || empty($matieres_ids) || empty($classes_ids)) {
        die("Erreur : Tous les champs sont obligatoires !");
    }

    if ($password !== $confirm_password) {
        die("Erreur : Les mots de passe ne correspondent pas !");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $conn->beginTransaction();

        // Insertion du professeur
        $sql = "INSERT INTO professeurs (prenom_professeur, nom_professeur, email_professeur, mot_de_passe_professeur, matricule_professeur) 
                VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$prenom, $nom, $email, $hashed_password, $matricule]);
        $professeur_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        // Insertion dans la table professeur_matiere pour chaque matière sélectionnée
        $sql_matiere = "INSERT INTO professeur_matiere (professeur_id, matiere_id) VALUES (?, ?)";
        $stmt_matiere = $conn->prepare($sql_matiere);
        foreach ($matieres_ids as $matiere_id) {
            $stmt_matiere->execute([$professeur_id, $matiere_id]);
        }

        // Insertion dans la table professeur_classe pour chaque classe sélectionnée
        $sql_classe = "INSERT INTO professeur_classe (professeur_id, classe_id) VALUES (?, ?)";
        $stmt_classe = $conn->prepare($sql_classe);
        foreach ($classes_ids as $classe_id) {
            $stmt_classe->execute([$professeur_id, $classe_id]);
        }

        $conn->commit();

        echo "<div class='overlay'>
                <div class='loader'></div>
                <p class='message'>Veuillez patienter...</p>
              </div>
              <script>
                  setTimeout(() => {
                      document.querySelector('.message').textContent = 'Professeur enregistré avec succès ✅';
                  }, 2000);
                  setTimeout(() => {
                      window.location.href='administration.php';
                  }, 4000);
              </script>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
}

$matieres = $conn->query("SELECT id, nom_matiere FROM matieres")->fetchAll(PDO::FETCH_ASSOC);
$classes = $conn->query("SELECT id, nom_classe FROM classes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Professeur</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            z-index: 9999;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 6px solid #007bff;
            border-top: 6px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .message {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .custom-select {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .select-header {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .select-header:after {
            content: '▼';
            font-size: 12px;
        }
        
        .select-options {
            display: none;
            position: absolute;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
            background-color: white;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
        }
        
        .select-option {
            padding: 8px 15px;
            display: flex;
            align-items: center;
        }
        
        .select-option:hover {
            background-color: #f5f5f5;
        }
        
        .select-option input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .select-option label {
            flex: 1;
            cursor: pointer;
        }
        
        .selected-count {
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Formulaire d'enregistrement de Professeur</h1>
        <p>ESATIC - École Supérieure Africaine des TIC</p>
    </div>

    <form method="POST" action="">
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
            <input type="text" name="matricule" class="input-field" placeholder="Numéro Matricule" required>
        </div>
        
        <div class="custom-select" id="matieres-select">
            <div class="select-header">Sélectionnez les matières <span class="selected-count" style="display: none;">0</span></div>
            <div class="select-options">
                <?php foreach ($matieres as $matiere) { ?>
                    <div class="select-option">
                        <input type="checkbox" name="matieres[]" id="matiere-<?php echo $matiere['id']; ?>" value="<?php echo $matiere['id']; ?>">
                        <label for="matiere-<?php echo $matiere['id']; ?>"><?php echo $matiere['nom_matiere']; ?></label>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="custom-select" id="classes-select">
            <div class="select-header">Sélectionnez les classes <span class="selected-count" style="display: none;">0</span></div>
            <div class="select-options">
                <?php foreach ($classes as $classe) { ?>
                    <div class="select-option">
                        <input type="checkbox" name="classes[]" id="classe-<?php echo $classe['id']; ?>" value="<?php echo $classe['id']; ?>">
                        <label for="classe-<?php echo $classe['id']; ?>"><?php echo $classe['nom_classe']; ?></label>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="input-group">
            <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" class="input-field" placeholder="Confirmer Mot de passe" required>
        </div>
        <button type="submit" class="submit-btn">Ajouter le professeur</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration des sélecteurs personnalisés
        const customSelects = document.querySelectorAll('.custom-select');
        
        customSelects.forEach(select => {
            const header = select.querySelector('.select-header');
            const options = select.querySelector('.select-options');
            const checkboxes = select.querySelectorAll('input[type="checkbox"]');
            const countBadge = select.querySelector('.selected-count');
            
            // Ouvrir/fermer le menu déroulant
            header.addEventListener('click', () => {
                options.style.display = options.style.display === 'block' ? 'none' : 'block';
            });
            
            // Mettre à jour le compteur lorsqu'une case est cochée/décochée
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const checkedCount = select.querySelectorAll('input[type="checkbox"]:checked').length;
                    if (checkedCount > 0) {
                        countBadge.textContent = checkedCount;
                        countBadge.style.display = 'inline-flex';
                    } else {
                        countBadge.style.display = 'none';
                    }
                });
            });
            
            // Fermer le menu déroulant quand on clique ailleurs
            document.addEventListener('click', (e) => {
                if (!select.contains(e.target)) {
                    options.style.display = 'none';
                }
            });
        });
    });
</script>
</body>
</html>