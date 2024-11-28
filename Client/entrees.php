<?php
require('../HeaderFooter/Client/Header.php');
require('../Actions/client_repo.php');

$entrees = getAllAccompagnementFromFormule();
$formule_id = isset($_GET['formule_id']) ? intval($_GET['formule_id']) : null;
$isFormuleContext = $formule_id !== null;
?>

<div class="p-8 pb-32">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Entrées</h1>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="space-y-12">
            <?php foreach ($entrees as $entree): ?>
                <?php $entreeId = htmlspecialchars($entree['id']); ?>
                <div class="flex flex-col items-center" id="entree-<?php echo $entreeId; ?>">
                    <div class="relative">
                        <!-- Green checkmark for added state -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full items-center justify-center hidden added-indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <img src="../Assets/images/<?php echo strtolower($entree['nom']); ?>_menu.png" 
                             alt="<?php echo htmlspecialchars($entree['nom']); ?>"
                             class="w-48 h-48 object-contain mb-2">
                    </div>
                    <h2 class="text-lg font-medium mb-1"><?php echo htmlspecialchars($entree['nom']); ?></h2>
                    <p class="text-sm text-gray-600 mb-3">1,00 €</p>
                    <div class="flex flex-col items-center gap-2">
                        <button onclick="toggleEntreeUI('<?php echo $entreeId; ?>', '<?php echo htmlspecialchars($entree['nom']); ?>', 1)"
                                class="px-6 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors toggle-btn">
                            Ajouter
                        </button>
                        <!-- Quantity controls -->
                        <div class="flex items-center gap-2 mt-2 quantity-controls hidden">
                            <button onclick="updateEntreeQuantityUI('<?php echo $entreeId; ?>', -1)"
                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-l hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="w-8 h-8 flex items-center justify-center border-t border-b border-gray-300 quantity-display">1</span>
                            <button onclick="updateEntreeQuantityUI('<?php echo $entreeId; ?>', 1)"
                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-r hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <?php if ($isFormuleContext): ?>
                <button onclick="window.location.href='choix_ingredients.php?formule_id=<?php echo $formule_id; ?>'" 
                        class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                    Retour
                </button>
                <button onclick="window.location.href='boissons.php?formule_id=<?php echo $formule_id; ?>'" 
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
        console.log('Current entrées:', currentItem.entrees);
    }
}

// Update entrée states based on cart
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, checking cart state');
    logCartState();
    
    const cart = getCart();
    
    if (isFormuleContext) {
        // In formule context, show entrées for current formule
        const currentItem = cart.items[cart.items.length - 1];
        if (currentItem && currentItem.entrees) {
            currentItem.entrees.forEach(entree => {
                const entreeDiv = document.querySelector(`#entree-${entree.id}`);
                if (entreeDiv) {
                    updateEntreeUI(entreeDiv, true, entree.quantity);
                }
            });
        }
    } else {
        // In standalone context, show all entrées from cart
        if (cart.entrees) {
            cart.entrees.forEach(entree => {
                const entreeDiv = document.querySelector(`#entree-${entree.id}`);
                if (entreeDiv) {
                    updateEntreeUI(entreeDiv, true, entree.quantity);
                }
            });
        }
    }
});

// Function to update entrée UI
function updateEntreeUI(entreeDiv, isAdded, quantity = 1) {
    console.log('Updating UI:', entreeDiv.id, 'to added state:', isAdded, 'quantity:', quantity);
    const addedIndicator = entreeDiv.querySelector('.added-indicator');
    const toggleBtn = entreeDiv.querySelector('.toggle-btn');
    const quantityControls = entreeDiv.querySelector('.quantity-controls');
    const quantityDisplay = entreeDiv.querySelector('.quantity-display');
    
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

// Function to toggle entrée with UI update
function toggleEntreeUI(id, name, price) {
    console.log('Toggling UI for entrée:', id, name, price);
    const cart = getCart();
    
    if (isFormuleContext) {
        // Add to current formule
        const currentItem = cart.items[cart.items.length - 1];
        if (!currentItem.entrees) currentItem.entrees = [];
        const isAdded = currentItem.entrees.some(entree => entree.id === id);
        toggleEntree(id, name, price);
        updateEntreeUI(document.querySelector(`#entree-${id}`), !isAdded);
    } else {
        // Add to standalone cart
        if (!cart.entrees) cart.entrees = [];
        const isAdded = cart.entrees.some(entree => entree.id === id);
        if (!isAdded) {
            cart.entrees.push({
                id,
                name,
                price: parseFloat(price),
                quantity: 1
            });
        } else {
            cart.entrees = cart.entrees.filter(entree => entree.id !== id);
        }
        saveCart(cart);
        updateTotal();
        updateEntreeUI(document.querySelector(`#entree-${id}`), !isAdded);
    }
    
    logCartState();
}

// Function to update entrée quantity with UI update
function updateEntreeQuantityUI(id, delta) {
    console.log('Updating quantity for entrée:', id, 'delta:', delta);
    const cart = getCart();
    
    if (isFormuleContext) {
        // Update quantity in current formule
        const currentItem = cart.items[cart.items.length - 1];
        const entree = currentItem.entrees.find(e => e.id === id);
        if (entree) {
            updateEntreeQuantity(id, delta);
            const entreeDiv = document.querySelector(`#entree-${id}`);
            const quantityDisplay = entreeDiv.querySelector('.quantity-display');
            const newQuantity = entree.quantity + delta;
            if (newQuantity > 0) {
                quantityDisplay.textContent = newQuantity;
            } else {
                updateEntreeUI(entreeDiv, false);
            }
        }
    } else {
        // Update quantity in standalone cart
        const entree = cart.entrees.find(e => e.id === id);
        if (entree) {
            const newQuantity = entree.quantity + delta;
            if (newQuantity > 0) {
                entree.quantity = newQuantity;
                const entreeDiv = document.querySelector(`#entree-${id}`);
                const quantityDisplay = entreeDiv.querySelector('.quantity-display');
                quantityDisplay.textContent = newQuantity;
            } else {
                cart.entrees = cart.entrees.filter(e => e.id !== id);
                updateEntreeUI(document.querySelector(`#entree-${id}`), false);
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
