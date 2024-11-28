<?php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borne de Commande</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../Assets/js/cart.js"></script>
</head>

<body class="flex min-h-screen bg-gray-50">
    <header class="w-[200px] min-h-screen bg-gray-100 fixed left-0 top-0">
        <nav class="flex flex-col h-full py-8 px-4 space-y-8">
            <div class="flex justify-center mb-8">
                <img src="../../Assets/images/logo-simple.png" alt="Click & Eat" class="w-16 h-16">
            </div>

            <a href="../../Client/formules.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/burger.png" alt="Menu" class="w-12 h-12 mb-2">
                <span>Menu</span>
            </a>

            <a href="../../Client/entrees.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/frite.png" alt="Frites" class="w-12 h-12 mb-2">
                <span>Frites</span>
            </a>

            <a href="../../Client/plats_resistance.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/burger.png" alt="Burger" class="w-12 h-12 mb-2">
                <span>Burger</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/sauce.png" alt="Sauce" class="w-12 h-12 mb-2">
                <span>Sauce</span>
            </a>

            <a href="../../Client/boissons.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/boisson.png" alt="Boisson" class="w-12 h-12 mb-2">
                <span>Boisson</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/salade.png" alt="Salade" class="w-12 h-12 mb-2">
                <span>Salade</span>
            </a>

            <div class="mt-auto text-center">
                <div class="mb-4">
                    <span class="text-gray-600">Total:</span>
                    <span id="cart-total" class="font-bold text-xl">0,00 €</span>
                </div>
                <a href="../../Client/panier.php" id="cart-button" class="hidden">
                    <button class="w-full px-4 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                        Voir mon panier
                    </button>
                </a>
            </div>
        </nav>
    </header>
    <div class="flex-1 ml-[200px]">
