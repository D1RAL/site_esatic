<?php
// Connexion à la base
$host = 'localhost';
$dbname = 'site_esatic';
$user = 'postgres';
$password = 'admin';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupération des professeurs
$professeurs = $conn->query("SELECT id, prenom_professeur, nom_professeur, email_professeur, matricule_professeur FROM professeurs")->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifier'])) {
    $professeur_id = $_POST['professeur_id'];
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $matricule = htmlspecialchars($_POST['matricule']);

    // Mise à jour des informations du professeur
    $sql = "UPDATE professeurs SET nom_professeur = ?, prenom_professeur = ?, email_professeur = ?, matricule_professeur = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $matricule, $professeur_id]);

    echo "<p>Informations mises à jour avec succès !</p>";
}

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['supprimer'])) {
    $professeur_id = $_POST['professeur_id'];

    // Suppression du professeur
    $sql = "DELETE FROM professeurs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$professeur_id]);

    echo "<p>Professeur supprimé avec succès !</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Professeurs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            width: 90%; /* Ajustez la largeur selon vos préférences */
            max-width: 1200px; /* Largeur maximale pour éviter l'étirement excessif */
            margin: 0 auto; /* Centrer le conteneur */
            background-color: #fff; /* Couleur de fond blanche */
            padding: 0; /* Retirer le padding pour que le tableau prenne tout l'espace */
            border-radius: 8px; /* Coins arrondis */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Ombre pour donner un effet de profondeur */
        }

        table {
            width: 100%; /* Utiliser toute la largeur du conteneur */
            border-collapse: collapse;
            margin: 0; /* Retirer la marge pour que le tableau touche les bords du conteneur */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px; /* Augmenter l'espacement pour plus de confort visuel */
        }

        th {
            background-color: #f2f2f2;
        }

        /* Style pour les boutons */
        button {
            background-color: #007bff; /* Couleur du fond */
            color: white; /* Couleur du texte */
            border: none; /* Pas de bordure */
            padding: 10px 15px; /* Espacement intérieur */
            border-radius: 5px; /* Coins arrondis */
            cursor: pointer; /* Curseur pointer */
            transition: background-color 0.3s; /* Effet de transition */
        }

        button:hover {
            background-color: #0056b3; /* Couleur de fond au survol */
        }

        button:disabled {
            background-color: #ccc; /* Couleur pour les boutons désactivés */
            cursor: not-allowed; /* Curseur pour désactivé */
        }

        /* Style pour le bouton de retour */
        .back-button {
            margin-bottom: 20px; /* Espacement en dessous */
            background-color: #28a745; /* Couleur de fond verte */
            color: white; /* Couleur du texte */
        }

        /* Style pour la barre de recherche */
        .search-bar {
            margin-bottom: 20px; /* Espacement en dessous */
        }

        /* Style pour les modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Transition pour les lignes du tableau */
        .fade {
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Liste des Professeurs</h1>
    <button onclick="window.location.href='administration.php'" class="back-button">Retour</button>

    <!-- Barre de recherche -->
    <form class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher par nom, prénom ou matricule" />
    </form>

    <table id="professorTable">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($professeurs)): ?>
                <tr>
                    <td colspan="5">Aucun professeur trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($professeurs as $professeur): ?>
                    <tr class="fade">
                        <td><?php echo $professeur['matricule_professeur']; ?></td>
                        <td><?php echo $professeur['nom_professeur']; ?></td>
                        <td><?php echo $professeur['prenom_professeur']; ?></td>
                        <td><?php echo $professeur['email_professeur']; ?></td>
                        <td>
                            <button onclick="document.getElementById('modal-<?php echo $professeur['id']; ?>').style.display='block'">Modifier</button>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="professeur_id" value="<?php echo $professeur['id']; ?>">
                                <button type="submit" name="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce professeur ?');">Supprimer</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal de modification -->
                    <div id="modal-<?php echo $professeur['id']; ?>" class="modal">
                        <div class="modal-content">
                            <span onclick="document.getElementById('modal-<?php echo $professeur['id']; ?>').style.display='none'" class="close">&times;</span>
                            <form method="POST" action="">
                                <input type="hidden" name="professeur_id" value="<?php echo $professeur['id']; ?>">
                                <div>
                                    <label>Nom:</label>
                                    <input type="text" name="nom" value="<?php echo $professeur['nom_professeur']; ?>" required>
                                </div>
                                <div>
                                    <label>Prénom:</label>
                                    <input type="text" name="prenom" value="<?php echo $professeur['prenom_professeur']; ?>" required>
                                </div>
                                <div>
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?php echo $professeur['email_professeur']; ?>" required>
                                </div>
                                <div>
                                    <label>Matricule:</label>
                                    <input type="text" name="matricule" value="<?php echo $professeur['matricule_professeur']; ?>" required>
                                </div>
                                <button type="submit" name="modifier">Modifier</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Recherche en temps réel
    const searchInput = document.getElementById('searchInput');
    const professorTable = document.getElementById('professorTable');
    const rows = professorTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }

            // Ajout de la classe fade-out pour les lignes qui ne correspondent pas
            if (found) {
                rows[i].classList.remove('fade-out');
                rows[i].classList.add('fade');
                rows[i].style.display = ''; // Afficher la ligne
            } else {
                rows[i].classList.remove('fade');
                rows[i].classList.add('fade-out');
                setTimeout(() => {
                    rows[i].style.display = 'none'; // Masquer la ligne après la transition
                }, 900); // Correspond à la durée de la transition CSS
            }
        }
    });
</script>
</body>
</html>