<?php
require('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE CUISINE') {
    header("Location: connexion.php");
    exit;
}

require("../Actions/zone_cuisine_repo.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $commandeId = (int)$_POST['commande_id'];
    if (setCommandePrete($commandeId, $_SESSION['id'])) {
        $message = "La commande a été marquée comme prête.";
    } else {
        $error = "Erreur lors de la mise à jour de la commande.";
    }
}

$commandes = getCommandes();
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
    <div class="ml-[200px] w-[calc(100%-200px)]">
        <div class="p-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Commandes en cours</h1>
            </div>

            <?php if (isset($message)): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    <?php echo $message; ?>
                </div>
            <?php elseif (isset($error)): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (count($commandes) > 0): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code Commande</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créée il y a</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Méthode de Paiement</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Commande</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($commandes as $commande):
                                $dateCommande = new DateTime($commande['created_at']);
                                $now = new DateTime();
                                $intervalInSeconds = $now->getTimestamp() - $dateCommande->getTimestamp();
                                $hours = floor($intervalInSeconds / 3600);
                                $minutes = floor(($intervalInSeconds % 3600) / 60);
                                $seconds = $intervalInSeconds % 60;
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($commande['code_commande']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" id="temps-<?php echo $commande['id']; ?>">
                                            <?php
                                            $tempsEcoule = '';
                                            if ($hours > 0) $tempsEcoule .= $hours . 'h ';
                                            if ($minutes > 0 || $hours > 0) $tempsEcoule .= $minutes . 'm ';
                                            $tempsEcoule .= $seconds . 's';
                                            echo $tempsEcoule;
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo number_format($commande['total'], 2, ',', ' ') . ' €'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <?php echo htmlspecialchars($commande['paiement_method']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                                                Prête
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="info_commande.php?commande_id=<?php echo $commande['id']; ?>" 
                                           class="text-[#D84315] hover:text-[#BF360C]">
                                            Voir détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <p>Aucune commande en cours.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateElapsedTime() {
    var commandes = <?php echo json_encode($commandes); ?>;

    commandes.forEach(function(commande) {
        var createdAt = new Date(commande.created_at);
        var now = new Date();
        var elapsedTimeInSeconds = Math.floor((now - createdAt) / 1000);

        var hours = Math.floor(elapsedTimeInSeconds / 3600);
        var minutes = Math.floor((elapsedTimeInSeconds % 3600) / 60);
        var seconds = elapsedTimeInSeconds % 60;

        var timeString = '';
        if (hours > 0) {
            timeString += hours + 'h ';
        }
        if (minutes > 0 || hours > 0) {
            timeString += minutes + 'm ';
        }
        timeString += seconds + 's';

        document.getElementById('temps-' + commande.id).textContent = timeString;
    });
}

setInterval(updateElapsedTime, 1000);
</script>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>
