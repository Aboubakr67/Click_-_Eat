<?php

require('Databases.php');

function getAllFormules()
{
    try {

        $con = connexion();

        $query = "SELECT id, nom, prix FROM formules";
        $stmt = $con->prepare($query);

        $stmt->execute();

        $formules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $formules;
    } catch (PDOException $e) {
        // Gestion des erreurs
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}

// Récupérer les plats de type 'PLAT' associés à la formule
function getPlatFromFormule($formule_id)
{
    try {
        $con = connexion();
        $query = "
            SELECT p.id, p.nom, p.prix
            FROM formule_plat fp
            INNER JOIN plats p ON fp.plat_id = p.id
            WHERE fp.formule_id = :formule_id AND p.type = 'PLAT'
        ";
        $stmt = $con->prepare($query);
        $stmt->execute(['formule_id' => $formule_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return null;
    }
}


function getIngredientsFromPlat($plat_id)
{
    try {
        $con = connexion();
        $query = "
            SELECT i.nom AS ingredient_nom, i.quantite AS ingredient_quantite
            FROM plat_ingredient pi
            INNER JOIN ingredients i ON pi.ingredient_id = i.id
            WHERE pi.plat_id = :plat_id
        ";
        $stmt = $con->prepare($query);
        $stmt->execute(['plat_id' => $plat_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}

function getSupplementIngredients()
{


    try {
        $con = connexion();

        $query = "
            SELECT id, nom, quantite, prix
            FROM ingredients
            WHERE nom IN ('Poivrons', 'Bacon', 'Avocat')
        ";
        $stmt = $con->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}
