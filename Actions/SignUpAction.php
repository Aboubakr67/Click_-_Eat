<?php
session_start();
require('Databases.php');

if (isset($_POST['validate'])) {
    if (!empty(!empty($_POST['nom'] && $_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirmPassword']) )) {
        $nom = filter_input(INPUT_POST, "nom");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmPassword = filter_input(INPUT_POST, "confirmPassword");


        // Vérifier si le mot de passe et le confirmPassword sont les mêmes
        if ($password !== $confirmPassword) {
            $_SESSION['errorMessage'] = "Les mots de passe ne correspondent pas.";
        } else {
            // Vérifier si l'e-mail est unique dans la base de données
            $bdd = connexion();
            $sql = "SELECT COUNT(*) AS count FROM Users WHERE email = :email";
            $stmt = $bdd->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $_SESSION['errorMessage'] = "Cette adresse e-mail est déjà utilisée.";
            }  else {

                // On hash le password
                $password = password_hash($password, PASSWORD_DEFAULT);
                $bdd = connexion();
                // On insère l'user dans la bdd
                $sql = "INSERT INTO Users (nom, email, password_user) VALUES (:nom, :email, :password)";
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();


                // Requete pour récuperer l'user dans la bdd
                $sql = "SELECT id, nom, email, password_user FROM Users WHERE email = :email";
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Stockage des donnes dans la session
                $_SESSION['auth'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['email'] = $user['email'];



                header('Location: ../dashboard.php ');
                exit();
            }
        }

    } else {
        $_SESSION['errorMessage'] = "Veuillez remplir tous les champs.";
    }
}

// Redirection vers la page d'inscription
header('Location: ../inscription.php');
exit();
