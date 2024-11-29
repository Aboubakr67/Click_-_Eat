<?php
require_once('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE CUISINE') {
    header("Location: connexion.php");
    exit;
}

require_once("../Actions/zone_cuisine_repo.php");

if (isset($_GET['commande_id'])) {
    $commande_id = $_GET['commande_id'];
    $commandeDetails = getCommandeDetails($commande_id);
} else {
    echo "Commande non spécifiée.";
    exit;
}
?>

<div class="flex">
    <!-- Sidebar -->
    <div class="w-[200px] h-screen bg-[#FFF1F1] fixed left-0 top-0">
        <div class="p-4">
            <img src="../Assets/images/logo_fast_food.png" alt="Click & Eat" class="w-24 mb-12">
            
            <ul class="space-y-6">
                <li>
                    <a href="zone_cuisine.php" class="text-[#D84315]">Commandes en cours</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-[calc(100%-200px)]">
        <div class="p-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Détails de la commande #<?php echo htmlspecialchars($commande_id); ?></h1>
                <a href="zone_cuisine.php" class="text-[#D84315] hover:text-[#BF360C] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour aux commandes
                </a>
            </div>

            <?php if ($commandeDetails): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modifications</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Supplément</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($commandeDetails as $detail): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($detail['plat_name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            <?php
                                            if ($detail['modifications'] && $detail['modifications'] != 'NULL') {
                                                $modifications = json_decode($detail['modifications'], true);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    $modificationText = '';
                                                    if (isset($modifications['ajouts']) && count($modifications['ajouts']) > 0) {
                                                        echo '<div class="mb-1">';
                                                        echo '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ajouts:</span> ';
                                                        echo implode(', ', $modifications['ajouts']);
                                                        echo '</div>';
                                                    }
                                                    if (isset($modifications['suppression']) && count($modifications['suppression']) > 0) {
                                                        echo '<div>';
                                                        echo '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Suppressions:</span> ';
                                                        echo implode(', ', $modifications['suppression']);
                                                        echo '</div>';
                                                    }
                                                    if (empty($modificationText)) {
                                                        echo '<span class="text-gray-400">Aucune modification</span>';
                                                    }
                                                } else {
                                                    echo '<span class="text-red-500">Erreur de format JSON</span>';
                                                }
                                            } else {
                                                echo '<span class="text-gray-400">Aucune modification</span>';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo number_format($detail['prix_supplément'], 2, ',', ' ') . ' €'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($detail['quantite']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <p>Aucun détail trouvé pour cette commande.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>
