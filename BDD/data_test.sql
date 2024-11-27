USE click_and_eat;

-- Insertion des utilisateurs (administrateurs)
INSERT INTO users (nom, prenom, role, email, mot_de_passe) VALUES
('Dupont', 'Jean', 'ZONE CUISINE', 'dj@gmail.com',  '$2y$10$YT7rGiEXq.7xTxZ0Uk/dy.KbLhWIxkQd1AbivyUJGZKA3zR5Da1Le'),
('Martin', 'Claire', 'ZONE STOCK','mc@gmail.com', '$2y$10$YT7rGiEXq.7xTxZ0Uk/dy.KbLhWIxkQd1AbivyUJGZKA3zR5Da1Le'),
('Lemoine', 'Pierre', 'ZONE MANAGEMENT', 'lp@gmail.com', '$2y$10$YT7rGiEXq.7xTxZ0Uk/dy.KbLhWIxkQd1AbivyUJGZKA3zR5Da1Le');


-- Insertion des plats dans `plats`
INSERT INTO plats (nom, image, prix, type) VALUES
('Burger Mixte', 'burger_mixte.jpg', 10.00, 'PLAT'), -- Plat principal dans Menu Classique
('Mini-Burger', 'mini_burger.jpg', 7.00, 'PLAT'), -- Plat principal dans Menu Enfant
('Salade César', 'salade_cesar.jpg', 8.50, 'ENTREE'), -- Entrée pour le Menu Végétarien
('Steak de Saumon', 'steak_saumon.jpg', 15.00, 'PLAT'), -- Plat principal dans Menu Premium
('Fondant au Chocolat', 'fondant_chocolat.jpg', 3.50, 'DESSERT'), -- Dessert dans Menu Classique
('Tarte aux Pommes', 'tarte_pommes.jpg', 4.00, 'DESSERT'), -- Dessert dans Menu Gourmet
('Glace', 'glace.jpg', 2.50, 'DESSERT'), -- Dessert dans Menu Enfant
('Cheesecake', 'cheesecake.jpg', 4.50, 'DESSERT'), -- Dessert dans Menu Végétarien
('Mousse au Chocolat', 'mousse_chocolat.jpg', 5.00, 'DESSERT'), -- Dessert dans Menu Premium
('Eau Minérale', 'eau_minerale.jpg', 2.00, 'BOISSON'), -- Boisson dans Menu Classique
('Soda Cola', 'soda_cola.jpg', 3.00, 'BOISSON'), -- Boisson dans Menu Gourmet
('Jus de Fruits', 'jus_fruits.jpg', 2.50, 'BOISSON'), -- Boisson dans Menu Enfant
('Eau Gazeuse', 'eau_gazeuse.jpg', 2.50, 'BOISSON'), -- Boisson dans Menu Végétarien
('Cocktail Maison', 'cocktail_maison.jpg', 7.00, 'BOISSON'); -- Boisson dans Menu Premium

-- Insertion des ingrédients dans `ingredients`
INSERT INTO ingredients (nom, image, quantite) VALUES
('Poulet', 'poulet.jpg', 100), -- Utilisé dans plusieurs plats
('Tomate', 'tomate.jpg', 150), -- Utilisé pour les salades et burgers
('Fromage', 'fromage.jpg', 120), -- Fromage pour burgers et autres plats
('Laitue', 'laitue.jpg', 80), -- Laitue pour salades et burgers
('Oignons', 'oignons.jpg', 60), -- Oignons pour burgers
('Saumon', 'saumon.jpg', 50), -- Saumon pour le plat Steak de Saumon
('Chocolat', 'chocolat.jpg', 70), -- Chocolat pour les desserts
('Pomme', 'pomme.jpg', 50), -- Pommes pour la tarte
('Biscuit', 'biscuit.jpg', 40), -- Base pour cheesecake
('Crème', 'creme.jpg', 90); -- Crème pour plusieurs desserts

