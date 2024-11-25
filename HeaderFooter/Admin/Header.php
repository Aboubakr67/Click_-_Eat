<?php session_start(); ?>

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
            <?php if (isset($_SESSION['AUTH'])): ?>
                <?php if ($_SESSION['ROLE'] === 'A'): ?>
                    <a href="">Dashboard A</a>
                    <a href="">Profil A</a>
                    <a href="">Action A</a>
                <?php elseif ($_SESSION['ROLE'] === 'B'): ?>
                    <a href="">Dashboard B</a>
                    <a href="">Profil B</a>
                    <a href="">Action B</a>
                <?php elseif ($_SESSION['ROLE'] === 'C'): ?>
                    <a href="">Dashboard C</a>
                    <a href="">Profil C</a>
                    <a href="">Action C</a>
                <?php endif; ?>
                <a href="../Actions/Deconnexion.php" class="logout">Déconnexion</a>
            <?php else: ?>
                <a href="">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>