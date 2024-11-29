<?php
require_once('../HeaderFooter/Client/Header.php');
require_once('../Actions/client_repo.php');

$desserts = getAllDesserts();
?>

<div class="p-8 pb-32">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Desserts</h1>
        <span id="cart-total" class="text-2xl font-bold">0,00 €</span>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="grid grid-cols-2 gap-x-12 gap-y-8">
            <?php foreach ($desserts as $dessert): ?>
                <?php $dessertId = htmlspecialchars($dessert['id']); ?>
                <div class="flex flex-col items-center transition-all duration-200 p-4 rounded-xl" id="dessert-<?php echo $dessertId; ?>">
                    <div class="relative">
                        <!-- Green checkmark for added state -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full items-center justify-center hidden added-indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <img src="../Assets/images/<?php echo strtolower($dessert['image']); ?>"
                            alt="<?php echo htmlspecialchars($dessert['nom']); ?>"
                            class="w-24 h-auto object-contain mb-2">
                    </div>
                    <div class="text-center">
                        <h2 class="text-lg font-medium mb-1"><?php echo htmlspecialchars($dessert['nom']); ?></h2>
                        <p class="text-sm text-gray-600 mb-3"><?php echo number_format($dessert['prix'], 2, ',', ' '); ?> €</p>
                        <div class="flex flex-col items-center gap-2">
                            <button onclick="toggleDessertUI('<?php echo $dessertId; ?>', '<?php echo htmlspecialchars($dessert['nom']); ?>', <?php echo $dessert['prix']; ?>, '<?php echo strtolower($dessert['image']); ?>')"
                                class="px-6 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors toggle-btn">
                                Ajouter
                            </button>
                            <!-- Quantity controls -->
                            <div class="flex items-center gap-2 mt-2 quantity-controls hidden">
                                <button onclick="updateDessertQuantityUI('<?php echo $dessertId; ?>', -1)"
                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-l hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="w-8 h-8 flex items-center justify-center border-t border-b border-gray-300 quantity-display">1</span>
                                <button onclick="updateDessertQuantityUI('<?php echo $dessertId; ?>', 1)"
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
            <!-- Include mini cart -->
            <?php require('panier-mini.php'); ?>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <button onclick="window.location.href='formules.php'"
                class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
            <button onclick="window.location.href='panier.php'"
                class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                Voir mon panier
            </button>
        </div>
    </div>

    <!-- Include mini cart -->
    <?php require_once('panier-mini.php'); ?>
</div>

<script>
    // Debug function to log cart state
    function logCartState() {
        const cart = getCart();
        console.log('Current cart state:', cart);
    }

    // Update dessert states based on cart
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, checking cart state');
        logCartState();

        const cart = getCart();
        if (cart.desserts) {
            cart.desserts.forEach(dessert => {
                const dessertDiv = document.querySelector(`#dessert-${dessert.id}`);
                if (dessertDiv) {
                    updateDessertUI(dessertDiv, true, dessert.quantity);
                }
            });
        }
    });

    // Function to update dessert UI
    function updateDessertUI(dessertDiv, isAdded, quantity = 1) {
        console.log('Updating UI:', dessertDiv.id, 'to added state:', isAdded, 'quantity:', quantity);
        const addedIndicator = dessertDiv.querySelector('.added-indicator');
        const toggleBtn = dessertDiv.querySelector('.toggle-btn');
        const quantityControls = dessertDiv.querySelector('.quantity-controls');
        const quantityDisplay = dessertDiv.querySelector('.quantity-display');

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
        };
    }


    // Function to update dessert UI
    function updateDessertUI(dessertDiv, isAdded, quantity = 1) {
        console.log('Updating UI:', dessertDiv.id, 'to added state:', isAdded, 'quantity:', quantity);
        const addedIndicator = dessertDiv.querySelector('.added-indicator');
        const toggleBtn = dessertDiv.querySelector('.toggle-btn');
        const quantityControls = dessertDiv.querySelector('.quantity-controls');
        const quantityDisplay = dessertDiv.querySelector('.quantity-display');

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

    // Function to toggle dessert with UI update
    function toggleDessertUI(id, name, price, image) {
        console.log('Toggling UI for dessert:', id, name, price, image);
        const cart = getCart();

        if (!cart.desserts) cart.desserts = [];
        const isAdded = cart.desserts.some(dessert => dessert.id === id);

        if (!isAdded) {
            cart.desserts.push({
                id,
                name,
                price: parseFloat(price),
                quantity: 1,
                image
            });
        } else {
            cart.desserts = cart.desserts.filter(dessert => dessert.id !== id);
        }

        saveCart(cart);
        updateTotal();
        updateDessertUI(document.querySelector(`#dessert-${id}`), !isAdded);

        logCartState();
    }

    // Function to update dessert quantity with UI update
    function updateDessertQuantityUI(id, delta) {
        console.log('Updating quantity for dessert:', id, 'delta:', delta);
        const cart = getCart();
        const dessert = cart.desserts.find(d => d.id === id);

        if (dessert) {
            const newQuantity = dessert.quantity + delta;
            if (newQuantity > 0) {
                dessert.quantity = newQuantity;
                const dessertDiv = document.querySelector(`#dessert-${id}`);
                const quantityDisplay = dessertDiv.querySelector('.quantity-display');
                quantityDisplay.textContent = newQuantity;
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
    }

    // Function to toggle dessert with UI update
    function toggleDessertUI(id, name, price) {
        console.log('Toggling UI for dessert:', id, name, price);
        const cart = getCart();

        if (!cart.desserts) cart.desserts = [];
        const isAdded = cart.desserts.some(dessert => dessert.id === id);

        if (!isAdded) {
            cart.desserts.push({
                id,
                name,
                price: parseFloat(price),
                quantity: 1
            });
        } else {
            cart.desserts = cart.desserts.filter(dessert => dessert.id !== id);
        }

        saveCart(cart);
        updateTotal();
        updateDessertUI(document.querySelector(`#dessert-${id}`), !isAdded);

        logCartState();
    }

    // Function to update dessert quantity with UI update
    function updateDessertQuantityUI(id, delta) {
        console.log('Updating quantity for dessert:', id, 'delta:', delta);
        const cart = getCart();
        const dessert = cart.desserts.find(d => d.id === id);

        if (dessert) {
            const newQuantity = dessert.quantity + delta;
            if (newQuantity > 0) {
                dessert.quantity = newQuantity;
                const dessertDiv = document.querySelector(`#dessert-${id}`);
                const quantityDisplay = dessertDiv.querySelector('.quantity-display');
                quantityDisplay.textContent = newQuantity;
            } else {
                cart.desserts = cart.desserts.filter(d => d.id !== id);
                updateDessertUI(document.querySelector(`#dessert-${id}`), false);
            }
            saveCart(cart);
            updateTotal();
        }

        logCartState();
    }
</script>

<?php
require_once('../HeaderFooter/Client/Footer.php');
?>