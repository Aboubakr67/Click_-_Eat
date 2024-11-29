<?php

require_once('Databases.php');


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


function setCommandePrete($commandeId, $idEmploye)
{
    try {
        $con = connexion();

        $stmt = $con->prepare("UPDATE commandes SET statut = 'PRETE', date_prete = NOW(), employe = :idEmploye  WHERE id = :id");
        $stmt->execute([
            'id' => $commandeId,
            'idEmploye' => $idEmploye
        ]);

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
                contenu_commande.modifications,
                contenu_commande.prix_supplément,
                contenu_commande.quantite
            FROM contenu_commande
            LEFT JOIN plats ON contenu_commande.plat_id = plats.id
            WHERE contenu_commande.commande_id = :id
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


// Le temps moyen de réalisation d’une commande
function getTempsMoyenCommande()
{
    try {
        $con = connexion();

        // Calculer le temps moyen en minutes entre la création et la date de préparation des commandes
        $stmt = $con->prepare("SELECT AVG(TIMESTAMPDIFF(SECOND, created_at, date_prete)) / 60 AS avg_time_minutes
                               FROM commandes
                               WHERE statut = 'PRETE' AND date_prete IS NOT NULL");
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si aucune commande n'est trouvée
        if ($result['avg_time_minutes'] === null) {
            return 'Aucune commande';
        }

        // Retourner le temps moyen (en minutes)
        return round($result['avg_time_minutes'], 2);
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
