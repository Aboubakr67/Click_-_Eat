<?php
require_once('../HeaderFooter/Client/Header.php');
require_once('../Actions/client_repo.php');

$boissons = getAllBoissons();
$formule_id = isset($_GET['formule_id']) ? intval($_GET['formule_id']) : null;
$isFormuleContext = $formule_id !== null;

if (!$isFormuleContext) {
    header('Location: boissons_supplementaires.php');
    exit;
}
?>

<div class="p-8 pb-32">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8">
        <h1 class="text-2xl font-bold">Choisissez votre boisson</h1>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="grid grid-cols-2 gap-x-12 gap-y-8" id="boissons-grid">
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
                        <h2 class="text-lg font-medium mb-3"><?php echo htmlspecialchars($boisson['nom']); ?></h2>
                        <div class="flex flex-col items-center gap-2">
                            <button onclick="toggleBoissonUI('<?php echo $boissonId; ?>', '<?php echo htmlspecialchars($boisson['nom']); ?>')"
                                class="px-6 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors toggle-btn">
                                Choisir
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <button onclick="window.location.href='entrees.php?formule_id=<?php echo $formule_id; ?>'"
                class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
            <button onclick="window.location.href='panier.php'"
                class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                Continuer
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
        if (cart.items && cart.items.length > 0) {
            const currentItem = cart.items[cart.items.length - 1];
            console.log('Current item:', currentItem);
            console.log('Current boissons:', currentItem.boissons);
        }
    }

    // Function to gray out all boissons except the selected one
    function updateBoissonsGrid(selectedId = null) {
        const grid = document.getElementById('boissons-grid');
        const items = grid.children;

        for (let item of items) {
            if (selectedId === null) {
                // No selection - remove gray out from all
                item.classList.remove('opacity-50', 'pointer-events-none');
            } else if (item.id !== `boisson-${selectedId}`) {
                // Gray out non-selected items
                item.classList.add('opacity-50', 'pointer-events-none');
            }
        }
    }

    // Update boisson states based on cart
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, checking cart state');
        logCartState();

        const cart = getCart();
        const currentItem = cart.items[cart.items.length - 1];

        if (currentItem && currentItem.boissons && currentItem.boissons.length > 0) {
            const selectedBoisson = currentItem.boissons[0]; // Only one boisson allowed
            const boissonDiv = document.querySelector(`#boisson-${selectedBoisson.id}`);
            if (boissonDiv) {
                updateBoissonUI(boissonDiv, true);
                updateBoissonsGrid(selectedBoisson.id);
            }
        }
    });

    // Function to update boisson UI
    function updateBoissonUI(boissonDiv, isAdded) {
        console.log('Updating UI:', boissonDiv.id, 'to added state:', isAdded);
        const addedIndicator = boissonDiv.querySelector('.added-indicator');
        const toggleBtn = boissonDiv.querySelector('.toggle-btn');

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
            toggleBtn.textContent = 'Choisir';
            toggleBtn.classList.remove('bg-[#D84315]', 'text-white');
            toggleBtn.classList.add('border-[#D84315]', 'text-[#D84315]');
        }
    }

    // Function to toggle boisson with UI update
    function toggleBoissonUI(id, name) {
        console.log('Toggling UI for boisson:', id, name);
        const cart = getCart();
        const currentItem = cart.items[cart.items.length - 1];

        if (!currentItem.boissons) currentItem.boissons = [];
        const isAdded = currentItem.boissons.some(boisson => boisson.id === id);

        if (!isAdded) {
            // Remove any existing boisson first (only one allowed)
            currentItem.boissons = [{
                id,
                name,
                quantity: 1,
                price: 0 // Price is 0 since it's included in formule
            }];
            updateBoissonsGrid(id);
        } else {
            // Remove the boisson
            currentItem.boissons = [];
            updateBoissonsGrid(null);
        }

        saveCart(cart);

        // Update UI for clicked boisson
        const boissonDiv = document.querySelector(`#boisson-${id}`);
        if (boissonDiv) {
            updateBoissonUI(boissonDiv, !isAdded);
        }

        logCartState();
    }
</script>

<?php
require_once('../HeaderFooter/Client/Footer.php');
?>
