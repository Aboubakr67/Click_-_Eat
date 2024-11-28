<?php
require('../HeaderFooter/Client/Header.php');
require("../Actions/client_repo.php");

if (!isset($_GET['formule_id'])) {
    echo "Formule non spécifiée.";
    exit;
}
$formule_id = intval($_GET['formule_id']);

// Charger le plat et ses ingrédients
$plat = getPlatFromFormule($formule_id);
$ingredients = $plat ? getIngredientsFromPlat($plat['id']) : [];
$formules = getAllFormules();
// Récupérer les suppléments disponibles
$supplements = getSupplementIngredients();

function getFormuleById($formules, $formule_id) {
    foreach ($formules as $formule) {
        if ($formule['id'] === $formule_id) {
            return $formule;
        }
    }
    return null;
}

$formuleActive = getFormuleById($formules, $formule_id);

?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <?php if ($plat): ?>
            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($formuleActive['nom']); ?></h1>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="grid grid-cols-2 gap-8">
            <?php if (!empty($ingredients)): ?>
                <?php foreach ($ingredients as $ingredient): ?>
                    <?php $ingredientId = htmlspecialchars($ingredient['ingredient_nom']); ?>
                    <div class="flex flex-col items-center transition-all duration-200 p-4 rounded-xl" id="ingredient-<?php echo $ingredientId; ?>">
                        <div class="relative ingredient-image-container">
                            <!-- Red diagonal line for removed state -->
                            <div class="absolute inset-0 hidden removed-overlay">
                                <div class="absolute inset-0 bg-gray-200 opacity-50"></div>
                                <div class="absolute top-1/2 left-1/2 w-[141%] h-0.5 bg-red-500 -translate-x-1/2 -translate-y-1/2 rotate-45"></div>
                            </div>
                            <img src="../Assets/images/<?php echo strtolower($ingredient['ingredient_nom']); ?>_menu.png" 
                                 alt="<?php echo $ingredientId; ?>" 
                                 class="w-32 h-32 object-contain mb-2">
                        </div>
                        <div class="text-center">
                            <h3 class="font-medium mb-1"><?php echo $ingredientId; ?></h3>
                            <p class="text-sm text-gray-600 mb-2">Disponible : <?php echo $ingredient['ingredient_quantite']; ?></p>
                            <div class="flex justify-center">
                                <button onclick="toggleIngredientUI('<?php echo $ingredientId; ?>', '<?php echo $ingredientId; ?>')"
                                        class="px-4 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors toggle-btn">
                                    Retirer
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-span-2 text-center text-gray-500">Aucun ingrédient disponible pour ce plat.</p>
            <?php endif; ?>
        </div>

        <div class="flex justify-center gap-4 mt-8">
            <button onclick="window.location.href='formules.php'" 
                    class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
            <button onclick="validateIngredientsAndContinue(<?php echo htmlspecialchars(json_encode($ingredients)); ?>, <?php echo $formule_id; ?>)" 
                    class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                Continuer
            </button>
        </div>
    </div>

    <!-- Include mini cart -->
    <?php require('panier-mini.php'); ?>
</div>

<script>
// Debug function to log cart state
function logCartState() {
    const cart = getCart();
    console.log('Current cart state:', cart);
    if (cart.items && cart.items.length > 0) {
        console.log('Current item:', cart.items[cart.items.length - 1]);
    }
}

// Update ingredient states based on cart
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, checking cart state');
    logCartState();
    
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    if (currentItem && currentItem.removedIngredients) {
        console.log('Found removed ingredients:', currentItem.removedIngredients);
        currentItem.removedIngredients.forEach(ing => {
            const ingredientDiv = document.querySelector(`#ingredient-${ing.id}`);
            console.log('Looking for ingredient:', ing.id, ingredientDiv);
            if (ingredientDiv) {
                updateIngredientUI(ingredientDiv, true);
            }
        });
    }
});

// Function to update ingredient UI
function updateIngredientUI(ingredientDiv, isRemoved) {
    console.log('Updating UI:', ingredientDiv.id, 'to removed state:', isRemoved);
    const overlay = ingredientDiv.querySelector('.removed-overlay');
    const toggleBtn = ingredientDiv.querySelector('.toggle-btn');
    
    if (isRemoved) {
        // Show removed state
        overlay.classList.remove('hidden');
        toggleBtn.textContent = 'Ajouter';
        toggleBtn.classList.add('bg-[#D84315]', 'text-white');
        toggleBtn.classList.remove('border-[#D84315]', 'text-[#D84315]');
    } else {
        // Show normal state
        overlay.classList.add('hidden');
        toggleBtn.textContent = 'Retirer';
        toggleBtn.classList.remove('bg-[#D84315]', 'text-white');
        toggleBtn.classList.add('border-[#D84315]', 'text-[#D84315]');
    }
}

// Function to toggle ingredient with UI update
function toggleIngredientUI(id, name) {
    console.log('Toggling UI for ingredient:', id, name);
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    // Initialize removedIngredients if it doesn't exist
    if (!currentItem.removedIngredients) {
        currentItem.removedIngredients = [];
    }
    
    // Find ingredient in removed list
    const isRemoved = currentItem.removedIngredients.some(ing => ing.id === id);
    console.log('Current removed state:', isRemoved);
    
    // Toggle ingredient in cart
    toggleIngredient(id, name);
    
    // Update UI
    const ingredientDiv = document.querySelector(`#ingredient-${id}`);
    if (ingredientDiv) {
        updateIngredientUI(ingredientDiv, !isRemoved);
    }
    
    // Log updated state
    logCartState();
}

// Function to validate ingredients and continue
function validateIngredientsAndContinue(allIngredients, formuleId) {
    const cart = getCart();
    const currentItem = cart.items[cart.items.length - 1];
    
    // If no ingredients have been explicitly removed, add all ingredients
    if (!currentItem.removedIngredients || currentItem.removedIngredients.length === 0) {
        currentItem.addedIngredients = allIngredients.map(ing => ({
            id: ing.ingredient_nom,
            name: ing.ingredient_nom
        }));
    } else {
        // Add all ingredients that haven't been removed
        currentItem.addedIngredients = allIngredients
            .filter(ing => !currentItem.removedIngredients.some(removed => removed.id === ing.ingredient_nom))
            .map(ing => ({
                id: ing.ingredient_nom,
                name: ing.ingredient_nom
            }));
    }
    
    saveCart(cart);
    window.location.href = `entrees.php?formule_id=${formuleId}`;
}
</script>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
