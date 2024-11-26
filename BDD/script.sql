DROP DATABASE IF EXISTS click_and_eat;
CREATE DATABASE click_and_eat;
USE click_and_eat;

-- Création des tables (modèle déjà validé)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    role ENUM('ZONE CUISINE', 'ZONE STOCK', 'ZONE MANAGEMENT') NOT NULL,
    email VARCHAR(255) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE plats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    type ENUM('ENTREE', 'PLAT', 'DESSERT', 'BOISSON') NOT NULL
);

CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    quantite INT DEFAULT 0
);

CREATE TABLE formules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL
);

CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME NOT NULL,
    statut ENUM('EN COURS', 'PRETE', 'PAYEE', 'DISTRIBUE') NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    paiement_method ENUM('CB', 'ESPECE') NOT NULL,
    code_commande VARCHAR(10) UNIQUE NOT NULL
);

CREATE TABLE contenu_commande (
    commande_id INT NOT NULL,
    plat_id INT NOT NULL,
    quantite INT DEFAULT 1,
    modifications TEXT DEFAULT NULL,
    prix_supplément DECIMAL(10, 2) DEFAULT 0,
    PRIMARY KEY(commande_id, plat_id),
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (plat_id) REFERENCES plats(id)
);

CREATE TABLE plat_ingredient (
    plat_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantite INT NOT NULL,
    PRIMARY KEY(ingredient_id, plat_id),
    FOREIGN KEY (plat_id) REFERENCES plats(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

CREATE TABLE formule_plat (
    formule_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY(formule_id, plat_id),
    FOREIGN KEY (formule_id) REFERENCES formules(id),
    FOREIGN KEY (plat_id) REFERENCES plats(id)
);

CREATE TABLE imports_stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME NOT NULL,
    fichier_csv VARCHAR(255) NOT NULL
);
