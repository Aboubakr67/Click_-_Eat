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
    <!-- Sidebar -->
    <div class="w-[200px] h-screen bg-[#FFF1F1] fixed left-0 top-0">
        <div class="p-4">
            <img src="../Assets/images/logo_fast_food.png" alt="Click & Eat" class="w-24 mb-12">

            <ul class="space-y-6">
                <li>
                    <a href="#" class="text-black hover:text-[#D84315]">Dashboard</a>
                </li>
                <li>
                    <a href="#" class="text-black hover:text-[#D84315]">Gestion utilisateur</a>
                </li>
                <li>
                    <a href="#" class="text-black hover:text-[#D84315]">Gestion de stock</a>
                </li>
                <li>
                    <a href="#" class="text-black hover:text-[#D84315]">Management</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-[200px] w-[calc(100%-200px)]">
        <!-- Welcome and Logout Section -->
        <div class="flex justify-end items-center p-4 bg-white">
            <div class="flex items-center gap-4">
                <a href="../Actions/Deconnexion.php" class="px-4 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                    Déconnexion
                </a>
            </div>
        </div>

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