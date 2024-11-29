<?php

require_once('Databases.php');


function getStockRealTime()
{

    $con = connexion();

    // Requête pour récupérer tous les ingrédients
    $stmt = $con->query("SELECT id, nom, quantite, prix_unitaire, image FROM ingredients");

    $ingredients = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ingredients[] = $row;
    }

    return $ingredients; // Retourner les résultats sous forme de tableau
}
