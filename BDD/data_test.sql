USE click_and_eat;

-- Insertion des utilisateurs (administrateurs)
INSERT INTO users (nom, prenom, role, email, mot_de_passe) VALUES
('Dupont', 'Jean', 'ZONE CUISINE', 'dj@gmail.com',  '$2y$10$YT7rGiEXq.7xTxZ0Uk/dy.KbLhWIxkQd1AbivyUJGZKA3zR5Da1Le'),
('Martin', 'Claire', 'ZONE STOCK','mc@gmail.com', '$2y$10$YT7rGiEXq.7xTxZ0Uk/dy.KbLhWIxkQd1AbivyUJGZKA3zR5Da1Le'),
('Lemoine', 'Pierre', 'ZONE MANAGEMENT', 'lp@gmail.com', '$2y$10$YT7rGiEXq.7xTxZ0Uk/dy.KbLhWIxkQd1AbivyUJGZKA3zR5Da1Le');


-- Insérer des utilisateurs
INSERT INTO users (nom, prenom, role, email, mot_de_passe) VALUES
('Durand', 'Jean', 'ZONE CUISINE', 'jean.durand@example.com', 'password1'),
('Lemoine', 'Sophie', 'ZONE STOCK', 'sophie.lemoine@example.com', 'password2'),
('Martin', 'Alice', 'ZONE MANAGEMENT', 'alice.martin@example.com', 'password3');

-- Insérer des ingrédients
INSERT INTO ingredients (nom, quantite) VALUES
('Tomate', 100),
('Champignon', 80),
('Poulet', 50),
('Fromage', 60),
('Salade', 40),
('Oignon', 30);

-- Insérer des plats
INSERT INTO plats (nom, image, prix, type) VALUES
('Salade César', 'salade_cesar.jpg', 8.50, 'ENTREE'),
('Pizza Margherita', 'pizza_margherita.jpg', 12.00, 'PLAT'),
('Poulet Rôti', 'poulet_roti.jpg', 15.00, 'PLAT'),
('Tarte aux Pommes', 'tarte_pommes.jpg', 5.50, 'DESSERT'),
('Coca-Cola', 'coca_cola.jpg', 3.00, 'BOISSON');

-- Associer des ingrédients aux plats
INSERT INTO plat_ingredient (plat_id, ingredient_id, quantite) VALUES
(1, 1, 10), -- Salade César : Tomate
(1, 5, 20), -- Salade César : Salade
(2, 1, 5), -- Pizza Margherita : Tomate
(2, 4, 15), -- Pizza Margherita : Fromage
(3, 3, 25), -- Poulet Rôti : Poulet
(4, 1, 5), -- Tarte aux Pommes : Tomate
(4, 6, 10); -- Tarte aux Pommes : Oignon

-- Insérer des formules
INSERT INTO formules (nom, prix) VALUES
('Menu Entrée + Plat', 18.00),
('Menu Plat + Dessert', 20.00);

-- Associer des plats aux formules
INSERT INTO formule_plat (formule_id, plat_id) VALUES
(1, 1), -- Menu Entrée + Plat : Salade César
(1, 2), -- Menu Entrée + Plat : Pizza Margherita
(2, 2), -- Menu Plat + Dessert : Pizza Margherita
(2, 4); -- Menu Plat + Dessert : Tarte aux Pommes

-- Insérer des commandes
INSERT INTO commandes (created_at, statut, total, paiement_method, code_commande) VALUES
('2024-11-26 12:00:00', 'EN COURS', 36.00, 'CB', 'CMD12345'),
('2024-11-26 13:00:00', 'PAYEE', 20.00, 'ESPECE', 'CMD12346');

-- Associer des plats aux commandes avec des modifications
INSERT INTO contenu_commande (commande_id, plat_id, quantite, modifications, prix_supplément) VALUES
(1, 1, 2, '{"ajouts":["Poulet"],"suppression":["Tomate"]}', 2.00), -- Salade César, modifiée
(1, 2, 1, NULL, 0.00), -- Pizza Margherita, sans modification
(2, 2, 1, '{"ajouts":["Champignon"]}', 1.50); -- Pizza Margherita, ajout champignons

-- Insérer des entrées pour le suivi des stocks
INSERT INTO imports_stocks (created_at, fichier_csv) VALUES
('2024-11-25 08:00:00', 'stocks_25_11.csv'),
('2024-11-26 08:00:00', 'stocks_26_11.csv');

