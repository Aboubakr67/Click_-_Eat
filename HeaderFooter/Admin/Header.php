<?php
ob_start();
date_default_timezone_set('Europe/Paris');
session_start();

// Redirection si non connecté
if (!isset($_SESSION['auth']) && basename($_SERVER['PHP_SELF']) !== 'connexion.php') {
    header("Location: ../Admin/connexion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Click & Eat - Administration</title>
</head>
<body class="bg-gray-100">
    <?php if (isset($_SESSION['auth'])): ?>
        <!-- Sidebar -->
        <div class="w-[200px] h-screen bg-[#FFF1F1] fixed left-0 top-0">
            <div class="p-4">
                <img src="../Assets/images/logo_fast_food.png" alt="Click & Eat" class="w-24 mb-12">

                <ul class="space-y-6">
                    <?php if ($_SESSION['role'] === 'ZONE CUISINE'): ?>
                        <li>
                            <a href="../Admin/zone_cuisine.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'zone_cuisine.php' ? 'text-[#D84315]' : ''; ?>">
                                Commandes en cours
                            </a>
                        </li>
                    <?php elseif ($_SESSION['role'] === 'ZONE STOCK'): ?>
                        <li>
                            <a href="../Admin/zone_stock_reel.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'zone_stock_reel.php' ? 'text-[#D84315]' : ''; ?>">
                                Stock en temps réel
                            </a>
                        </li>
                        <li>
                            <a href="../Admin/remplir_stock.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'remplir_stock.php' ? 'text-[#D84315]' : ''; ?>">
                                Importation CSV
                            </a>
                        </li>
                        <li>
                            <a href="../Admin/dashboard_stock.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_stock.php' ? 'text-[#D84315]' : ''; ?>">
                                Ingrédients Utilisé
                            </a>
                        </li>
                    <?php elseif ($_SESSION['role'] === 'ZONE MANAGEMENT'): ?>
                        <li>
                            <a href="../Admin/zone_admin.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'zone_admin.php' ? 'text-[#D84315]' : ''; ?>">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="../Admin/liste_utilisateurs.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'liste_utilisateurs.php' ? 'text-[#D84315]' : ''; ?>">
                                Gestion utilisateur
                            </a>
                        </li>
                        <li>
                            <a href="../Admin/liste_plats.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'liste_plats.php' ? 'text-[#D84315]' : ''; ?>">
                                Gestion de stock
                            </a>
                        </li>
                        <li>
                            <a href="../Admin/liste_formules.php" class="text-black hover:text-[#D84315] <?php echo basename($_SERVER['PHP_SELF']) === 'liste_formules.php' ? 'text-[#D84315]' : ''; ?>">
                                Management
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-[200px] w-[calc(100%-200px)]">
            <!-- Top Bar -->
            <div class="flex justify-end items-center p-4 bg-white">
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">
                        <?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom']; ?>
                    </span>
                    <a href="../Actions/Deconnexion.php"
                        class="px-4 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                        Déconnexion
                    </a>
                </div>
            </div>
        <?php endif; ?>
