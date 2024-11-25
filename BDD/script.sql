DROP DATABASE IF EXISTS click_and_eat;
CREATE DATABASE click_and_eat;
USE click_and_eat;


CREATE TABLE USERS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    role ENUM('ZONE CUISINE', 'ZONE STOCK', 'ZONE MANAGEMENT') NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE PLATS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    type ENUM('ENTREE', 'PLAT', 'DESSERT', 'BOISSON') NOT NULL,
    ingredients TEXT NOT NULL
);

CREATE TABLE INGREDIENTS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE FORMULES (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL
);

CREATE TABLE COMMANDES (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME NOT NULL,
    statut ENUM('EN COURS', 'PRETE', 'PAYEE',"DISTRIBUE") NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    paiement_method ENUM('CB', 'ESPECE') NOT NULL,
    code_commande VARCHAR(10) UNIQUE NOT NULL
);


CREATE TABLE PLAT_INGREDIENT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plat_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantite INT NOT NULL,  -- Quantité de l'ingrédient pour le plat
    FOREIGN KEY (plat_id) REFERENCES PLATS(id),
    FOREIGN KEY (ingredient_id) REFERENCES INGREDIENTS(id)
);



CREATE TABLE FORMULE_PLAT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formule_id INT NOT NULL,
    plat_id INT NOT NULL,
    FOREIGN KEY (formule_id) REFERENCES FORMULES(id),
    FOREIGN KEY (plat_id) REFERENCES PLATS(id)
);

CREATE TABLE STOCKS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    quantite INT NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (ingredient_id) REFERENCES INGREDIENTS(id)
);

CREATE TABLE IMPORTS_STOCKS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME NOT NULL,
    fichier_csv VARCHAR(255) NOT NULL
);