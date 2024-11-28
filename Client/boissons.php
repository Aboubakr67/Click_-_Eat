<?php
require('../HeaderFooter/Client/Header.php');
require('../Actions/client_repo.php');

$boissons = getAllBoissons();
$formule_id = isset($_GET['formule_id']) ? intval($_GET['formule_id']) : null;
$isFormuleContext = $formule_id !== null;
?>

<div class="p-8 pb-32">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Boissons</h1>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="grid grid-cols-2 gap-x-12 gap-y-8">
            <?php foreach ($boissons as $boisson): ?>
                <?php $boissonId = htmlspecialchars($boisson['id']); ?>
                <div class="flex flex-col items-center transition-all duration-200 p-4 rounded-xl" id="boisson-<?php echo $boissonId; ?>">
                    <div class="relative">
                        <!-- Green checkmark for added state -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full items-center justify-center hidden added-indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <img src="../Assets/images/Boissons/<?php echo strtolower($boisson['image']); ?>" 
                             alt="<?php echo htmlspecialchars($boisson['nom']); ?>"
                             class="w-24 h-auto object-contain mb-2">
                    </div>
                    <div class="text-center">
                        <h2 class="text-lg font-medium mb-1"><?php echo htmlspecialchars($boisson['nom']); ?></h2>
                        <p class="text-sm text-gray-600 mb-3"><?php echo number_format($boisson['prix'], 2, ',', ' '); ?> €</p>
                        <div class="flex flex-col items-center gap-2">
                            <button onclick="toggleBoissonUI('<?php echo $boissonId; ?>', '<?php echo htmlspecialchars($boisson['nom']); ?>', <?php echo $boisson['prix']; ?>)"
                                    class="px-6 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors toggle-btn">
                                Ajouter
                            </button>
                            <!-- Quantity controls -->
                            <div class="flex items-center gap-2 mt-2 quantity-controls hidden">
                                <button onclick="updateBoissonQuantityUI('<?php echo $boissonId; ?>', -1)"
                                        class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-l hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="w-8 h-8 flex items-center justify-center border-t border-b border-gray-300 quantity-display">1</span>
                                <button onclick="updateBoissonQuantityUI('<?php echo $boissonId; ?>', 1)"
                                        class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-r hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <?php if ($isFormuleContext): ?>
                <button onclick="window.location.href='entrees.php?formule_id=<?php echo $formule_id; ?>'" 
                        class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                    Retour
                </button>
                <button onclick="window.location.href='panier.php'" 
                        class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                    Continuer
                </button>
            <?php else: ?>
                <button onclick="window.location.href='formules.php'" 
                        class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                    Retour
                </button>
                <button onclick="window.location.href='panier.php'" 
                        class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                    Continuer
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include mini cart -->
    <?php require('panier-mini.php'); ?>
</div>

<script>
const isFormuleContext = <?php echo $isFormuleContext ? 'true' : 'false'; ?>;

// Debug function to log cart state
function logCartState() {
    const cart = getCart();
    console.log('Current cart state:', cart);
    if (cart.items && cart.items.length > 0) {
        const currentItem = cart.items[cart.items.length - 1];
        console.log('Current item:', currentItem);
        console.log('Current boissons:', currentItem.boissons);
    }
}

// Update boisson states based on cart
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, checking cart state');
    logCartState();
    
    const cart = getCart();
    
    if (isFormuleContext) {
        // In formule context, show boissons for current formule
        const currentItem = cart.items[cart.items.length - 1];
        if (currentItem && currentItem.boissons) {
            currentItem.boissons.forEach(boisson => {
                const boissonDiv = document.querySelector(`#boisson-${boisson.id}`);
                if (boissonDiv) {
                    updateBoissonUI(boissonDiv, true, boisson.quantity);
                }
            });
        }
    } else {
        // In standalone context, show all boissons from cart
        if (cart.boissons) {
            cart.boissons.forEach(boisson => {
                const boissonDiv = document.querySelector(`#boisson-${boisson.id}`);
                if (boissonDiv) {
                    updateBoissonUI(boissonDiv, true, boisson.quantity);
                }
            });
        }
    }
});

