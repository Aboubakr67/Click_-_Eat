<?php
require('../HeaderFooter/Client/Header.php');
?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Mode de paiement</h1>
        <span id="cart-total" class="text-2xl font-bold">0,00 €</span>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4 min-h-[500px]">
        <div class="flex justify-center items-center gap-8 h-full">
            <div onclick="setPaymentMethod('CB')"
                 class="bg-white rounded-2xl shadow-md p-8 w-64 h-64 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition-shadow">
                <div class="w-24 h-24 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#D84315]">
                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                </div>
                <span class="text-xl font-medium text-center">Carte bancaire</span>
            </div>

            <div onclick="setPaymentMethod('ESPECE')"
                 class="bg-white rounded-2xl shadow-md p-8 w-64 h-64 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition-shadow">
                <div class="w-24 h-24 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#D84315]">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                </div>
                <span class="text-xl font-medium text-center">Espèces</span>
            </div>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <button onclick="window.location.href='choix_etat_commande.php'" 
                    class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
        </div>
    </div>

    <!-- Include mini cart -->
    <?php require('panier-mini.php'); ?>
</div>

<!-- Invoice Template -->
<template id="invoice-template">
    <div class="bg-white p-8 max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <img src="../Assets/images/logo_fast_food.png" alt="Logo" class="w-16 h-16 mr-4">
                <div>
                    <h1 class="text-2xl font-bold text-[#D84315]">Fast Food Borne</h1>
                    <p class="text-gray-600">Votre commande</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-gray-600">Date: <span class="invoice-date"></span></p>
                <p class="text-gray-600">N° Commande: <span class="invoice-number"></span></p>
            </div>
        </div>

        <div class="border-t border-b border-gray-200 py-4 mb-4">
            <div class="flex justify-between mb-2">
                <span class="font-medium">Article</span>
                <span class="font-medium">Prix</span>
            </div>
            <div class="invoice-items space-y-4">
                <!-- Items will be inserted here -->
            </div>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div>
                <p class="text-gray-600">Type de commande: <span class="order-type"></span></p>
                <p class="text-gray-600">Mode de paiement: <span class="payment-method"></span></p>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold">Total: <span class="invoice-total"></span></p>
            </div>
        </div>

        <div class="text-center text-gray-600 text-sm">
            <p>Merci de votre confiance !</p>
            <p>Fast Food Borne - 123 Avenue de la République - 75011 Paris</p>
        </div>
    </div>
</template>

<script>
// Debug function to log cart state
function logCartState() {
    const cart = getCart();
    console.log('Current cart state:', cart);
}

// Function to generate invoice number
function generateInvoiceNumber() {
    const date = new Date();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    return `INV-${date.getFullYear()}${(date.getMonth() + 1).toString().padStart(2, '0')}${date.getDate().toString().padStart(2, '0')}-${random}`;
}

// Function to format price
function formatPrice(price) {
    return `${parseFloat(price).toFixed(2)} €`;
}

