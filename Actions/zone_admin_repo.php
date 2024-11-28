<?php

require('Databases.php');
require('ElementCommande.php');

function getAllUsers()
{
    try {

        $con = connexion();

        $query = "SELECT id, nom, prenom, email, role FROM users";
        $stmt = $con->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}

// Récupérer un utilisateur par son ID
function getUserById($id)
{
    try {
        $con = connexion();

        $query = "SELECT id, nom, prenom, role, email FROM users WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}

// Mettre à jour un utilisateur
function updateUser($id, $nom, $prenom, $email, $role)
{
    try {
        $con = connexion();

        $query = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email, role = :role WHERE id = :id";
        $stmt = $con->prepare($query);

        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}


function deleteUser($userId)
{
    try {
        $con = connexion();

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $con->prepare($query);

        // Liaison des paramètres
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        // Exécution de la requête
        $stmt->execute();

        return $stmt->rowCount() > 0; // Retourne true si une ligne a été supprimée
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}


// Fonction pour vérifier si l'email existe déjà
function checkEmailExists($email)
{
    try {
        $con = connexion();

        // Vérifier si l'email existe déjà
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Si le nombre d'utilisateurs avec cet email est supérieur à 0, l'email existe déjà
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur lors de la vérification de l'email : " . $e->getMessage();
        return false;
    }
}

// Fonction pour créer un utilisateur
function createUser($nom, $prenom, $email, $mot_de_passe, $role)
{
    try {
        $con = connexion();

        // Insérer l'utilisateur dans la base de données
        $query = "INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";
        $stmt = $con->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':role', $role);

        $stmt->execute();

        return true; // L'utilisateur a été créé avec succès
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
        return false;
    }
}



// ------------------------------------------ Plats ---------------

// Fonction pour récupérer les plats
// function getPlats()
// {
//     try {
//         $con = connexion();
//         // Requête pour récupérer les plats avec leurs ingrédients
//         $query = "
//             SELECT p.id, p.nom, p.image, p.prix, p.type, GROUP_CONCAT(i.nom SEPARATOR ', ') AS ingredients
//             FROM plats p
//             LEFT JOIN plat_ingredient pi ON p.id = pi.plat_id
//             LEFT JOIN ingredients i ON pi.ingredient_id = i.id
//             GROUP BY p.id
//         ";
//         $stmt = $con->prepare($query);
//         $stmt->execute();
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         echo "Erreur : " . $e->getMessage();
//         return [];
//     }
// }


function getPlats(): array
{
    try {
        $con = connexion();
        $query = "
            SELECT p.id, p.nom, p.image, p.prix, p.type, 
                   GROUP_CONCAT(i.nom SEPARATOR ', ') AS ingredients
            FROM plats p
            LEFT JOIN plat_ingredient pi ON p.id = pi.plat_id
            LEFT JOIN ingredients i ON pi.ingredient_id = i.id
            GROUP BY p.id
        ";
        $stmt = $con->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $plats = [];
        foreach ($results as $row) {
            $ingredients = !empty($row['ingredients']) ? explode(', ', $row['ingredients']) : [];
            $plats[] = new Plat(
                (int)$row['id'],
                $row['nom'],
                (float)$row['prix'],
                $row['type'],
                $row['image'],
                $ingredients
            );
        }

        // var_dump($plats);
        // exit;

        return $plats;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}


// Fonction pour récupérer les détails du plat à partir de l'ID
function getPlatById($platId)
{
    try {
        $con = connexion();
        $query = "SELECT * FROM plats WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $platId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return null;
    }
}


// Fonction pour récupérer les ingrédients associés à un plat
function getIngredientsByPlat($platId)
{
    try {
        $con = connexion();
        $query = "SELECT ingredient_id FROM plat_ingredient WHERE plat_id = :plat_id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}

// Fonction pour récupérer tous les ingrédients
function getIngredients()
{
    try {
        $con = connexion();
        $query = "SELECT * FROM ingredients";
        $stmt = $con->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}


function updatePlat($platId, $nom, $prix, $type, $image, $ingredients)
{
    try {
        $con = connexion();

        // Mise à jour des informations du plat
        $query = "UPDATE plats SET nom = :nom, prix = :prix, type = :type, image = :image WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $platId, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':image', $image);
        $stmt->execute();

        // Récupérer les ingrédients actuels du plat
        $currentIngredientsQuery = "SELECT ingredient_id FROM plat_ingredient WHERE plat_id = :plat_id";
        $currentIngredientsStmt = $con->prepare($currentIngredientsQuery);
        $currentIngredientsStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
        $currentIngredientsStmt->execute();
        $currentIngredients = $currentIngredientsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

        // Supprimer les ingrédients qui ne sont plus sélectionnés
        $ingredientsToRemove = array_diff($currentIngredients, $ingredients);
        if (!empty($ingredientsToRemove)) {
            $removeQuery = "DELETE FROM plat_ingredient WHERE plat_id = :plat_id AND ingredient_id IN (" . implode(',', array_map('intval', $ingredientsToRemove)) . ")";
            $removeStmt = $con->prepare($removeQuery);
            $removeStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
            $removeStmt->execute();
        }

        // Ajouter les nouveaux ingrédients
        foreach ($ingredients as $ingredientId) {
            // Si l'association n'existe pas encore, on l'ajoute
            if (!in_array($ingredientId, $currentIngredients)) {
                $insertQuery = "INSERT INTO plat_ingredient (plat_id, ingredient_id, quantite) VALUES (:plat_id, :ingredient_id, 1)";
                $insertStmt = $con->prepare($insertQuery);
                $insertStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
                $insertStmt->bindParam(':ingredient_id', $ingredientId, PDO::PARAM_INT);
                $insertStmt->execute();
            }
        }

        return true; // Succès
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}


// Fonction pour vérifier si le plat est utilisé dans l'une des tables (contenu_commande, formule_plat)
function checkPlatAssociation($platId)
{
    try {
        $con = connexion();

        // Vérifier si le plat est utilisé dans la table contenu_commande
        $queryContenuCommande = "SELECT COUNT(*) FROM contenu_commande WHERE plat_id = :plat_id";
        $stmtContenuCommande = $con->prepare($queryContenuCommande);
        $stmtContenuCommande->bindParam(':plat_id', $platId, PDO::PARAM_INT);
        $stmtContenuCommande->execute();

        $contenuCommandeCount = $stmtContenuCommande->fetchColumn();

        // Vérifier si le plat est utilisé dans la table plat_ingredient
        // $queryPlatIngredient = "SELECT COUNT(*) FROM plat_ingredient WHERE plat_id = :plat_id";
        // $stmtPlatIngredient = $con->prepare($queryPlatIngredient);
        // $stmtPlatIngredient->bindParam(':plat_id', $platId, PDO::PARAM_INT);
        // $stmtPlatIngredient->execute();

        // $platIngredientCount = $stmtPlatIngredient->fetchColumn();

        // Vérifier si le plat est utilisé dans la table formule_plat
        $queryFormulePlat = "SELECT COUNT(*) FROM formule_plat WHERE plat_id = :plat_id";
        $stmtFormulePlat = $con->prepare($queryFormulePlat);
        $stmtFormulePlat->bindParam(':plat_id', $platId, PDO::PARAM_INT);
        $stmtFormulePlat->execute();

        $formulePlatCount = $stmtFormulePlat->fetchColumn();

        var_dump($contenuCommandeCount);
        // var_dump($platIngredientCount);
        var_dump($formulePlatCount);

        // Si le plat est utilisé dans l'une des tables, ne pas permettre la suppression
        return $contenuCommandeCount > 0 || $formulePlatCount > 0;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}


function insertPlat($nom, $prix, $type, $image)
{
    try {
        $con = connexion(); // Assurez-vous que cette fonction de connexion est définie

        // Requête d'insertion du plat
        $query = "INSERT INTO plats (nom, prix, type, image) VALUES (:nom, :prix, :type, :image)";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':image', $image);

        // Exécution de la requête
        $stmt->execute();

        // Récupérer l'ID du plat inséré
        return $con->lastInsertId(); // Retourne l'ID du dernier enregistrement inséré
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur lors de l'insertion du plat : " . $e->getMessage();
        return null; // Retourne null en cas d'erreur
    }
}


function insertIngredientsToPlat($platId, $ingredients)
{
    try {
        $con = connexion(); // Assurez-vous que cette fonction de connexion est définie

        // Préparer la requête d'insertion des ingrédients
        $query = "INSERT INTO plat_ingredient (plat_id, ingredient_id) VALUES (:plat_id, :ingredient_id)";
        $stmt = $con->prepare($query);

        // Associer chaque ingrédient au plat
        foreach ($ingredients as $ingredientId) {
            $stmt->bindParam(':plat_id', $platId);
            $stmt->bindParam(':ingredient_id', $ingredientId);
            $stmt->execute(); // Exécute l'insertion pour chaque ingrédient
        }

        return true; // Retourne true si l'insertion est réussie
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur lors de l'insertion des ingrédients : " . $e->getMessage();
        return false; // Retourne false en cas d'erreur
    }
}


// ------------------------------------------ Formules ------------------------------------

// Récupérer les plats par type (plat, boisson, dessert)
function getPlatsByType($type)
{
    try {
        $con = connexion();

        // Requête pour récupérer les plats en fonction du type
        $query = "SELECT id, nom, image FROM plats WHERE type = :type";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}


//  ---------------------------------------------   COMMANDES -------------------------------------------------------

function generateCodeCommande($paiement_method)
{

    $con = connexion();

    // Valider le mode de paiement
    $valid_methods = ['CB', 'ESP'];
    if (!in_array($paiement_method, $valid_methods)) {
        throw new Exception("Mode de paiement invalide");
    }

    // Requête pour récupérer le dernier code_commande pour le mode de paiement donné
    $stmt = $con->prepare("
        SELECT code_commande 
        FROM commandes 
        WHERE paiement_method = :paiement_method 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->execute(['paiement_method' => $paiement_method]);

    $last_code = $stmt->fetchColumn();

    // Si aucun code commande n'existe pour ce mode, commencer avec 'CB01' ou 'ESP01'
    if (!$last_code) {
        return $paiement_method . '01';
    }

    // Extraire la partie numérique du dernier code
    $numeric_part = (int)substr($last_code, strlen($paiement_method));

    // Incrémenter la partie numérique
    $new_numeric_part = $numeric_part + 1;

    // Reformater avec un padding de 2 chiffres
    $new_code = $paiement_method . str_pad($new_numeric_part, 2, '0', STR_PAD_LEFT);

    return $new_code;
}




function insertCommande($createdAt, $statut, $total, $paiementMethod, $codeCommande, $datePrete = null, $employe = null)
{
    $con = connexion();

    $query = "INSERT INTO commandes (created_at, statut, total, paiement_method, code_commande, date_prete, employe) 
              VALUES (:created_at, :statut, :total, :paiement_method, :code_commande, :date_prete, :employe)";
    $stmt = $con->prepare($query);

    $stmt->execute([
        ':created_at' => $createdAt,
        ':statut' => $statut,
        ':total' => $total,
        ':paiement_method' => $paiementMethod,
        ':code_commande' => $codeCommande,
        ':date_prete' => $datePrete,
        ':employe' => $employe,
    ]);

    return $con->lastInsertId(); // Retourne l'ID de la commande insérée
}


function insertContenuCommande($commandeId, $platId, $quantite = 1, $modifications = '{"ajouts":[],"suppression":[]}', $prixSupplement = 0)
{

    $con = connexion();


    $query = "INSERT INTO contenu_commande (commande_id, plat_id, quantite, modifications, prix_supplément) 
              VALUES (:commande_id, :plat_id, :quantite, :modifications, :prix_supplement)";
    $stmt = $con->prepare($query);

    $stmt->execute([
        ':commande_id' => $commandeId,
        ':plat_id' => $platId,
        ':quantite' => $quantite,
        ':modifications' => $modifications,
        ':prix_supplement' => $prixSupplement,
    ]);
}

function insertHistoriqueIngredient($ingredientId, $dateUtilisee, $quantite)
{

    $con = connexion();

    $query = "INSERT INTO historique_ingredients_utilisee (ingredient_id, date_utilisee, quantite) 
              VALUES (:ingredient_id, :date_utilisee, :quantite)";
    $stmt = $con->prepare($query);

    $stmt->execute([
        ':ingredient_id' => $ingredientId,
        ':date_utilisee' => $dateUtilisee,
        ':quantite' => $quantite,
    ]);
}


function processCommande($commandeData, $contenuCommandeData, $historiqueIngredientsData)
{
    try {

        $con = connexion();
        // Commencer une transaction
        $con->beginTransaction();

        // Générer le code commande
        $commande['code_commande'] = generateCodeCommande($commandeData['paiement_method']);

        // 1. Insérer dans commandes
        $commandeId = insertCommande(
            $commandeData['created_at'], // ici c'est date actuelle
            $commandeData['statut'], // statut EN COURS
            $commandeData['total'], // faire le calcule de tout les plats commander
            $commandeData['paiement_method'],
            $commandeData['code_commande'],
            $commandeData['date_prete'] ?? null,
            $commandeData['employe'] ?? null
        );

        // 2. Insérer les plats dans contenu_commande
        foreach ($contenuCommandeData as $plat) {
            insertContenuCommande(
                $commandeId,
                $plat['plat_id'],
                $plat['quantite'],
                $plat['modifications'] ?? '{"ajouts":[],"suppression":[]}',
                $plat['prix_supplément'] ?? 0
            );
        }

        // 3. Insérer dans historique_ingredients_utilisee
        foreach ($historiqueIngredientsData as $ingredient) {
            insertHistoriqueIngredient(
                $ingredient['ingredient_id'],
                $ingredient['date_utilisee'],
                $ingredient['quantite']
            );
        }

        // Valider la transaction
        $con->commit();

        return $commandeId; // Retourne l'ID de la commande
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $con->rollBack();
        throw $e; // Propager l'exception
    }
}
