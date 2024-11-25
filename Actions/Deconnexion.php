<?php
session_start();

// Nettoyer toutes les variables de session importantes
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page de connexion
header('Location: ../Admin/connexion.php');
exit();
?>