// Function to update boisson UI
function updateBoissonUI(boissonDiv, isAdded, quantity = 1) {
    console.log('Updating UI:', boissonDiv.id, 'to added state:', isAdded, 'quantity:', quantity);
    const addedIndicator = boissonDiv.querySelector('.added-indicator');
    const toggleBtn = boissonDiv.querySelector('.toggle-btn');
    const quantityControls = boissonDiv.querySelector('.quantity-controls');
    const quantityDisplay = boissonDiv.querySelector('.quantity-display');
    
    if (isAdded) {
        // Show added state
        addedIndicator.classList.remove('hidden');
        addedIndicator.classList.add('flex');
        toggleBtn.textContent = 'Retirer';
        toggleBtn.classList.add('bg-[#D84315]', 'text-white');
        toggleBtn.classList.remove('border-[#D84315]', 'text-[#D84315]');
        quantityControls.classList.remove('hidden');
        if (quantityDisplay) {
            quantityDisplay.textContent = quantity;
        }
    } else {
        // Show not added state
        addedIndicator.classList.add('hidden');
        addedIndicator.classList.remove('flex');
        toggleBtn.textContent = 'Ajouter';
        toggleBtn.classList.remove('bg-[#D84315]', 'text-white');
        toggleBtn.classList.add('border-[#D84315]', 'text-[#D84315]');
        quantityControls.classList.add('hidden');
    }
}

// Function to toggle boisson with UI update
function toggleBoissonUI(id, name, price) {
    console.log('Toggling UI for boisson:', id, name, price);
    const cart = getCart();
    
    if (isFormuleContext) {
        // Add to current formule
        const currentItem = cart.items[cart.items.length - 1];
        if (!currentItem.boissons) currentItem.boissons = [];
        const isAdded = currentItem.boissons.some(boisson => boisson.id === id);
        toggleBoisson(id, name, price);
        updateBoissonUI(document.querySelector(`#boisson-${id}`), !isAdded);
    } else {
        // Add to standalone cart
        if (!cart.boissons) cart.boissons = [];
        const isAdded = cart.boissons.some(boisson => boisson.id === id);
        if (!isAdded) {
            cart.boissons.push({
                id,
                name,
                price: parseFloat(price),
                quantity: 1
            });
        } else {
            cart.boissons = cart.boissons.filter(boisson => boisson.id !== id);
        }
        saveCart(cart);
        updateTotal();
        updateBoissonUI(document.querySelector(`#boisson-${id}`), !isAdded);
    }
    
    logCartState();
}

// Function to update boisson quantity with UI update
function updateBoissonQuantityUI(id, delta) {
    console.log('Updating quantity for boisson:', id, 'delta:', delta);
    const cart = getCart();
    
    if (isFormuleContext) {
        // Update quantity in current formule
        const currentItem = cart.items[cart.items.length - 1];
        const boisson = currentItem.boissons.find(b => b.id === id);
        if (boisson) {
            updateBoissonQuantity(id, delta);
            const boissonDiv = document.querySelector(`#boisson-${id}`);
            const quantityDisplay = boissonDiv.querySelector('.quantity-display');
            const newQuantity = boisson.quantity + delta;
            if (newQuantity > 0) {
                quantityDisplay.textContent = newQuantity;
            } else {
                updateBoissonUI(boissonDiv, false);
            }
        }
    } else {
        // Update quantity in standalone cart
        const boisson = cart.boissons.find(b => b.id === id);
        if (boisson) {
            const newQuantity = boisson.quantity + delta;
            if (newQuantity > 0) {
                boisson.quantity = newQuantity;
                const boissonDiv = document.querySelector(`#boisson-${id}`);
                const quantityDisplay = boissonDiv.querySelector('.quantity-display');
                quantityDisplay.textContent = newQuantity;
            } else {
                cart.boissons = cart.boissons.filter(b => b.id !== id);
                updateBoissonUI(document.querySelector(`#boisson-${id}`), false);
            }
            saveCart(cart);
            updateTotal();
        }
    }
    
    logCartState();
}
</script>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
