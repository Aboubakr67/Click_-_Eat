<?php
require_once('../HeaderFooter/Client/Header.php');
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
            <button onclick="validateCart()"
                class="px-8 py-3 bg-[#D84315] text-white rounded-lg font-medium hover:bg-[#BF360C] transition-colors">
                Valider ma commande
            </button>
        </div>
    </div>
</div>

<script>
    // Function to delete menu item
    function deleteMenuItem(index) {
        const cart = getCart();
        cart.items.splice(index, 1);
        saveCart(cart);
        location.reload();
    }

    // Function to delete standalone entrée
    function deleteEntree(id) {
        const cart = getCart();
        cart.entrees = cart.entrees.filter(entree => entree.id !== id);
        saveCart(cart);
        location.reload();
    }

    // Function to delete standalone boisson
    function deleteBoisson(id) {
        const cart = getCart();
        cart.boissons = cart.boissons.filter(boisson => boisson.id !== id);
        saveCart(cart);
        location.reload();
    }

    // Function to delete standalone dessert
    function deleteDessert(id) {
        const cart = getCart();
        cart.desserts = cart.desserts.filter(dessert => dessert.id !== id);
        saveCart(cart);
        location.reload();
    }

    // Function to validate cart and proceed
    function validateCart() {
        const cart = getCart();
        const hasItems = (cart.items && cart.items.length > 0) ||
            (cart.entrees && cart.entrees.length > 0) ||
            (cart.boissons && cart.boissons.length > 0) ||
            (cart.desserts && cart.desserts.length > 0);

        if (hasItems) {
            // If there are menu items, go to order type selection
            if (cart.items && cart.items.length > 0) {
                window.location.href = 'choix_etat_commande.php';
            } else {
                // If only standalone items, go directly to payment
                window.location.href = 'choix_paiment.php';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const cart = getCart();
        const cartItemsContainer = document.getElementById('cart-items');
        let cartHTML = '<div class="space-y-6">';

        // Display menu items with their details
        if (cart.items && cart.items.length > 0) {
            cartHTML += '<div class="mb-8"><h2 class="text-xl font-bold mb-4">Menus</h2>';
            cart.items.forEach((item, index) => {
                cartHTML += `
                <div class="bg-gray-50 rounded-xl p-6 mt-4">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-start gap-4">
                            <img src="../Assets/images/${item.image}" alt="${item.name}" class="w-24 h-24 object-contain">
                            <div>
                                <h3 class="text-xl font-medium">${item.name}</h3>
                                <p class="text-[#D84315] font-medium mt-1">${item.price.toFixed(2)} €</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="choix_ingredients.php?formule_id=${item.id}" class="text-[#D84315] hover:underline">
                                Modifier
                            </a>
                            <button onclick="deleteMenuItem(${index})" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>`;

                // Show removed ingredients if any
                if (item.removedIngredients && item.removedIngredients.length > 0) {
                    cartHTML += `
                    <div class="ml-28 text-red-500 text-sm mb-2">
                        Sans: ${item.removedIngredients.map(ing => ing.name).join(', ')}
                    </div>`;
                }

                // Show entrées if any
                if (item.entrees && item.entrees.length > 0) {
                    cartHTML += `
                    <div class="ml-28 mt-4">
                        <h4 class="font-medium mb-2">Entrées:</h4>
                        <div class="space-y-2">`;
                    item.entrees.forEach(entree => {
                        cartHTML += `
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span>${entree.name}</span>
                                <span class="text-gray-500">x${entree.quantity}</span>
                            </div>
                            <span>${(entree.price * entree.quantity).toFixed(2)} €</span>
                        </div>`;
                    });
                    cartHTML += `</div></div>`;
                }

                // Show boissons if any
                if (item.boissons && item.boissons.length > 0) {
                    cartHTML += `
                    <div class="ml-28 mt-4">
                        <h4 class="font-medium mb-2">Boissons:</h4>
                        <div class="space-y-2">`;
                    item.boissons.forEach(boisson => {
                        cartHTML += `
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span>${boisson.name}</span>
                                <span class="text-gray-500">x${boisson.quantity}</span>
                            </div>
                            <span>${(boisson.price * boisson.quantity).toFixed(2)} €</span>
                        </div>`;
                    });
                    cartHTML += `</div></div>`;
                }

                // Show order type if set
                if (item.orderType) {
                    cartHTML += `
                    <div class="ml-28 mt-4 text-gray-600">
                        Type de commande: ${item.orderType === 'SUR_PLACE' ? 'Sur place' : 'À emporter'}
                    </div>`;
                }

                // Show subtotal for this menu
                const itemTotal = calculateItemTotal(item);
                cartHTML += `
                <div class="ml-28 mt-4 flex justify-between font-medium">
                    <span>Sous-total Menu ${index + 1}:</span>
                    <span>${itemTotal.toFixed(2)} €</span>
                </div>
            </div>`;
            });
            cartHTML += '</div>';
        }

        // Display standalone entrées
        if (cart.entrees && cart.entrees.length > 0) {
            cartHTML += `
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Entrées supplémentaires</h2>
                <div class="space-y-4">`;
            cart.entrees.forEach(entree => {
                cartHTML += `
                <div class="bg-gray-50 rounded-xl p-4 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <img src="../Assets/images/${entree.name.toLowerCase()}_menu.png" alt="${entree.name}" class="w-16 h-16 object-contain">
                        <div>
                            <h3 class="font-medium">${entree.name}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-gray-500">x${entree.quantity}</span>
                                <span class="text-[#D84315]">${(entree.price * entree.quantity).toFixed(2)} €</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="entrees.php" class="text-[#D84315] hover:underline">Modifier</a>
                        <button onclick="deleteEntree('${entree.id}')" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>`;
            });
            cartHTML += '</div></div>';
        }

        // Display standalone boissons
        if (cart.boissons && cart.boissons.length > 0) {
            cartHTML += `
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Boissons supplémentaires</h2>
                <div class="space-y-4">`;
            cart.boissons.forEach(boisson => {
                cartHTML += `
                <div class="bg-gray-50 rounded-xl p-4 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <img src="../Assets/images/boisson.png" alt="${boisson.name}" class="w-16 h-16 object-contain">
                        <div>
                            <h3 class="font-medium">${boisson.name}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-gray-500">x${boisson.quantity}</span>
                                <span class="text-[#D84315]">${(boisson.price * boisson.quantity).toFixed(2)} €</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="boissons_supplementaires.php" class="text-[#D84315] hover:underline">Modifier</a>
                        <button onclick="deleteBoisson('${boisson.id}')" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>`;
            });
            cartHTML += '</div></div>';
        }

        // Display standalone desserts
        if (cart.desserts && cart.desserts.length > 0) {
            cartHTML += `
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Desserts supplémentaires</h2>
                <div class="space-y-4">`;
            cart.desserts.forEach(dessert => {
                cartHTML += `
                <div class="bg-gray-50 rounded-xl p-4 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <img src="../Assets/images/${dessert.image}" alt="${dessert.name}" class="w-16 h-16 object-contain">
                        <div>
                            <h3 class="font-medium">${dessert.name}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-gray-500">x${dessert.quantity}</span>
                                <span class="text-[#D84315]">${(dessert.price * dessert.quantity).toFixed(2)} €</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="desserts.php" class="text-[#D84315] hover:underline">Modifier</a>
                        <button onclick="deleteDessert('${dessert.id}')" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>`;
            });
            cartHTML += '</div></div>';
        }

        cartHTML += '</div>';

        // Show empty cart message if no items
        if (!cart.items?.length && !cart.entrees?.length && !cart.boissons?.length && !cart.desserts?.length) {
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

        // Calculate and update total
        const total = calculateCartTotal(cart);
        cart.total = total;
        saveCart(cart);

        // Update total display
        const totalElement = document.querySelector('#cart-total');
        if (totalElement) {
            totalElement.textContent = `${total.toFixed(2)} €`;
        }
    });
</script>

<?php
require_once('../HeaderFooter/Client/Footer.php');
?>