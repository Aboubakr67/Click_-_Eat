<?php
require_once('../HeaderFooter/Admin/Header.php');
?>

<?php
// Vérifie si l'utilisateur est autorisé
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

require_once('../Actions/zone_admin_repo.php');

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe']; // Mot de passe en clair
    $role = htmlspecialchars($_POST['role']);


    // Vérifier si l'email existe déjà
    if (checkEmailExists($email)) {
        $_SESSION['error'] = "L'email existe déjà.";
    } else {
        // Validation du mot de passe
        if (strlen($mot_de_passe) < 10) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins 10 caractères.";
        } else {
            // Hacher le mot de passe
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Créer l'utilisateur
            $created = createUser($nom, $prenom, $email, $mot_de_passe_hash, $role);

            if ($created) {
                $_SESSION['success'] = "Utilisateur créé avec succès.";
                header("Location: liste_utilisateurs.php");
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la création de l'utilisateur.";
            }
        }
    }
}
?>

<h1>Ajouter un utilisateur</h1>

<?php

// Affichage du message d'erreur
if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>{$_SESSION['error']}</p>";
    unset($_SESSION['error']); // Supprimer le message après affichage
}


?>

<form method="POST" action="">
    <div>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
    </div>

    <div>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>
    </div>

    <div>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        <small>Le mot de passe doit contenir au moins 10 caractères.</small>
    </div>

    <div>
        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="ZONE CUISINE">ZONE CUISINE</option>
            <option value="ZONE STOCK">ZONE STOCK</option>
            <option value="ZONE MANAGEMENT">ZONE MANAGEMENT</option>
        </select>
    </div>

    <button type="submit">Créer l'utilisateur</button>
</form>

<a href="liste_utilisateurs.php">Retour à la liste des utilisateurs</a>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>