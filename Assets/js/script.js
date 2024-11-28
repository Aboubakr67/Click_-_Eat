// Cart structure in localStorage
const STORAGE_KEY = 'fastfood_cart';

// Initialize cart structure
const initCart = () => {
    if (!localStorage.getItem(STORAGE_KEY)) {
        const cart = {
            items: [], // Array of menu items
            entrees: [], // Standalone entrées
            boissons: [], // Standalone boissons
            desserts: [], // Standalone desserts
            total: 0,
            step: 'menu'
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
    const cart = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    if (!cart.items) cart.items = [];
    if (!cart.entrees) cart.entrees = [];
    if (!cart.boissons) cart.boissons = [];
    if (!cart.desserts) cart.desserts = [];
    return cart;
};

// Calculate item total
const calculateItemTotal = (item) => {
    let total = parseFloat(item.price || 0);

    // Add entrées total
    if (item.entrees && item.entrees.length > 0) {
        total += item.entrees.reduce((sum, entree) => {
            return sum + (parseFloat(entree.price || 0) * (entree.quantity || 1));
        }, 0);
    }

    // Add boissons total
    if (item.boissons && item.boissons.length > 0) {
        total += item.boissons.reduce((sum, boisson) => {
            return sum + (parseFloat(boisson.price || 0) * (boisson.quantity || 1));
        }, 0);
    }

    return total;
};

// Calculate standalone items total
const calculateStandaloneTotal = (cart) => {
    let total = 0;

    // Add standalone entrées total
    if (cart.entrees && cart.entrees.length > 0) {
        total += cart.entrees.reduce((sum, entree) => {
            return sum + (parseFloat(entree.price || 0) * (entree.quantity || 1));
        }, 0);
    }

    // Add standalone boissons total
    if (cart.boissons && cart.boissons.length > 0) {
        total += cart.boissons.reduce((sum, boisson) => {
            return sum + (parseFloat(boisson.price || 0) * (boisson.quantity || 1));
        }, 0);
    }

    // Add standalone desserts total
    if (cart.desserts && cart.desserts.length > 0) {
        total += cart.desserts.reduce((sum, dessert) => {
            return sum + (parseFloat(dessert.price || 0) * (dessert.quantity || 1));
        }, 0);
    }

    return total;
};

// Calculate cart total
const calculateCartTotal = (cart) => {
    // Calculate total for menu items
    const menuTotal = cart.items.reduce((total, item) => {
        return total + calculateItemTotal(item);
    }, 0);

    // Add standalone items total
    const standaloneTotal = calculateStandaloneTotal(cart);

    return menuTotal + standaloneTotal;
};

// Update total price
const updateTotal = () => {
    const cart = getCart();
    const total = calculateCartTotal(cart);
    cart.total = total;
    saveCart(cart);
    
    // Update all total displays on the page
    const totalElements = document.querySelectorAll('#cart-total');
    totalElements.forEach(element => {
        element.textContent = `${total.toFixed(2)} €`;
    });

    console.log('Updated total:', total, 'Cart:', cart);
    return total;
};

// Update cart icon and total in header
const updateCartDisplay = () => {
    const cart = getCart();
    const cartButton = document.querySelector('#cart-button');
    const totalElements = document.querySelectorAll('#cart-total');
    
    if (cartButton) {
        const hasItems = cart.items.length > 0 || cart.entrees.length > 0 || cart.boissons.length > 0 || cart.desserts.length > 0;
        cartButton.style.display = hasItems ? 'block' : 'none';
    }
    
    totalElements.forEach(element => {
        element.textContent = `${cart.total.toFixed(2)} €`;
    });

    // Update mini cart visibility
    const miniCart = document.getElementById('mini-cart');
    if (miniCart) {
        const hasItems = cart.items.length > 0 || cart.entrees.length > 0 || cart.boissons.length > 0 || cart.desserts.length > 0;
        miniCart.style.display = hasItems ? 'block' : 'none';
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
        removedIngredients: [], // List of ingredients removed
        entrees: [], // List of entrées with quantities
        boissons: [], // List of boissons with quantities
        orderType: null,
        status: 'ingredients' // Current step in formule customization
    });
    cart.step = 'ingredients';
    saveCart(cart);
    updateTotal();
    window.location.href = `choix_ingredients.php?formule_id=${id}`;
};

// Toggle ingredient
const toggleIngredient = (id, name) => {
    console.log('Toggling ingredient:', id, name);
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    // Initialize arrays if they don't exist
    if (!currentItem.addedIngredients) currentItem.addedIngredients = [];
    if (!currentItem.removedIngredients) currentItem.removedIngredients = [];
    
    // Find ingredient in removed list
    const isRemoved = currentItem.removedIngredients.some(ing => ing.id === id);
    console.log('Is removed:', isRemoved);
    
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
    
    console.log('Updated cart:', cart);
    saveCart(cart);
    updateTotal();
    return !isRemoved;
};

// Toggle entrée with quantity
const toggleEntree = (id, name, price) => {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    if (!currentItem.entrees) currentItem.entrees = [];
    
    const existingEntree = currentItem.entrees.find(entree => entree.id === id);
    
    if (!existingEntree) {
        // Add entrée with quantity 1
        currentItem.entrees.push({
            id,
            name,
            price: parseFloat(price),
            quantity: 1
        });
    } else {
        // Remove entrée
        currentItem.entrees = currentItem.entrees.filter(entree => entree.id !== id);
    }
    
    saveCart(cart);
    updateTotal();
};

// Update entrée quantity
const updateEntreeQuantity = (id, delta) => {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    const entree = currentItem.entrees.find(e => e.id === id);
    
    if (entree) {
        const newQuantity = entree.quantity + delta;
        if (newQuantity > 0) {
            entree.quantity = newQuantity;
        } else {
            currentItem.entrees = currentItem.entrees.filter(e => e.id !== id);
        }
        saveCart(cart);
        updateTotal();
    }
};

// Toggle boisson with quantity
const toggleBoisson = (id, name, price) => {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    if (!currentItem.boissons) currentItem.boissons = [];
    
    const existingBoisson = currentItem.boissons.find(boisson => boisson.id === id);
    
    if (!existingBoisson) {
        // Add boisson with quantity 1
        currentItem.boissons.push({
            id,
            name,
            price: parseFloat(price),
            quantity: 1
        });
    } else {
        // Remove boisson
        currentItem.boissons = currentItem.boissons.filter(boisson => boisson.id !== id);
    }
    
    saveCart(cart);
    updateTotal();
};

// Update boisson quantity
const updateBoissonQuantity = (id, delta) => {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    const boisson = currentItem.boissons.find(b => b.id === id);
    
    if (boisson) {
        const newQuantity = boisson.quantity + delta;
        if (newQuantity > 0) {
            boisson.quantity = newQuantity;
        } else {
            currentItem.boissons = currentItem.boissons.filter(b => b.id !== id);
        }
        saveCart(cart);
        updateTotal();
    }
};

// Set order type
const setOrderType = (type) => {
    const cart = getCart();
    cart.items.forEach(item => {
        item.orderType = type;
    });
    cart.step = 'payment';
    saveCart(cart);
    window.location.href = 'choix_paiment.php';
};

// Handle page navigation
const validateStep = (nextPage) => {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    currentItem.status = nextPage;
    cart.step = nextPage;
    saveCart(cart);
    window.location.href = `${nextPage}.php`;
};

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initCart();
    updateCartDisplay();
});

// Debug function to clear cart
function clearCart() {
    localStorage.removeItem(STORAGE_KEY);
    initCart();
}
