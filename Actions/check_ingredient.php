<?php

require_once('Databases.php');

$con = connexion();

if (isset($_GET['nom'])) {
    $nom = $_GET['nom'];

    // Vérifier si l'ingrédient existe en base
    $stmt = $con->prepare('SELECT COUNT(*) FROM ingredients WHERE nom = ?');
    $stmt->execute([$nom]);
    $exists = $stmt->fetchColumn() > 0;

    echo json_encode(['exists' => $exists]);
}
