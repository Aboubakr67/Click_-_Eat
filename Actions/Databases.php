<?php

function connexion()
{
    $serveur = 'localhost';
    $utilisateur = 'root';
    $mot_de_passe = 'root';
    $bdd = 'click_and_eat';
    $port = 3308; // a changer

    try {

        $conn = "mysql:host=$serveur;port=$port;dbname=$bdd;charset=utf8";

        // Options pour sécuriser et configurer PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les exceptions en cas d'erreur
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mode de récupération par défaut
            PDO::ATTR_EMULATE_PREPARES => false, // Désactiver les requêtes préparées émulées pour plus de sécurité
        ];

        // Création de la connexion PDO
        $connexion = new PDO($conn, $utilisateur, $mot_de_passe, $options);

        // if ($connexion) {
        //     echo "connexion !";
        // } else {
        //     echo "non connecté !";
        // }

        return $connexion;
    } catch (PDOException $e) {
        // Gestion des erreurs de connexion
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}


connexion();
