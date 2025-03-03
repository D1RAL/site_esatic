<?php
// Démarrer la session pour récupérer les paramètres de la session
session_start();

// Connexion à la base de données
$pdo = new PDO('pgsql:host=localhost;dbname=site_esatic', 'postgres', 'admin');

// Vérifier si les paramètres sont dans l'URL ou dans la session
$classe_id = isset($_GET['classe_id']) ? $_GET['classe_id'] : (isset($_SESSION['classe_id']) ? $_SESSION['classe_id'] : null);
$matiere_id = isset($_GET['matiere_id']) ? $_GET['matiere_id'] : (isset($_SESSION['matiere_id']) ? $_SESSION['matiere_id'] : null);

// Vérifier si les paramètres sont valides
if ($classe_id && $matiere_id) {
    // Récupérer les étudiants de cette classe
    $stmt = $pdo->prepare('SELECT * FROM etudiants WHERE classe_id = :classe_id');
    $stmt->execute(['classe_id' => $classe_id]);
    $etudiants = $stmt->fetchAll();

    // Récupérer la matière spécifique
    $stmt_matiere = $pdo->prepare('SELECT * FROM matieres WHERE id = :matiere_id');
    $stmt_matiere->execute(['matiere_id' => $matiere_id]);
    $matiere = $stmt_matiere->fetch();
} else {
    echo "Classe ou matière non trouvée.";
    exit;
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
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    table {
        width: 50%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    th, td {
        border: 1px solid black;
        padding: 10px;
        text-align: center;
    }
    input {
        width: 100%;
        border: none;
        text-align: center;
    }
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

  <header id="header" class="header d-flex flex-column justify-content-center">

    <i class="header-toggle d-xl-none bi bi-list"></i>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="#hero" class="active"><i class="bi bi-house-door navicon"></i><span>Tableau de bord</span></a></li>
        <li><a href="#about"><i class="bi bi-book navicon"></i><span>Mes classes</span></a></li>
        <li><a href="#resume"><i class="bi bi-pencil-square navicon"></i><span>Gestion des notes</span></a></li>
        <li><a href="#portfolio"><i class="bi bi-calendar navicon"></i><span>Emploi du temps</span></a></li>
        
        
      </ul>
    </nav>

  </header>

  <main class="main" >
    
    <div class="content" id="hero">
      <div class="container mt-4">
        <header class="mb-4">
            <h1 class="text-center">Bienvenue, Professeur Goli</h1>
        </header>
      </div>
    </div>
    
    <!-- Resume Section -->
    <section id="resume" class="courses section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Gestion des notes</h2>
        <p>Cette section permet aux enseignants de gérer les notes des étudiants par classe.</p>
      </div><!-- End Section Title -->
    
      <div class="container" data-aos="fade-up" data-aos-delay="100">
    
        <div class="row gy-4 justify-content-center">
          
          <div class="col-lg-15 content">
            <h2>Gestion des notes par classe</h2>
            <p class="fst-italic py-3">
              Les enseignants peuvent ajouter des notes pour chaque étudiant d'une classe et calculer la moyenne générale.
            </p>
            
            <form action="enregistrer_notes.php" method="POST">
              <input type="hidden" name="classe_id" value="<?= $classe_id ?>">
              <input type="hidden" name="matiere_id" value="<?= $matiere_id ?>">

              <!-- Sélecteur de type de note -->
              <label for="type_evaluation">Type d'évaluation :</label>
              <select name="type_evaluation" id="type_evaluation">
                  <option value="CC">Contrôle Continu (CC)</option>
                  <option value="Examen">Examen</option>
              </select>

              <table style="width: 100%">
                  <thead>
                      <tr>
                          <th style="width: 10%">Nom</th>
                          <th style="width: 10%">Note</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($etudiants as $etudiant): ?>
                          <tr>
                              <td><?= htmlspecialchars($etudiant['prenom_etudiant'] . ' ' . $etudiant['nom_etudiant']) ?></td>
                              <td>
                                  <input type="number" name="note_<?= $etudiant['id'] ?>" min="0" max="20" step="0.1" />
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>

              <button type="submit" class="btn btn-primary">Enregistrer les notes</button>
            </form>

          </div>
        </div>
    
      </div>
    
    </section>



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
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/typed.js/typed.umd.js"></script>
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>