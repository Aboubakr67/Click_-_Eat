// Cart structure in localStorage
const STORAGE_KEY = 'fastfood_cart';

// Initialize cart structure
const initCart = () => {
    if (!localStorage.getItem(STORAGE_KEY)) {
        const cart = {
            items: [], // Array of menu items with their ingredients
            entrees: [], // Optional entrées
            boissons: [], // Optional drinks
            orderType: null, // 'SUR_PLACE' or 'A_EMPORTER'
            paymentMethod: null, // 'CB' or 'ESPECE'
            total: 0,
            step: 'menu' // Current step in order process: menu, ingredients, entrees, boissons, orderType, payment
        };
        saveCart(cart);
    }
    updateCartDisplay();
};

// Save cart to localStorage
const saveCart = (cart) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateCartDisplay();
};

// Get cart from localStorage
const getCart = () => {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
};

// Update total price
const updateTotal = () => {
    const cart = getCart();
    let total = 0;

    // Calculate menu items total
    cart.items.forEach(item => {
        total += parseFloat(item.price);
        if (item.addedIngredients) {
            item.addedIngredients.forEach(ing => {
                if (ing.price) total += parseFloat(ing.price);
            });
        }
    });

    // Add entrées total
    cart.entrees.forEach(entree => {
        total += parseFloat(entree.price);
    });

    // Add drinks total
    cart.boissons.forEach(boisson => {
        total += parseFloat(boisson.price);
    });

    cart.total = total;
    saveCart(cart);
    
    // Update total display in header
    const totalElement = document.querySelector('#cart-total');
    if (totalElement) {
        totalElement.textContent = `${total.toFixed(2)} €`;
    }
};

// Add menu item to cart
const addMenuItem = (id, name, price, image) => {
    const cart = getCart();
    cart.items.push({
        id,
        name,
        price: parseFloat(price),
        image,
        addedIngredients: [], // List of ingredients included
        removedIngredients: [] // List of ingredients removed
    });
    cart.step = 'ingredients';
    saveCart(cart);
    window.location.href = `choix_ingredients.php?formule_id=${id}`;
};

// Toggle ingredient (remove/restore)
const toggleIngredient = (id, name) => {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    // Initialize arrays if they don't exist
    if (!currentItem.addedIngredients) currentItem.addedIngredients = [];
    if (!currentItem.removedIngredients) currentItem.removedIngredients = [];
    
    // Find ingredient in removed list
    const isRemoved = currentItem.removedIngredients.some(ing => ing.id === id);
    
    if (!isRemoved) {
        // Add to removed list
        currentItem.removedIngredients.push({
            id,
            name
        });
    } else {
        // Remove from removed list
        currentItem.removedIngredients = currentItem.removedIngredients.filter(ing => ing.id !== id);
    }
    
    saveCart(cart);
    updateTotal();
};

// Toggle entrée
const toggleEntree = (id, name, price) => {
    const cart = getCart();
    if (!cart.entrees) cart.entrees = [];
    
    const isAdded = cart.entrees.some(entree => entree.id === id);
    
    if (!isAdded) {
        // Add entrée
        cart.entrees.push({
            id,
            name,
            price: parseFloat(price)
        });
    } else {
        // Remove entrée
        cart.entrees = cart.entrees.filter(entree => entree.id !== id);
    }
    
    saveCart(cart);
    updateTotal();
    return !isAdded; // Return new state for UI update
};

// Add/remove drink
const toggleBoisson = (id, name, price, action = 'add') => {
    const cart = getCart();
    
    if (action === 'add') {
        cart.boissons.push({
            id,
            name,
            price: parseFloat(price)
        });
    } else {
        cart.boissons = cart.boissons.filter(boisson => boisson.id !== id);
    }
    
    saveCart(cart);
    updateTotal();
};

// Set order type
const setOrderType = (type) => {
    const cart = getCart();
    cart.orderType = type;
    cart.step = 'payment';
    saveCart(cart);
    window.location.href = 'choix_paiment.php';
};

