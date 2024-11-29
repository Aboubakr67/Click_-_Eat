<?php
require_once('../HeaderFooter/Admin/Header.php');
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

require_once('../Actions/zone_admin_repo.php');

// Récupérer l'ID de l'utilisateur
$userId = (int) $_GET['id'];

// Supprimer l'utilisateur
$deleted = deleteUser($userId);

// Si l'utilisateur est supprimé
if ($deleted) {
    // Stocke le message de succès dans la session
    $_SESSION['success'] = "Utilisateur supprimé avec succès.";
    header("Location: liste_utilisateurs.php"); // Redirige vers la liste des utilisateurs
    exit;
} else {
    // Si la suppression échoue
    $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
    header("Location: liste_utilisateurs.php"); // Redirige vers la liste des utilisateurs
    exit;
}
?>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>