// Function to generate invoice HTML
function generateInvoiceHTML(cart) {
    const template = document.getElementById('invoice-template').content.cloneNode(true);
    const invoiceDate = new Date().toLocaleDateString('fr-FR');
    const invoiceNumber = generateInvoiceNumber();

    // Set basic info
    template.querySelector('.invoice-date').textContent = invoiceDate;
    template.querySelector('.invoice-number').textContent = invoiceNumber;
    template.querySelector('.order-type').textContent = cart.orderType === 'SUR_PLACE' ? 'Sur place' : 'À emporter';
    template.querySelector('.payment-method').textContent = cart.paymentMethod === 'CB' ? 'Carte bancaire' : 'Espèces';

    // Add items
    const itemsContainer = template.querySelector('.invoice-items');

    // Add menu items
    if (cart.items && cart.items.length > 0) {
        cart.items.forEach((item, index) => {
            // Menu header
            const menuHeader = document.createElement('div');
            menuHeader.className = 'font-medium text-[#D84315] mb-2';
            menuHeader.textContent = `Menu ${index + 1}`;
            itemsContainer.appendChild(menuHeader);

            // Main menu item
            const mainItemDiv = document.createElement('div');
            mainItemDiv.className = 'flex justify-between';
            mainItemDiv.innerHTML = `
                <span>${item.name}</span>
                <span>${formatPrice(item.price)}</span>
            `;
            itemsContainer.appendChild(mainItemDiv);

            // Removed ingredients
            if (item.removedIngredients && item.removedIngredients.length > 0) {
                const removedDiv = document.createElement('div');
                removedDiv.className = 'text-red-500 text-sm ml-4';
                removedDiv.textContent = `Sans: ${item.removedIngredients.map(ing => ing.name).join(', ')}`;
                itemsContainer.appendChild(removedDiv);
            }

            // Menu entrées
            if (item.entrees && item.entrees.length > 0) {
                item.entrees.forEach(entree => {
                    const entreeDiv = document.createElement('div');
                    entreeDiv.className = 'flex justify-between ml-4';
                    entreeDiv.innerHTML = `
                        <span>${entree.name} x${entree.quantity}</span>
                        <span>${formatPrice(entree.price * entree.quantity)}</span>
                    `;
                    itemsContainer.appendChild(entreeDiv);
                });
            }

            // Menu boissons
            if (item.boissons && item.boissons.length > 0) {
                item.boissons.forEach(boisson => {
                    const boissonDiv = document.createElement('div');
                    boissonDiv.className = 'flex justify-between ml-4';
                    boissonDiv.innerHTML = `
                        <span>${boisson.name} x${boisson.quantity}</span>
                        <span>${formatPrice(boisson.price * boisson.quantity)}</span>
                    `;
                    itemsContainer.appendChild(boissonDiv);
                });
            }

            // Menu subtotal
            const subtotal = calculateItemTotal(item);
            const subtotalDiv = document.createElement('div');
            subtotalDiv.className = 'flex justify-between font-medium mt-2 mb-4';
            subtotalDiv.innerHTML = `
                <span>Sous-total Menu ${index + 1}</span>
                <span>${formatPrice(subtotal)}</span>
            `;
            itemsContainer.appendChild(subtotalDiv);
        });
    }

    // Add standalone entrées
    if (cart.entrees && cart.entrees.length > 0) {
        const entreesHeader = document.createElement('div');
        entreesHeader.className = 'font-medium text-[#D84315] mt-4 mb-2';
        entreesHeader.textContent = 'Entrées supplémentaires';
        itemsContainer.appendChild(entreesHeader);

        cart.entrees.forEach(entree => {
            const entreeDiv = document.createElement('div');
            entreeDiv.className = 'flex justify-between';
            entreeDiv.innerHTML = `
                <span>${entree.name} x${entree.quantity}</span>
                <span>${formatPrice(entree.price * entree.quantity)}</span>
            `;
            itemsContainer.appendChild(entreeDiv);
        });
    }

    // Add standalone boissons
    if (cart.boissons && cart.boissons.length > 0) {
        const boissonsHeader = document.createElement('div');
        boissonsHeader.className = 'font-medium text-[#D84315] mt-4 mb-2';
        boissonsHeader.textContent = 'Boissons supplémentaires';
        itemsContainer.appendChild(boissonsHeader);

        cart.boissons.forEach(boisson => {
            const boissonDiv = document.createElement('div');
            boissonDiv.className = 'flex justify-between';
            boissonDiv.innerHTML = `
                <span>${boisson.name} x${boisson.quantity}</span>
                <span>${formatPrice(boisson.price * boisson.quantity)}</span>
            `;
            itemsContainer.appendChild(boissonDiv);
        });
    }

    // Set total
    template.querySelector('.invoice-total').textContent = formatPrice(cart.total);

    return template;
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, checking cart state');
    logCartState();
    
    const cart = getCart();
    if (cart.paymentMethod) {
        const selectedDiv = document.querySelector(`[onclick="setPaymentMethod('${cart.paymentMethod}')"]`);
        if (selectedDiv) {
            selectedDiv.classList.add('ring-2', 'ring-[#D84315]');
        }
    }
});

// Modifier la fonction setPaymentMethod
async function setPaymentMethod(method) {
    const cart = getCart();
    cart.paymentMethod = method;
    saveCart(cart);

    try {
        // Envoyer les données au serveur avant d'afficher la facture
        const response = await fetch('../Actions/PostCommande.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(cart)
        });

        const result = await response.json();
        console.log('Réponse du serveur:', result);

        if (result.success) {
            // Continuer avec l'affichage de la facture
            showInvoice(cart);
        } else {
            alert('Erreur lors de l\'enregistrement de la commande');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'enregistrement de la commande');
    }
}

// Fonction pour afficher la facture
function showInvoice(cart) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50';
    
    const invoiceContainer = document.createElement('div');
    invoiceContainer.className = 'relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto';
    
    const invoiceContent = generateInvoiceHTML(cart);
    invoiceContainer.appendChild(invoiceContent);

    // Ajouter les boutons
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'flex justify-center gap-4 p-8 border-t';
    
    const printButton = document.createElement('button');
    printButton.className = 'px-8 py-3 bg-[#D84315] text-white rounded-lg font-medium hover:bg-[#BF360C] transition-colors';
    printButton.textContent = 'Imprimer';
    printButton.onclick = () => {
        window.print();
    };
    
    const closeButton = document.createElement('button');
    closeButton.className = 'px-8 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg font-medium hover:bg-gray-50 transition-colors';
    closeButton.textContent = 'Fermer';
    closeButton.onclick = () => {
        document.body.removeChild(modal);
        // Rediriger vers la page d'accueil ou une autre page
        window.location.href = 'formules.php';
    };
    
    buttonContainer.appendChild(printButton);
    buttonContainer.appendChild(closeButton);
    invoiceContainer.appendChild(buttonContainer);
    
    modal.appendChild(invoiceContainer);
    document.body.appendChild(modal);
}
</script>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
