<?php
require_once('../Actions/zone_admin_repo.php');
require_once('../Actions/ft_extensions.php');
require_once('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

$platId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($platId == 0) {
    $_SESSION['error'] = "Id du plat égale à 0";
    exit;
}

$plat = getPlatById($platId);
?>

<div class="flex">
    <div class="p-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Modifier le plat</h1>
            <a href="liste_plats.php" class="text-[#D84315] hover:text-[#BF360C] flex items-center gap-2">
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
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" id="nom" name="nom" value="<?php echo $plat['nom']; ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>

                    <!-- Prix -->
                    <div>
                        <label for="prix" class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                        <input type="text" id="prix" name="prix" value="<?php echo $plat['prix']; ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="type" name="type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                        <option value="ENTREE" <?php echo $plat['type'] == 'ENTREE' ? 'selected' : ''; ?>>ENTREE</option>
                        <option value="PLAT" <?php echo $plat['type'] == 'PLAT' ? 'selected' : ''; ?>>PLAT</option>
                        <option value="DESSERT" <?php echo $plat['type'] == 'DESSERT' ? 'selected' : ''; ?>>DESSERT</option>
                        <option value="BOISSON" <?php echo $plat['type'] == 'BOISSON' ? 'selected' : ''; ?>>BOISSON</option>
                    </select>
                </div>

                <!-- Ingrédients -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ingrédients</label>
                    <div class="grid grid-cols-3 gap-4">
                        <?php
                        $ingredients = getIngredients();
                        $platIngredients = getIngredientsByPlat($platId);

                        foreach ($ingredients as $ingredient):
                            $checked = in_array($ingredient['id'], $platIngredients) ? 'checked' : '';
                        ?>
                            <div class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                                <img src="../Assets/img/ingredients/<?php echo $ingredient['image']; ?>"
                                    alt="<?php echo htmlspecialchars($ingredient['nom']); ?>"
                                    class="w-12 h-12 object-cover rounded">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                        id="ingredient_<?php echo $ingredient['id']; ?>"
                                        name="ingredients[]"
                                        value="<?php echo $ingredient['id']; ?>"
                                        <?php echo $checked; ?>
                                        class="w-4 h-4 text-[#D84315] border-gray-300 rounded focus:ring-[#D84315]">
                                    <label for="ingredient_<?php echo $ingredient['id']; ?>"
                                        class="ml-2 text-sm text-gray-700">
                                        <?php echo htmlspecialchars($ingredient['nom']); ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <div class="flex items-center space-x-4">
                        <?php if (!empty($plat['image'])): ?>
                            <img src="../Assets/img/<?php echo $plat['image']; ?>"
                                alt="Image actuelle"
                                class="w-24 h-24 object-cover rounded-lg">
                        <?php endif; ?>
                        <input type="file"
                            id="image"
                            name="image"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D84315] file:text-white hover:file:bg-[#BF360C]">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                        Mettre à jour le plat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<?php require('../HeaderFooter/Admin/Footer.php'); ?>