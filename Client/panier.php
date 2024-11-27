<?php
require('../HeaderFooter/Client/Header.php');
?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8">
        <h1 class="text-4xl font-bold text-center">Mon panier</h1>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4 min-h-[500px]" id="cart-items">
        <!-- Cart items will be dynamically inserted here by JavaScript -->
    </div>

    <div class="flex items-center justify-between mt-6">
        <a href="formules.php">
            <button class="px-8 py-3 bg-[#D84315] text-white rounded-lg font-medium hover:bg-[#BF360C] transition-colors">
                Continuer mes achats
            </button>
        </a>
        <div class="flex items-center gap-8">
            <div class="flex items-baseline gap-3">
                <span class="text-gray-600">Total:</span>
                <span id="cart-total" class="font-bold text-xl">0,00 €</span>
            </div>
            <button onclick="validateStep('choix_etat_commande')" 
                    class="px-8 py-3 bg-[#D84315] text-white rounded-lg font-medium hover:bg-[#BF360C] transition-colors">
                Valider ma commande
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    const cartItemsContainer = document.getElementById('cart-items');
    let cartHTML = '<div class="space-y-4">';

    // Display menu items
    cart.items.forEach((item, index) => {
        cartHTML += `
            <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="../Assets/images/${item.image}" alt="${item.name}" class="w-16 h-16 object-contain">
                    <div>
                        <h3 class="font-medium">${item.name}</h3>
                        <p class="text-sm text-gray-600">${item.ingredients.map(ing => ing.name).join(', ')}</p>
                        <p class="text-gray-600">${item.price.toFixed(2)} €</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <a href="choix_ingredients.php?formule_id=${item.id}" class="px-4 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                        Modifier
                    </a>
                </div>
            </div>
        `;
    });

    // Display entrées
    cart.entrees.forEach(entree => {
        cartHTML += `
            <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="../Assets/images/${entree.name.toLowerCase()}_menu.png" alt="${entree.name}" class="w-16 h-16 object-contain">
                    <div>
                        <h3 class="font-medium">${entree.name}</h3>
                        <p class="text-gray-600">${entree.price.toFixed(2)} €</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <a href="edit_entrees.php" class="px-4 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                        Modifier
                    </a>
                </div>
            </div>
        `;
    });

    // Display drinks
    cart.boissons.forEach(boisson => {
        cartHTML += `
            <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="../Assets/images/Boissons/${boisson.name.toLowerCase()}.png" alt="${boisson.name}" class="w-16 h-16 object-contain">
                    <div>
                        <h3 class="font-medium">${boisson.name}</h3>
                        <p class="text-gray-600">${boisson.price.toFixed(2)} €</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <a href="boissons.php" class="px-4 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                        Modifier
                    </a>
                </div>
            </div>
        `;
    });

    cartHTML += '</div>';

    // Add modify order button if there are items
    if (cart.items.length > 0 || cart.entrees.length > 0 || cart.boissons.length > 0) {
        cartHTML += `
            <div class="flex justify-center mt-12">
                <button onclick="window.history.back()" 
                        class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                    Modifier ma commande
                </button>
            </div>
        `;
    } else {
        cartHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <p class="text-gray-500 text-lg mb-4">Votre panier est vide</p>
                <a href="formules.php" class="px-8 py-3 bg-[#D84315] text-white rounded-lg font-medium hover:bg-[#BF360C] transition-colors">
                    Commencer ma commande
                </a>
            </div>
        `;
    }

    cartItemsContainer.innerHTML = cartHTML;
    updateTotal();
});
</script>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
