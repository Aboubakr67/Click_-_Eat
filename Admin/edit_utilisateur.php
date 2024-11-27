<?php
require('../HeaderFooter/Admin/Header.php');
?>

<?php
// Vérifie si l'utilisateur est autorisé
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

// Vérifie si l'ID de l'utilisateur est passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Utilisateur introuvable.";
    exit;
}

require('../Actions/zone_admin_repo.php');

// Récupérer les détails de l'utilisateur
$userId = (int) $_GET['id'];
$user = getUserById($userId);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    $updated = updateUser($userId, $nom, $prenom, $email, $role);

    if ($updated) {
        // Enregistrement du message de succès dans la session
        $_SESSION['success'] = "Mise à jour réussie.";
        // Redirection vers la liste des utilisateurs après mise à jour
        header("Location: liste_utilisateurs.php");
        exit;
    } else {
        // Enregistrement du message d'erreur dans la session
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
        // Rester sur la page pour afficher l'erreur
        header("Location: edit_utilisateur.php?id=$userId");
        exit;
    }
}
?>

<h1>Modifier l'utilisateur</h1>

<?php

// Affichage des messages d'erreur
if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>{$_SESSION['error']}</p>";
    unset($_SESSION['error']); // Supprimer le message après affichage
}

?>

<form method="POST" action="">
    <div>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
    </div>

    <div>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
    </div>

    <div>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div>
        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="ZONE CUISINE" <?= $user['role'] === 'ZONE CUISINE' ? 'selected' : '' ?>>ZONE CUISINE</option>
            <option value="ZONE STOCK" <?= $user['role'] === 'ZONE STOCK' ? 'selected' : '' ?>>ZONE STOCK</option>
            <option value="ZONE MANAGEMENT" <?= $user['role'] === 'ZONE MANAGEMENT' ? 'selected' : '' ?>>ZONE MANAGEMENT</option>
        </select>
    </div>

    <button type="submit">Mettre à jour</button>
</form>

<a href="liste_utilisateurs.php">Retour à la liste des utilisateurs</a>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>