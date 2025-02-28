<?php
session_start();

// Vérifier si l'utilisateur est connecté et si son rôle est 'professeur'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professeur') {
    die("Erreur : Vous devez être connecté en tant que professeur pour accéder à cette page.");
}

$professeur_id = $_SESSION['user_id'];  // Récupérer l'ID du professeur depuis la session

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

// Récupérer les données du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $classe = $_POST['department'];
  $jour = $_POST['doctor_day']; // Ajoutez un champ pour le jour dans votre formulaire
  $heure_debut = $_POST['doctor_start_time']; // Ajoutez un champ pour l'heure de début
  $heure_fin = $_POST['doctor_end_time']; // Ajoutez un champ pour l'heure de fin

  // Vérifier si les champs sont remplis
  if (!empty($classe) && !empty($jour) && !empty($heure_debut) && !empty($heure_fin)) {
      
      // Récupérer l'ID de la classe
      $query_classe = "SELECT id FROM classes WHERE nom_classe = ?";
      $stmt_classe = $conn->prepare($query_classe);
      $stmt_classe->execute([$classe]);
      $classe_id = $stmt_classe->fetchColumn();

      // Insertion dans la table des rattrapages
      $query_rattrapage = "
          INSERT INTO rattrapages (professeur_id, classe_id, jour, heure_debut, heure_fin)
          VALUES (?, ?, ?, ?, ?)
      ";
      $stmt_rattrapage = $conn->prepare($query_rattrapage);
      $stmt_rattrapage->execute([$professeur_id, $classe_id, $jour, $heure_debut, $heure_fin]);

      // Optionnel : Envoyer des notifications ou des emails aux étudiants de la classe
      // Exemple : récupérer les étudiants de la classe et leur envoyer une notification
      // (Cela nécessiterait l'ajout de la logique pour récupérer les étudiants et envoyer des notifications.)
  }
}

// Récupérer les classes et matières enseignées par le professeur
$query = "
    SELECT DISTINCT cl.nom_classe, m.nom_matiere
    FROM professeur_classe pc
    JOIN classes cl ON pc.classe_id = cl.id
    JOIN professeur_matiere pm ON pc.professeur_id = pm.professeur_id
    JOIN matieres m ON pm.matiere_id = m.id
    JOIN ue u ON m.ue_id = u.id
    JOIN niveaux n ON cl.niveau_id = n.id AND u.niveau_id = n.id
    WHERE pc.professeur_id = ?
";

$stmt = $conn->prepare($query);
$stmt->execute([$professeur_id]);
$classes_matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations du professeur
$query_professeur = "SELECT nom_professeur, prenom_professeur FROM professeurs WHERE id = ?";
$stmt_professeur = $conn->prepare($query_professeur);
$stmt_professeur->execute([$professeur_id]);
$professeur = $stmt_professeur->fetch(PDO::FETCH_ASSOC);

// Récupérer les fichiers uploadés pour ce professeur depuis la table fichier_prof
$query_files = "
    SELECT fp.fichier_pdf, fp.date_telechargement
    FROM fichier_prof fp
    WHERE fp.professeur_id = ?
";
$stmt_files = $conn->prepare($query_files);
$stmt_files->execute([$professeur_id]);
$files = $stmt_files->fetchAll(PDO::FETCH_ASSOC);

// Organisation des données pour un affichage structuré
$data = [];
foreach ($classes_matieres as $row) {
    $classe = $row['nom_classe'];
    $matiere = $row['nom_matiere'];

    if (!isset($data[$classe])) {
        $data[$classe] = [];
    }

    if ($matiere && !in_array($matiere, $data[$classe])) {
        $data[$classe][] = $matiere;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Myesatic</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
        background-color: #f8f9fa;
    }
    .widget {
        padding: 20px;
        border-radius: 10px;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-size: 1.2em;
    }
    .chart-container {
        width: 100%;
        max-width: 600px;
        margin: auto;
    }
    /* Section d'image en haut */
  .top-image-container {
    width: 100%;
    height: 100vh; /* Utilise toute la hauteur de la fenêtre */
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    z-index: 10;
  }

  /* Image */
  .top-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* L'image va remplir l'espace sans déformer */
  }

  /* Contenu sous l'image */
  .content {
    position: relative;
    z-index: 1;
    padding: 20px;
  }

  </style>

  <!-- Styles CSS pour la fenêtre modale -->
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
  
    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 20px;
      width: 50%;
      text-align: center;
      border-radius: 8px;
    }
  
    .close {
      float: right;
      font-size: 28px;
      cursor: pointer;
    }
  </style>

</head>

