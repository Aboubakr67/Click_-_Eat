USE click_and_eat;

-- Insertion des utilisateurs (administrateurs)
INSERT INTO USERS (nom, prenom, role, mot_de_passe) VALUES
('Dupont', 'Jean', 'ZONE CUISINE', 'password123'),
('Martin', 'Claire', 'ZONE STOCK', 'password123'),
('Lemoine', 'Pierre', 'ZONE MANAGEMENT', 'password123');

-- Insertion des ingrédients
INSERT INTO INGREDIENTS (nom) VALUES
('Tomate'),
('Laitue'),
('Oignon'),
('Fromage'),
('Steak'),
('Poulet'),
('Pain'),
('Frites'),
('Salade'),
('Sauce Ketchup');

-- Insertion des plats
INSERT INTO PLATS (nom, image, prix, type, ingredients) VALUES
('Burger Classique', 'burger.jpg', 5.99, 'PLAT', '1,2,3,4,7'),
('Salade César', 'salade.jpg', 6.50, 'PLAT', '2,10'),
('Poulet Grillé', 'poulet.jpg', 7.99, 'PLAT', '6,7'),
('Frites', 'frites.jpg', 2.00, 'BOISSON', '8'),
('Boisson Cola', 'cola.jpg', 1.50, 'BOISSON', '9');

-- Insertion des formules
INSERT INTO FORMULES (nom, prix) VALUES
('Formule Burger Classique', 8.50),
('Formule Salade César', 9.50);

-- Insertion des relations entre formules et plats
INSERT INTO FORMULE_PLAT (formule_id, plat_id) VALUES
(1, 1),  -- Formule Burger Classique inclut le Burger Classique
(1, 4),  -- Formule Burger Classique inclut les Frites
(2, 2),  -- Formule Salade César inclut la Salade César
(2, 4);  -- Formule Salade César inclut les Frites

-- Insertion des commandes
INSERT INTO COMMANDES (created_at, statut, total, paiement_method, code_commande) VALUES
('2024-11-25 12:00:00', 'EN COURS', 10.50, 'CB', 'CB01'),
('2024-11-25 12:30:00', 'PRETE', 7.99, 'ESPECE', 'ESP25');

-- Insertion des personnalisations de plats
INSERT INTO PLAT_PERSONNALISE (commande_id, plat_id, ingredient_id, action, prix_supplément) VALUES
(1, 1, 4, 'AJOUT', 1.50),  -- Ajouter du fromage au Burger Classique
(1, 1, 3, 'SUPPRESSION', 0),  -- Supprimer l'oignon du Burger Classique
(2, 2, 10, 'SUPPRESSION', 0);  -- Supprimer la sauce Ketchup de la Salade César

-- Insertion des stocks d'ingrédients
INSERT INTO STOCKS (ingredient_id, quantite, updated_at) VALUES
(1, 50, '2024-11-25 10:00:00'),  -- Tomates en stock
(2, 30, '2024-11-25 10:00:00'),  -- Laitue en stock
(3, 20, '2024-11-25 10:00:00'),  -- Oignon en stock
(4, 25, '2024-11-25 10:00:00'),  -- Fromage en stock
(5, 40, '2024-11-25 10:00:00'),  -- Steak en stock
(6, 35, '2024-11-25 10:00:00'),  -- Poulet en stock
(7, 60, '2024-11-25 10:00:00'),  -- Pain en stock
(8, 80, '2024-11-25 10:00:00'),  -- Frites en stock
(9, 100, '2024-11-25 10:00:00'), -- Salade en stock
(10, 75, '2024-11-25 10:00:00'); -- Sauce Ketchup en stock

-- Insertion des imports de stocks
INSERT INTO IMPORTS_STOCKS (created_at, fichier_csv) VALUES
('2024-11-25 09:00:00', 'import_fichier.csv');
