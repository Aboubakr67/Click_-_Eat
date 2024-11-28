<?php
require('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_cuisine_repo.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}
$tempsMoyen = getTempsMoyenCommande();
?>


<h1>Admin</h1>

<div class="bg-white p-6 rounded-lg shadow-md my-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Temps moyen de réalisation d'une commande</h2>
    <p class="text-lg text-gray-600">
        <?php if ($tempsMoyen === 'Aucune commande'): ?>
            <span class="font-bold text-red-500">Aucune commande</span>
        <?php else: ?>
            <span class="font-bold text-green-500"><?php echo floor($tempsMoyen) . ' minutes'; ?></span>
        <?php endif; ?>
    </p>
</div>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>