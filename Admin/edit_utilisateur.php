<?php
require('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Utilisateur introuvable.";
    exit;
}

require('../Actions/zone_admin_repo.php');

$userId = (int) $_GET['id'];
$user = getUserById($userId);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    $updated = updateUser($userId, $nom, $prenom, $email, $role);

    if ($updated) {
        $_SESSION['success'] = "Mise à jour réussie.";
        header("Location: liste_utilisateurs.php");
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
        header("Location: edit_utilisateur.php?id=$userId");
        exit;
    }
}
?>

<div class="flex">
    <!-- Sidebar -->
    <div class="w-[200px] h-screen bg-[#FFF1F1] fixed left-0 top-0">
        <div class="p-4">
            <img src="../Assets/images/logo_fast_food.png" alt="Click & Eat" class="w-24 mb-12">
            
            <ul class="space-y-6">
                <li>
                    <a href="zone_admin.php" class="text-black hover:text-[#D84315]">Dashboard</a>
                </li>
                <li>
                    <a href="liste_utilisateurs.php" class="text-[#D84315]">Gestion utilisateur</a>
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
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Modifier l'utilisateur</h1>
                <a href="liste_utilisateurs.php" class="text-[#D84315] hover:text-[#BF360C] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
                <form method="POST" action="" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                        </div>

                        <!-- Prénom -->
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>

                    <!-- Rôle -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                        <select id="role" name="role" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                            <option value="ZONE CUISINE" <?= $user['role'] === 'ZONE CUISINE' ? 'selected' : '' ?>>ZONE CUISINE</option>
                            <option value="ZONE STOCK" <?= $user['role'] === 'ZONE STOCK' ? 'selected' : '' ?>>ZONE STOCK</option>
                            <option value="ZONE MANAGEMENT" <?= $user['role'] === 'ZONE MANAGEMENT' ? 'selected' : '' ?>>ZONE MANAGEMENT</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>
