<?php
header('Content-Type: application/json');

// Recevoir les données JSON
$data = json_decode(file_get_contents('php://input'), true);

// Insérer dans la base de données ici
// TODO: Ajoute le code pour l'insertion en base de données

// Echo les données reçues pour debug
echo json_encode([
    'success' => true,
    'message' => 'Données reçues avec succès',
    'data' => $data
]);
?>
