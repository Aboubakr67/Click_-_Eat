<?php
require('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_cuisine_repo.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

$tempsMoyen = getTempsMoyenCommande();
?>

<div class="flex">
    <div class="p-8">
        <!-- Stats Cards -->
        <p class="text-gray-700 text-2xl mb-8">Bienvenue, <span class="font-medium"><?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom'] ?></span></p>
        <div class="grid grid-cols-3 gap-6 mb-8">
            <!-- Commandes du jour -->
            <div class="bg-gradient-to-br from-[#FF8A65] to-[#FF5722] rounded-[20px] p-6">
                <h3 class="text-white text-sm mb-2">Nombre de commande du jour</h3>
                <p class="text-white text-4xl font-bold">150</p>
            </div>

            <!-- Chiffre d'affaires -->
            <div class="bg-gradient-to-br from-[#FF8A65] to-[#FF5722] rounded-[20px] p-6">
                <h3 class="text-white text-sm mb-2">Chiffre d'affaire du mois</h3>
                <p class="text-white text-4xl font-bold">150,000</p>
            </div>

            <!-- Temps moyen -->
            <div class="bg-gradient-to-br from-[#FF8A65] to-[#FF5722] rounded-[20px] p-6">
                <h3 class="text-white text-sm mb-2">Temps moyen de réalisation</h3>
                <p class="text-white text-4xl font-bold">8min</p>
            </div>
        </div>

        <!-- Graph Section -->
        <div class="bg-white rounded-[20px] p-6 shadow-sm">
            <canvas id="myChart" class="w-full h-[300px]"></canvas>
        </div>
    </div>
</div>
</div>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>