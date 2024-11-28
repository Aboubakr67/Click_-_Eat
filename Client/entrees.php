<?php
require('../HeaderFooter/Client/Header.php');
require('../Actions/client_repo.php');

// Récupérer le formule_id depuis l'URL
$formule_id = isset($_GET['formule_id']) ? intval($_GET['formule_id']) : null;

if (!$formule_id) {
    // Rediriger vers la page des formules si pas d'ID
    header('Location: formules.php');
    exit;
}

$entrees = getAllAccompagnementFromFormule();
?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Entrées</h1>
        <span class="text-2xl font-bold">10,75 €</span>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4">
        <div class="space-y-12">
            <?php foreach ($entrees as $entree): ?>
                <div class="flex flex-col items-center">
                    <img src="../Assets/images/<?php echo strtolower($entree['nom']); ?>_menu.png" 
                         alt="<?php echo htmlspecialchars($entree['nom']); ?>"
                         class="w-48 h-48 object-contain mb-2">
                    <h2 class="text-lg font-medium mb-1"><?php echo htmlspecialchars($entree['nom']); ?></h2>
                    <p class="text-sm text-gray-600 mb-3">1,00 €</p>
                    <div class="flex gap-2">
                        <button class="px-6 py-2 border border-[#D84315] text-[#D84315] rounded hover:bg-[#D84315] hover:text-white transition-colors">
                            Retirer
                        </button>
                        <button class="px-6 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                            Ajouter
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-center mt-12">
            <button onclick="window.location.href='choix_ingredients.php?formule_id=<?php echo $formule_id; ?>'" 
                    class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
            <button onclick="window.location.href='edit_boissons.php?formule_id=<?php echo $formule_id; ?>'" 
                    class="px-12 py-3 bg-[#D84315] text-white rounded-lg text-lg font-medium hover:bg-[#BF360C] transition-colors">
                Continuer
            </button>
        </div>
    </div>

    <div class="flex justify-center">
        <button class="px-12 py-3 bg-white text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
            Voir mon panier
        </button>
    </div>
</div>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