<body class="index-page">

  <div class="top-image-container">
    <img src="assets/img/esatic.webp" alt="Image description" class="top-image">
  </div>

  <header id="header" class="header d-flex flex-column justify-content-center">

    <i class="header-toggle d-xl-none bi bi-list"></i>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="#hero" class="active"><i class="bi bi-house-door navicon"></i><span>Tableau de bord</span></a></li>
        <li><a href="#about"><i class="bi bi-book navicon"></i><span>Mes classes</span></a></li>
        <li><a href="#appointment"><i class="bi bi-pencil-square navicon"></i><span>Rattrapage</span></a></li>
        <li><a href="#"><i class="bi bi-calendar navicon"></i><span>Deconnexion</span></a></li>
      </ul>
    </nav>

  </header>

  <main class="main" >
    <div class="content" id="hero">
      <div class="container mt-4">
        <header class="mb-4">
            <h1 class="text-center">Bienvenue, Professeur Goli</h1>
        </header>
    
        <!-- Widgets -->
        <div class="row text-center">
            <div class="col-md-4">
                <div class="widget">📖 <br><span>Cours : 5</span></div>
            </div>
            <div class="col-md-4">
                <div class="widget">📅 <br><span>Prochain cours : 10h</span></div>
            </div>
            <div class="col-md-4">
                <div class="widget">📝 <br><span>Copies à corriger : 12</span></div>
            </div>
        </div>
    
        <!-- Graphique -->
        <div class="chart-container my-4">
            <h3 class="text-center">Progression des cours donnés</h3>
            <canvas id="statsChart"></canvas>
        </div>
    
        <!-- Derniers fichiers téléversés -->
        <div class="mt-4">
            <h3>Derniers fichiers téléversés</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom du fichier</th>
                        <th>Classe</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Téléchargement</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>cours_informatique.pdf</td>
                        <td>SRIT2A</td>
                        <td>Cours</td>
                        <td>16/02/2025</td>
                        <td><button class="btn btn-sm btn-primary">📥 Télécharger</button></td>
                    </tr>
                    <tr>
                        <td>TD_math.xlsx</td>
                        <td>SRIT2B</td>
                        <td>TD</td>
                        <td>15/02/2025</td>
                        <td><button class="btn btn-sm btn-primary">📥 Télécharger</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    
        <!-- Cours à venir -->
        <div class="mt-4">
            <h3>Calendrier des cours à venir</h3>
            <ul class="list-group">
                <li class="list-group-item">🕘 Lundi 10h - Algèbre Linéaire</li>
                <li class="list-group-item">🕒 Mercredi 14h - Réseaux Informatiques</li>
                <li class="list-group-item">🕖 Vendredi 8h - Programmation Java</li>
            </ul>
        </div>
    
        <!-- Notifications -->
        <div class="mt-4">
            <h3>Notifications 📢</h3>
            <ul class="list-group">
                <li class="list-group-item">📌 Nouveau devoir ajouté pour "Algèbre Linéaire"</li>
                <li class="list-group-item">📌 Rappel : Réunion pédagogique demain à 9h</li>
                <li class="list-group-item">📌 TD de Réseaux Informatiques à corriger avant vendredi</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Graphique avec Chart.js
        const ctx = document.getElementById('statsChart').getContext('2d');
        const statsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Cours donnés',
                    data: [5, 8, 12, 10, 15, 18],
                    borderColor: 'blue',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    
    </div>

    
    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Mes classes</h2>
        <p>Gérez facilement vos cours en ajoutant, modifiant ou supprimant des cours dans le programme.</p>
      </div>
    
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 justify-content-center">
    
          <div class="col-lg-10 mobile-list"> 
            <?php if (!empty($data)) : ?>
              <?php foreach ($data as $classe => $matieres) : ?>
                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($classe) ?></h5>
                    <p><strong>Code :</strong> INF101</p>
                    <p><strong>Intitulés :</strong> <?= htmlspecialchars(implode(', ', array_unique($matieres))) ?></p>
                    <p><strong>Horaires :</strong> Lundi 8h - 10h</p>
                    <div class="d-flex flex-wrap gap-2">
                      <button class="btn btn-success" onclick="openModal('cours')">Ajouter cours</button>
                      <button class="btn btn-warning" onclick="openModal('td')">Ajouter TD</button>
                      <a href="note.html" class="btn btn-danger">Saisir Notes</a>
                      <button class="btn btn-success">Liste de classe</button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    
    
    
    </section>
    
    <!-- Fenêtre Modale -->
    <div id="uploadModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Téléverser un fichier</h3>
        <p>Sélectionnez les classes concernées :</p>
        <form id="uploadForm">
          <label><input type="checkbox" name="classe" value="L1 Informatique"> SRIT2A</label><br>
          <label><input type="checkbox" name="classe" value="L2 Informatique"> SRIT2B</label><br>
    
          <input type="file" id="fileInput" class="form-control mb-2" accept=".pdf,.xls,.xlsx">
          <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
      </div>
    </div>
    
    

    <section id="appointment" class="appointment section light-background">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>PROGRAMMATION DE RATTRAPAGE</h2>
        <p>La fonctionnalité de prise de rendez-vous en ligne de Medicor vous permet de planifier vos consultations</p>
      </div><!-- End Section Title -->
  
      <!-- Formulaire HTML -->
      <div class="container" data-aos="fade-up" data-aos-delay="100">
          <form method="POST" id="appointment-form" role="form">
              <div class="row">
                  <div class="col-md-4 form-group mt-3">
                      <select name="department" id="department" class="form-select" required>
                          <option value="">Sélectionnez la classe</option>
                          <?php
                          // Afficher dynamiquement les classes enseignées par le professeur
                          foreach ($data as $classe => $matieres) {
                              echo "<option value=\"$classe\">$classe</option>";
                          }
                          ?>
                      </select>
                  </div>
                  <div class="col-md-4 form-group mt-3">
                      <input type="date" name="doctor_day" class="form-control" required>
                  </div>
                  <div class="col-md-4 form-group mt-3">
                      <input type="time" name="doctor_start_time" class="form-control" required>
                  </div>
                  <div class="col-md-4 form-group mt-3">
                      <input type="time" name="doctor_end_time" class="form-control" required>
                  </div>
              </div>
              <div class="mt-3">
                  <div class="text-center">
                      <button type="submit" class="btn btn-success">VALIDER</button>
                  </div>
              </div>
          </form>
      </div>

    </section>

    <h3>Vos fichiers uploadés :</h3>
    <?php if (!empty($files)) : ?>
        <ul>
            <?php foreach ($files as $file) : ?>
                <li>
                    <a href="telecharger_fichier.php?file=<?= urlencode($file['fichier_pdf']) ?>">
                        <?= htmlspecialchars($file['fichier_pdf']) ?>
                    </a> - Date : <?= htmlspecialchars($file['date_telechargement']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Aucun fichier trouvé.</p>
    <?php endif; ?>
  </main>

  <footer id="footer" class="footer position-relative light-background">
    <div class="container">
      
      <div class="social-links d-flex justify-content-center">
        <a href=""><i class="bi bi-twitter-x"></i></a>
        <a href=""><i class="bi bi-facebook"></i></a>
        <a href=""><i class="bi bi-instagram"></i></a>
        <a href=""><i class="bi bi-skype"></i></a>
        <a href=""><i class="bi bi-linkedin"></i></a>
      </div>
      <div class="container">
        <div class="copyright">
          <span>Copyright</span> <strong class="px-1 sitename">Alex Smith</strong> <span>All Rights Reserved</span>
        </div>
        <div class="credits">
          Designed by <a href="https://bootstrapmade.com/">ESATIC</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/typed.js/typed.umd.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // Récupérer les paramètres de l'URL
    const params = new URLSearchParams(window.location.search);
    const moyenne = params.get('moyenne');

    // Afficher la moyenne si elle existe
    if (moyenne) {
      document.getElementById('moyenne').innerText = moyenne;
    }
  </script>

  <!-- Script pour gérer l'upload -->
  <script>
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
      event.preventDefault();
      
      let fileInput = document.getElementById('fileInput');
      if (fileInput.files.length === 0) {
        alert("Veuillez sélectionner un fichier samuel à téléverser.");
        return;
      }

      let formData = new FormData();
      formData.append("emploiDuTemps", fileInput.files[0]);

      fetch('/upload_emploi_du_temps', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => alert("Fichier téléversé avec succès !"))
      .catch(error => console.error("Erreur lors du téléversement :", error));
    });
  </script>

  <!-- JavaScript pour gérer l'affichage de la fenêtre modale -->
  <script>
    function openModal(type) {
      document.getElementById("uploadModal").style.display = "block";
      document.getElementById("modalTitle").innerText = "Téléverser un " + (type === 'cours' ? "cours" : "TD");
    }
  
    function closeModal() {
      document.getElementById("uploadModal").style.display = "none";
    }
  
    document.getElementById("uploadForm").addEventListener("submit", function(event) {
      event.preventDefault();
  
      let fileInput = document.getElementById("fileInput");
      let selectedClasses = Array.from(document.querySelectorAll('input[name="classe"]:checked'))
        .map(cb => cb.value);
  
      if (selectedClasses.length === 0) {
        alert("Veuillez sélectionner au moins une classe !");
        return;
      }
  
      if (fileInput.files.length === 0) {
        alert("Veuillez sélectionner un fichier !");
        return;
      }
  
      alert("Fichier envoyé avec succès pour les classes : " + selectedClasses.join(", "));
      closeModal();
    });
  </script>

</body>

</html>