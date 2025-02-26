-- Table des étudiants
CREATE TABLE etudiants (
    id SERIAL PRIMARY KEY,
    nom_etudiant VARCHAR(100),
    prenom_etudiant VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe TEXT NOT NULL,
    matricule_etudiant VARCHAR(50) UNIQUE NOT NULL,
    classe_id INT,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- Table des professeurs
CREATE TABLE professeurs (
    id SERIAL PRIMARY KEY,
    nom_professeur VARCHAR(100),
    prenom_professeur VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    matricule_professeur VARCHAR(50) UNIQUE NOT NULL,
    mot_de_passe TEXT NOT NULL,
    specialite VARCHAR(100)
);

-- Table des classes
CREATE TABLE classes (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    niveau VARCHAR(50) NOT NULL
);

-- Table des matières
CREATE TABLE matieres (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL
);

-- Table des enseignements (Professeur <-> Matière <-> Classe)
CREATE TABLE enseignements (
    id SERIAL PRIMARY KEY,
    professeur_id INT NOT NULL,
    matiere_id INT NOT NULL,
    classe_id INT NOT NULL,
    FOREIGN KEY (professeur_id) REFERENCES professeurs(id) ON DELETE CASCADE,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Table des notes
CREATE TABLE notes (
    id SERIAL PRIMARY KEY,
    etudiant_id INT NOT NULL,
    enseignement_id INT NOT NULL,
    type_notes VARCHAR(20) CHECK (type_notes IN ('Controle Continu', 'Examen', 'Kholle')),
    note FLOAT CHECK (note >= 0 AND note <= 20),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE,
    FOREIGN KEY (enseignement_id) REFERENCES enseignements(id) ON DELETE CASCADE
);

-- Table des documents (cours, TDs)
CREATE TABLE documents (
    id SERIAL PRIMARY KEY,
    enseignement_id INT NOT NULL,
    type_documents VARCHAR(10) CHECK (type_documents IN ('Cours', 'TD')),
    fichier VARCHAR(255),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enseignement_id) REFERENCES enseignements(id) ON DELETE CASCADE
);

-- Table administration
CREATE TABLE administration (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe TEXT NOT NULL,
    dernier_connexion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);