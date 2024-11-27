<?php

require('Databases.php');

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