-- Insertion des formules dans `formules`
INSERT INTO formules (nom, prix) VALUES
('Menu Classique', 16.00), -- Burger Mixte + Eau Minérale + Fondant au Chocolat
('Menu Gourmet', 20.00), -- Burger Mixte + Soda Cola + Tarte aux Pommes
('Menu Enfant', 12.00), -- Mini-Burger + Jus de Fruits + Glace
('Menu Végétarien', 15.00), -- Salade César + Eau Gazeuse + Cheesecake
('Menu Premium', 25.00); -- Steak de Saumon + Cocktail Maison + Mousse au Chocolat

-- Insertion des associations formules/plats dans `formule_plat`
INSERT INTO formule_plat (formule_id, plat_id) VALUES
(1, 1), (1, 10), (1, 5), -- Menu Classique
(2, 1), (2, 11), (2, 6), -- Menu Gourmet
(3, 2), (3, 12), (3, 7), -- Menu Enfant
(4, 3), (4, 13), (4, 8), -- Menu Végétarien
(5, 4), (5, 14), (5, 9); -- Menu Premium

-- Insertion des ingrédients pour chaque plat dans `plat_ingredient`
INSERT INTO plat_ingredient (plat_id, ingredient_id, quantite) VALUES
(1, 1, 1), (1, 2, 1), (1, 3, 1), (1, 4, 1), (1, 5, 1), -- Burger Mixte
(2, 1, 1), (2, 2, 1), (2, 3, 1), (2, 4, 1), -- Mini-Burger
(3, 1, 1), (3, 2, 1), (3, 4, 1), -- Salade César
(4, 6, 1), -- Steak de Saumon
(5, 7, 1), -- Fondant au Chocolat
(6, 8, 1), -- Tarte aux Pommes
(7, 7, 1), -- Glace
(8, 9, 1), (8, 10, 1), -- Cheesecake
(9, 7, 1), -- Mousse au Chocolat
(10, NULL, NULL), (11, NULL, NULL), (12, NULL, NULL), (13, NULL, NULL), (14, NULL, NULL); -- Boissons sans ingrédients

-- Insertion des commandes dans `commandes`
INSERT INTO commandes (created_at, statut, total, paiement_method, code_commande) VALUES
(NOW(), 'EN COURS', 16.00, 'CB', 'CMD123'), -- Commande en cours avec Menu Classique
(NOW(), 'PRETE', 25.00, 'ESPECE', 'CMD124'); -- Commande prête avec Menu Premium

-- Insertion du contenu des commandes dans `contenu_commande`
INSERT INTO contenu_commande (commande_id, plat_id, quantite, modifications, prix_supplément) VALUES
(1, 1, 1, '{"ajouts":["Fromage"],"suppression":["Oignons"]}', 1.00), -- Commande 1 avec modification sur Burger Mixte
(1, 10, 1, '{"ajouts":[],"suppression":[]}', 0.00), -- Commande 1 avec Eau Minérale
(1, 5, 1, '{"ajouts":[],"suppression":[]}', 0.00), -- Commande 1 avec Fondant au Chocolat
(2, 4, 1, '{"ajouts":["Herbes"],"suppression":[]}', 2.00), -- Commande 2 avec Steak de Saumon
(2, 14, 1, '{"ajouts":[],"suppression":[]}', 0.00), -- Commande 2 avec Cocktail Maison
(2, 9, 1, '{"ajouts":[],"suppression":[]}', 0.00); -- Commande 2 avec Mousse au Chocolat

-- Insertion des données dans `historique_ingredients_utilisee`
INSERT INTO historique_ingredients_utilisee (ingredient_id, date_utilisee, quantite) VALUES
(1, NOW(), 10), -- Poulet utilisé
(2, NOW(), 5), -- Tomates utilisées
(3, NOW(), 3), -- Fromage utilisé
(4, NOW(), 2), -- Laitue utilisée
(5, NOW(), 1), -- Oignons utilisés
(6, NOW(), 5), -- Saumon utilisé
(7, NOW(), 4); -- Chocolat utilisé

-- Insertion des données dans `imports_stocks`
INSERT INTO imports_stocks (created_at, fichier_csv) VALUES
(NOW(), 'import_stock_2024-11-01.csv'), -- Import initial
(NOW(), 'import_stock_2024-11-02.csv'); -- Import suivant