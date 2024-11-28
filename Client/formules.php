<?php
require('../HeaderFooter/Client/Header.php');
require("../Actions/client_repo.php");
$formules = getAllFormules();
?>

<div class="p-8">
    <h1 class="text-4xl font-bold text-white bg-[#D84315] p-6 -mx-8 -mt-8 mb-8">Menu</h1>

    <div class="grid grid-cols-3 gap-6 mb-24">
        <?php if (!empty($formules)): ?>
            <?php foreach ($formules as $formule): ?>
                <div onclick="addMenuItem(<?php echo htmlspecialchars($formule['id']); ?>, '<?php echo htmlspecialchars($formule['nom']); ?>', <?php echo $formule['prix']; ?>, 'menu-01.png')" 
                     class="bg-white rounded-xl p-6 relative group hover:shadow-lg cursor-pointer">
                    <div class="flex items-start gap-6">
                        <div class="relative w-32 flex-shrink-0">
                            <img src="../Assets/images/menu-01.png" alt="Menu" class="w-full">
                            <div class="flex -space-x-2 absolute -bottom-1 right-0">
                                <img src="../Assets/images/frite.png" alt="Frites" class="w-6 h-6">
                                <img src="../Assets/images/boisson.png" alt="Boisson" class="w-6 h-6">
                            </div>
                        </div>
                        <div class="flex-1 pt-2">
                            <h3 class="font-medium text-lg"><?php echo htmlspecialchars($formule['nom']); ?></h3>
                            <p class="text-gray-500 text-sm mt-1">Burger super good</p>
                            <div class="flex items-center justify-between mt-4">
                                <span class="font-bold text-lg"><?php echo number_format($formule['prix'], 2, ',', ' '); ?> €</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="col-span-3 text-center text-gray-500">Aucune formule disponible pour le moment.</p>
        <?php endif; ?>
    </div>

    <div class="fixed bottom-0 left-[200px] right-0 bg-white border-t shadow-sm">
        <div class="container mx-auto px-12 py-5 flex justify-between items-center">
            <div class="flex items-baseline gap-3">
                <span class="text-gray-600">Total:</span>
                <span id="cart-total" class="font-bold text-xl">0,00 €</span>
            </div>
            <a href="panier.php">
                <button class="bg-[#D84315] text-white px-10 py-3 rounded-lg text-lg font-medium hover:bg-[#BF360C]">
                    Ma commande
                </button>
            </a>
        </div>
    </div>
</div>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
