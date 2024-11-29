<?php

require_once('Databases.php');
$con = connexion();

$data = json_decode(file_get_contents('php://input'), true);

foreach ($data as $row) {
    $nom = $row[0];
    $quantite = $row[1];
    $prixUnitaire = $row[2];

    // Mettre à jour la quantité et le prix unitaire dans la base
    $stmt = $con->prepare('
        UPDATE ingredients
        SET quantite = quantite + ?, prix_unitaire = ?
        WHERE nom = ?
    ');
    $stmt->execute([$quantite, $prixUnitaire, $nom]);
}

echo json_encode(['success' => true]);
