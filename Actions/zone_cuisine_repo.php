<?php

require('Databases.php');


function getCommandes()
{
    try {
        $con = connexion();


        $stmt = $con->prepare("SELECT * FROM commandes WHERE statut = 'EN COURS' ORDER BY created_at ASC");
        $stmt->execute();

        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $commandes;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des commandes : " . $e->getMessage());
        return [];
    }
}


function setCommandePrete($commandeId)
{
    try {
        $con = connexion();

        $stmt = $con->prepare("UPDATE commandes SET statut = 'PRETE' WHERE id = :id");
        $stmt->execute(['id' => $commandeId]);

        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}

function getCommandeDetails($commande_id)
{
    try {
        // Connexion à la base de données
        $con = connexion(); // Assurez-vous que la fonction connexion() retourne un objet PDO

        $sql = "
            SELECT
                plats.nom AS plat_name,
                plat_personnalise.modifications,
                plat_personnalise.prix_supplément,
                plat_personnalise.quantite
            FROM plat_personnalise
            LEFT JOIN plats ON plat_personnalise.plat_id = plats.id
            WHERE plat_personnalise.commande_id = :id
        ";

        // Préparer la requête
        $stmt = $con->prepare($sql);

        // Lier le paramètre et exécuter la requête
        $stmt->execute(['id' => $commande_id]);

        // Récupérer tous les résultats sous forme de tableau
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retourner les résultats
        return $details;
    } catch (PDOException $e) {
        // Gérer les exceptions PDO
        echo "Erreur : " . $e->getMessage();
        return null;
    }
}