// Set payment method
const setPaymentMethod = (method) => {
    const cart = getCart();
    cart.paymentMethod = method;
    saveCart(cart);
    generateInvoice();
};

// Update cart icon and total in header
const updateCartDisplay = () => {
    const cart = getCart();
    const cartButton = document.querySelector('#cart-button');
    const totalElement = document.querySelector('#cart-total');
    
    if (cartButton) {
        cartButton.style.display = cart.items.length ? 'block' : 'none';
    }
    
    if (totalElement && cart.total) {
        totalElement.textContent = `${cart.total.toFixed(2)} €`;
    }
};

// Handle page navigation
const validateStep = (nextPage) => {
    const cart = getCart();
    cart.step = nextPage;
    saveCart(cart);
    window.location.href = `${nextPage}.php`;
};

// Generate invoice
const generateInvoice = () => {
    const cart = getCart();
    let invoiceHTML = `
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-[#D84315]">Facture</h1>
                <p class="text-gray-600">Date: ${new Date().toLocaleDateString()}</p>
            </div>
            <div class="space-y-4">
    `;

    // Add items with ingredients
    cart.items.forEach(item => {
        invoiceHTML += `
            <div class="flex justify-between items-start border-b pb-2">
                <div>
                    <h3 class="font-medium">${item.name}</h3>
                    ${item.addedIngredients.length > 0 ? `
                        <p class="text-sm text-gray-600">
                            Ingrédients: ${item.addedIngredients.map(ing => ing.name).join(', ')}
                        </p>
                    ` : ''}
                    ${item.removedIngredients.length > 0 ? `
                        <p class="text-sm text-red-500">
                            Retirés: ${item.removedIngredients.map(ing => ing.name).join(', ')}
                        </p>
                    ` : ''}
                </div>
                <span class="font-bold">${item.price.toFixed(2)} €</span>
            </div>
        `;
    });

    // Add extras
    if (cart.entrees.length) {
        invoiceHTML += `<div class="mt-4"><h3 class="font-medium">Entrées</h3>`;
        cart.entrees.forEach(entree => {
            invoiceHTML += `
                <div class="flex justify-between items-center">
                    <span>${entree.name}</span>
                    <span>${entree.price.toFixed(2)} €</span>
                </div>
            `;
        });
        invoiceHTML += `</div>`;
    }

    if (cart.boissons.length) {
        invoiceHTML += `<div class="mt-4"><h3 class="font-medium">Boissons</h3>`;
        cart.boissons.forEach(boisson => {
            invoiceHTML += `
                <div class="flex justify-between items-center">
                    <span>${boisson.name}</span>
                    <span>${boisson.price.toFixed(2)} €</span>
                </div>
            `;
        });
        invoiceHTML += `</div>`;
    }

    // Add total and details
    invoiceHTML += `
            <div class="mt-8 pt-4 border-t">
                <div class="flex justify-between items-center font-bold text-xl">
                    <span>Total</span>
                    <span>${cart.total.toFixed(2)} €</span>
                </div>
                <div class="mt-4 text-gray-600">
                    <p>Type de commande: ${cart.orderType === 'SUR_PLACE' ? 'Sur place' : 'À emporter'}</p>
                    <p>Méthode de paiement: ${cart.paymentMethod === 'CB' ? 'Carte bancaire' : 'Espèces'}</p>
                </div>
            </div>
        </div>
    </div>
    `;

    // Create modal with invoice
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4';
    modal.innerHTML = invoiceHTML;

    // Add download button
    const downloadBtn = document.createElement('button');
    downloadBtn.className = 'mt-4 px-6 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors';
    downloadBtn.textContent = 'Télécharger la facture';
    downloadBtn.onclick = () => {
        const invoiceContent = modal.innerHTML;
        const blob = new Blob([invoiceContent], { type: 'text/html' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'facture.html';
        a.click();
        window.URL.revokeObjectURL(url);
    };

    modal.querySelector('.bg-white').appendChild(downloadBtn);
    document.body.appendChild(modal);

    // Clear cart after generating invoice
    localStorage.removeItem(STORAGE_KEY);
};

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', initCart);
