<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fast food</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex">
    <header class="w-[200px] min-h-screen bg-gray-100">
        <nav class="flex flex-col h-full py-8 px-4 space-y-8">
            <div class="flex justify-center mb-8">
                <img src="../../Assets/images/logo-simple.png" alt="Click & Eat" class="w-16 h-16">
            </div>

            <a href="formules.php" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/burger.png" alt="Menu" class="w-12 h-12 mb-2">
                <span>Menu</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/frite.png" alt="Frites" class="w-12 h-12 mb-2">
                <span>Frites</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/burger.png" alt="Burger" class="w-12 h-12 mb-2">
                <span>Burger</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/sauce.png" alt="Sauce" class="w-12 h-12 mb-2">
                <span>Sauce</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/boisson.png" alt="Boisson" class="w-12 h-12 mb-2">
                <span>Boisson</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-600 hover:text-gray-900">
                <img src="../../Assets/images/salade.png" alt="Salade" class="w-12 h-12 mb-2">
                <span>Salade</span>
            </a>
<div>
    <header>
        <nav>
            <?php if (isset($_SESSION['auth'])): ?>
                <?php if ($_SESSION['role'] === 'ZONE CUISINE'): ?>
                    <a href="../Admin/zone_cuisine.php">Zone cuisine</a>
                <?php elseif ($_SESSION['role'] === 'ZONE STOCK'): ?>
                    <a href="../Admin/zone_stock.php">Zone stock</a>
                <?php elseif ($_SESSION['role'] === 'ZONE MANAGEMENT'): ?>
                    <a href="../Admin/zone_admin.php">Dashboard</a>
                    <a href="../Admin/liste_utilisateurs.php">Liste des utilisateurs</a>
                    <a href="../Admin/liste_plats.php">Liste des plats</a>
                    <a href="../Admin/liste_formules.php">Liste des menus</a>
                <?php endif; ?>
                <p>Connecté en tant que : <?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom'] ?></p>
                <a href="../Actions/Deconnexion.php" class="logout">Déconnexion</a>
            <?php else: ?>
                <a href="../Admin/connexion.php">Se connecter</a>
            <?php endif; ?>
        </nav>
    </header>
</div>
</html>
