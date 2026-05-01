CREATE DATABASE IF NOT EXISTS tp_batiment;
USE tp_batiment;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Table historique
CREATE TABLE IF NOT EXISTS historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_batiment VARCHAR(100),
    surface_batiment FLOAT,
    deperdition FLOAT,
    consommation FLOAT,
    classe VARCHAR(2),
    date_calcul TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insérer les utilisateurs avec les mots de passe EN CLAIR
INSERT INTO users (username, password, email, full_name) VALUES 
('admin', 'admin123', 'admin@tp.local', 'Administrateur');

INSERT INTO users (username, password, email, full_name) VALUES 
('user', 'user123', 'user@tp.local', 'Utilisateur');