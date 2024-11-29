<?php
date_default_timezone_set('Europe/Paris');
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ajout de html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Ajout de nos utilitaires PDF -->
    <script src="../Assets/js/pdf-utils.js"></script>
    <script src="../Assets/js/script.js"></script>
    <title>Fast food</title>
</head>

<body class="flex min-h-screen bg-gray-200">
    <header class="w-[200px] min-h-screen bg-gray-100 fixed left-0 top-0">
        <nav class="flex flex-col h-full py-8 px-4 space-y-8">
            <div class="flex justify-center mb-8">
                <a href="../Client/formules.php">
                    <img src="../Assets/images/logo-simple.png" alt="Click & Eat" class="w-16 h-16">
                </a>
            </div>

            <a href="../Client/formules.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../Assets/images/burger.png" alt="Menu" class="w-12 h-12 mb-2">
                <span>Menu</span>
            </a>

            <a href="../Client/entrees.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../Assets/images/frite.png" alt="Frites" class="w-12 h-12 mb-2">
                <span>Accompagnement</span>
            </a>

            <a href="../Client/boissons_supplementaires.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../Assets/images/boisson.png" alt="Boisson" class="w-12 h-12 mb-2">
                <span>Boisson</span>
            </a>

            <a href="../Client/desserts.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../Assets/images/cheesecake.jpg" alt="Dessert" class="w-12 h-12 mb-2 object-cover rounded-full">
                <span>Dessert</span>
            </a>
        </nav>
    </header>
    <div class="flex-1 ml-[200px]">