<?php
require('../HeaderFooter/Client/Header.php');
require('../Actions/client_repo.php');

$entrees = getAllAccompagnementFromFormule();
?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Ajouter des entrées ?</h1>
        <span id="cart-total" class="text-2xl font-bold">0,00 €</span>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="space-y-12">
            <?php foreach ($entrees as $entree): ?>
                <div class="flex flex-col items-center transition-all duration-200 p-4 rounded-xl" id="entree-<?php echo $entree['id']; ?>">
                    <div class="relative">
                        <!-- Red diagonal line for removed state -->
                        <div class="absolute inset-0 hidden removed-overlay">
                            <div class="absolute inset-0 bg-gray-200 opacity-50"></div>
                            <div class="absolute top-1/2 left-1/2 w-[141%] h-0.5 bg-red-500 -translate-x-1/2 -translate-y-1/2 rotate-45"></div>
                        </div>
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
                    <div class="text-center">
                        <h2 class="text-lg font-medium mb-1"><?php echo htmlspecialchars($entree['nom']); ?></h2>
                        <p class="text-sm text-gray-600 mb-3">1,00 €</p>
                        <div class="flex gap-2">
                            <button onclick="toggleEntreeUI('<?php echo $entree['id']; ?>', '<?php echo htmlspecialchars($entree['nom']); ?>', 1)"
                                    class="px-6 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors toggle-btn">
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <button onclick="window.location.href='choix_ingredients.php?formule_id=<?php echo $formule_id; ?>'" 
                    class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
            <button onclick="validateStep('boissons')" 
                    class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                Continuer
            </button>
        </div>
    </div>

    <div class="flex justify-center">
        <a href="panier.php">
            <button class="px-12 py-3 bg-white text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Voir mon panier
            </button>
        </a>
    </div>
</div>

<script>
// Update entrée states based on cart
document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    
    if (cart.entrees) {
        cart.entrees.forEach(entree => {
            const entreeDiv = document.querySelector(`#entree-${entree.id}`);
            if (entreeDiv) {
                updateEntreeUI(entreeDiv, true);
            }
        });
    }
});

// Function to update entrée UI
function updateEntreeUI(entreeDiv, isAdded) {
    const addedIndicator = entreeDiv.querySelector('.added-indicator');
    const toggleBtn = entreeDiv.querySelector('.toggle-btn');
    
    if (isAdded) {
        // Show added state
        addedIndicator.classList.remove('hidden');
        addedIndicator.classList.add('flex');
        toggleBtn.textContent = 'Retirer';
        toggleBtn.classList.add('bg-[#D84315]', 'text-white');
        toggleBtn.classList.remove('border-[#D84315]', 'text-[#D84315]');
    } else {
        // Show not added state
        addedIndicator.classList.add('hidden');
        addedIndicator.classList.remove('flex');
        toggleBtn.textContent = 'Ajouter';
        toggleBtn.classList.remove('bg-[#D84315]', 'text-white');
        toggleBtn.classList.add('border-[#D84315]', 'text-[#D84315]');
    }
}

// Function to toggle entrée with UI update
function toggleEntreeUI(id, name, price) {
    const cart = getCart();
    const isAdded = cart.entrees && cart.entrees.some(entree => entree.id === id);
    
    // Toggle entrée in cart
    toggleEntree(id, name, price, isAdded ? 'remove' : 'add');
    
    // Update UI
    const entreeDiv = document.querySelector(`#entree-${id}`);
    if (entreeDiv) {
        updateEntreeUI(entreeDiv, !isAdded);
    }
}
</script>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
