<?php
date_default_timezone_set('Europe/Paris');  // Remplacez par votre fuseau horaire si nécessaire
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fast food</title>
    <style>
        nav {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: #f5f5f5;
        }

        nav a {
            text-decoration: none;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        nav a:hover {
            background: #ddd;
        }

        .logout {
            color: white;
            background: red;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <?php if (isset($_SESSION['auth'])): ?>
                <?php if ($_SESSION['role'] === 'ZONE CUISINE'): ?>
                    <a href="../Admin/zone_cuisine.php">Zone cuisine</a>
                <?php elseif ($_SESSION['role'] === 'ZONE STOCK'): ?>
                    <a href="../Admin/zone_stock.php">Zone stock</a>
                <?php elseif ($_SESSION['role'] === 'ZONE MANAGEMENT'): ?>
                    <a href="../Admin/zone_admin.php">Dashboard</a>
                    <a href="../Admin/liste_utilisateurs.php">Liste des utilisateurs</a>
                <?php endif; ?>
                <p>Connecté en tant que : <?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom'] ?></p>
                <a href="../Actions/Deconnexion.php" class="logout">Déconnexion</a>
            <?php else: ?>
                <a href="../Admin/connexion.php">Se connecter</a>
            <?php endif; ?>
        </nav>
    </header>