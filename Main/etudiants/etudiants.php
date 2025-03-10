<?php
require '../dbconnection.php'; // Inclure le fichier de connexion

// ID de l'étudiant à afficher (exemple: 1)
$id_etudiant = 1;

// Requête SQL pour récupérer le nom de l'étudiant
$stmt = $pdo->prepare("SELECT nom_etudiant FROM etudiants WHERE id = ?");
$stmt->execute([$id_etudiant]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

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

  <!-- Vendor CSS Files  iyee -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">

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
    .btn-primary{
      background-color: #0563bb;
      border-radius: 50px;
      border: none;
      transition: transform 0.3s ease;
    }
    .btn-success{
      background-color: #0563bb;
      border-radius: 50px;
      border: none;
      transition: transform 0.3s ease;
      margin-left: 20px;
      

    }
    .btn-primary:hover{
      transform: scale(1.1);
      background-color: #0563bb;


    }
    .btn-success:hover{
      transform: scale(1.1);
      background-color: #0563bb;
      

    }
    #resume {
      margin-top : 50px;
    }
  </style>

</head>

<body class="index-page">

  <div class="top-image-container">
    <img src="../assets/img/esatic.webp" alt="Image description" class="top-image">
  </div>

  <header id="header" class="header d-flex flex-column justify-content-center">

    <i class="header-toggle d-xl-none bi bi-list"></i>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="#hero" class="active"><i class="bi bi-house-door navicon"></i><span>Tableau de bord</span></a></li>
        <li><a href="#about"><i class="bi bi-book navicon"></i><span>Mes documents</span></a></li>
        <li><a href="#resume"><i class="bi bi-book navicon"></i><span>Mes notes</span></a></li>
        <li><a href="#appointment"><i class="bi bi-pencil-square navicon"></i><span>Résultats</span></a></li>
        <li><a href="#"><i class="bi bi-calendar navicon"></i><span>Deconnexion</span></a></li>
      </ul>
    </nav>

  </header>

  <main class="main" >
    <div class="content" id="hero">
      <div class="container mt-4">
        <header class="mb-4">
        <h1 class="text-center">
        Bienvenue, <?php echo $etudiant ? htmlspecialchars($etudiant['nom_etudiant']) : "Professeur Goli"; ?>
    </h1>
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
        <h2>MES DOCUMENTS</h2>
        <p>Récupère ici tous tes documents selon la matière.</p>
      </div>
    
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 justify-content-center">
    
          <!-- Liste des cours -->
          <div class="col-lg-10">
            <h3>Liste des classes</h3>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Classe</th>
                  <th>Code</th>
                  <th>Intitulé</th>
                  <th>Horaires</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>SRIT2A</td>
                  <td>INF101</td>
                  <td>Introduction à l'informatique</td>
                  <td>Lundi 8h - 10h</td>
                  <td>
                    <button class="btn btn-success" onclick="openModal('cours')">Ajouter cours</button>
                    <button class="btn btn-warning" onclick="openModal('td')">Ajouter TD</button>
                    <a href="note.html" class="btn btn-danger">Saisir Notes</a>
                    <button class="btn btn-success">Liste de classe</button>
                  </td>
                </tr>
                <tr>
                  <td>SRIT2B</td>
                  <td>MAT201</td>
                  <td>Mathématiques avancées</td>
                  <td>Mercredi 14h - 16h</td>
                  <td>
                    <button class="btn btn-success" onclick="openModal('cours')">Ajouter cours</button>
                    <button class="btn btn-warning" onclick="openModal('td')">Ajouter TD</button>
                    <a href="note.html" class="btn btn-danger">Saisir Notes</a>
                    <button class="btn btn-success">Liste de classe</button>
                  </td>
                </tr>
                <tr>
                  <td>RTEL2</td>
                  <td>MAT201</td>
                  <td>Mathématiques avancées</td>
                  <td>Mercredi 14h - 16h</td>
                  <td>
                    <button class="btn btn-success" onclick="openModal('cours')">Ajouter cours</button>
                    <button class="btn btn-warning" onclick="openModal('td')">Ajouter TD</button>
                    <a href="note.html" class="btn btn-danger">Saisir Notes</a>
                    <button class="btn btn-success">Liste de classe</button>
                  </td>
                </tr>
                <tr>
                  <td>SIGL2</td>
                  <td>MAT201</td>
                  <td>Mathématiques avancées</td>
                  <td>Mercredi 14h - 16h</td>
                  <td>
                    <button class="btn btn-success" onclick="openModal('cours')">Ajouter cours</button>
                    <button class="btn btn-warning" onclick="openModal('td')">Ajouter TD</button>
                    <a href="note.html" class="btn btn-danger">Saisir Notes</a>
                    <button class="btn btn-success">Liste de classe</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
    
        </div>
      </div>
    
    </section>

    <section id="resume" class="courses section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Gestion des notes</h2>
        <p>Renseignez vos notes et calculer votre moyenne</p>
      </div><!-- End Section Title -->
    
      <div class="container" data-aos="fade-up" data-aos-delay="100">
    
        <div class="row gy-4 justify-content-center">
          
          <div class="col-lg-15 content">
            <h2>Gestion des notes</h2>

            <div class="mb-3">
              <label for="subjectSelect" class="form-label">Sélectionner une matière</label>
              <select id="subjectSelect" class="form-control">
                <option value="Mathématiques">Algo</option>
                <option value="Physique">Physique</option>
                <option value="Chimie">Python</option>
                <option value="Informatique">Geo-diff</option>
              </select>
          </div>

          
            
            <table style="width: 100%" id="notesTable" class="table table-bordered">
              <thead>
                <tr>
                    <th style="width: 10%">Nom</th>
                    <th style="width: 10%">Note </th>
                    
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td rowspan="1000">Dagou</td>
                  <td><input type="number" class="form-control note"></td>
                </tr>
              </tbody>
            </table>
            <button class="btn btn-primary add-row-btn">Ajouter une note</button>
            <button class="btn btn-success calculate-btn">Calculer la Moyenne</button>
            <h3 class="mt-4">Moyenne générale: <span id="average">-</span></h3>
          </div>
          <table id="subjectAverageTable" class="subject-average-table">
            <thead>
              <tr>
                <th>Matière</th>
                <th>Moyenne</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        
          <script>
       
            function resetNotesTable() {
              const tbody = document.querySelector('#notesTable tbody');
              tbody.innerHTML = '';  
          
           
              const firstRow = `
                <tr>
                     <td rowspan="1000">Dagou</td>
                        <td><input type="number" class="form-control note"></td>
                </tr>
              `;
              tbody.insertAdjacentHTML('beforeend', firstRow);
          
        
              document.getElementById('average').textContent = '-';
            }
          
       
            document.querySelector('.add-row-btn').addEventListener('click', function() {
              const tbody = document.querySelector('#notesTable tbody');
              const newRow = `
                <tr>
                  <td><input type="number" class="form-control note"></td>
                </tr>
              `;
              tbody.insertAdjacentHTML('beforeend', newRow);
            });
          
      
            document.querySelector('.calculate-btn').addEventListener('click', function() {
              const notes = document.querySelectorAll('#notesTable .note');
              let total = 0;
              let count = 0;
          
              notes.forEach(note => {
                const value = parseFloat(note.value);
                if (!isNaN(value)) {
                  total += value;
                  count++;
                }
              });
          
              const average = count > 0 ? (total / count).toFixed(2) : '-';
              document.getElementById('average').textContent = average;
          
         
              const subject = document.getElementById('subjectSelect').value;
              const subjectTable = document.getElementById('subjectAverageTable').querySelector('tbody');
              const newRow = `
                <tr>
                  <td>${subject}</td>
                  <td>${average}</td>
                </tr>
              `;
              subjectTable.insertAdjacentHTML('beforeend', newRow);
            });
          
            
            document.getElementById('subjectSelect').addEventListener('change', function() {
              resetNotesTable();  
            });
          </script>
          
        
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
        <h2>RESULTATS SEMESTRIELS</h2>
        <p><strong>Vos résultats du semestre ne sont pas disponibles actuellement...</strong></p>
      </div><!-- End Section Title -->
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