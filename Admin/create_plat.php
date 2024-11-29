<?php
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_admin_repo.php');
require_once('../Actions/ft_extensions.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

if (isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $type = $_POST['type'];
    $ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];

    // Vérification des champs obligatoires : nom, prix, type et image
    if (empty($nom)) {
        $_SESSION['error'] = "Le nom du plat est obligatoire.";
        header("Location: create_plat.php");
        exit;
    }

    if (empty($prix)) {
        $_SESSION['error'] = "Le prix du plat est obligatoire.";
        header("Location: create_plat.php");
        exit;
    }

    if (empty($type)) {
        $_SESSION['error'] = "Le type du plat est obligatoire.";
        header("Location: create_plat.php");
        exit;
    }

    // Validation des ingrédients : obligatoires pour les types PLAT, ENTREE, DESSERT
    if (($type == "PLAT" || $type == "ENTREE" || $type == "DESSERT") && empty($ingredients)) {
        $_SESSION['error'] = "Vous devez sélectionner au moins un ingrédient.";
        header("Location: create_plat.php");
        exit;
    }

    echo "Nom : " . $nom;
    echo "<br/>";
    echo "prix : " . $prix;
    echo "<br/>";
    echo "type : " . $type;
    echo "<br/>";
    echo "ingredients : ";
    var_dump($ingredients);

    // Vérification de l'image
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $imageMimeType = mime_content_type($imageTmpName);

        // Utiliser la fonction getAndVerify pour valider l'extension et le type MIME
        if (getAndVerify("." . $imageExt, $imageMimeType)) {
            // L'image est valide, on génère un nouveau nom pour éviter les conflits
            $newImageName = uniqid() . '.' . $imageExt;
            $imagePath = '../Assets/images/' . $newImageName;

            // Déplacer l'image vers le dossier de destination
            move_uploaded_file($imageTmpName, $imagePath);
            $image = $newImageName;
        } else {
            $_SESSION['error'] = "L'image téléchargée n'est pas valide. Formats autorisés : jpg, jpeg, png, gif, bmp, tiff.";
            header("Location: create_plat.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "L'image est obligatoire.";
        header("Location: create_plat.php");
        exit;
    }

    // Insertion du plat dans la base de données
    try {
        // Insertion du plat dans la table 'plats'
        $platId = insertPlat($nom, $prix, $type, $image);

        // Ajouter les ingrédients associés au plat
        if (!empty($ingredients)) {
            insertIngredientsToPlat($platId, $ingredients);
        }

        $_SESSION['success'] = "Plat créé avec succès.";
        header("Location: liste_plats.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la création du plat : " . $e->getMessage();
        header("Location: create_plat.php");
        exit;
    }
}
?>

<div class="flex">
    <div class="p-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Créer un plat</h1>
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
            <form action="create_plat.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom du Plat</label>
                        <input type="text" id="nom" name="nom" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>

                    <!-- Prix -->
                    <div>
                        <label for="prix" class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                        <input type="number" id="prix" name="prix" step="0.01" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="type" name="type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                        <option value="PLAT">PLAT</option>
                        <option value="ENTREE">ENTREE</option>
                        <option value="DESSERT">DESSERT</option>
                        <option value="BOISSON">BOISSON</option>
                    </select>
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                        required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D84315] file:text-white hover:file:bg-[#BF360C]">
                </div>

                <!-- Ingrédients -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ingrédients</label>
                    <div class="grid grid-cols-3 gap-4">
                        <?php
                        $ingredients = getIngredients();
                        foreach ($ingredients as $ingredient):
                        ?>
                            <div class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                                <img src="../Assets/images/ingredients/<?php echo $ingredient['image']; ?>"
                                    alt="<?php echo htmlspecialchars($ingredient['nom']); ?>"
                                    class="w-12 h-12 object-cover rounded">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                        id="ingredient_<?php echo $ingredient['id']; ?>"
                                        name="ingredients[]"
                                        value="<?php echo $ingredient['id']; ?>"
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

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        name="submit"
                        class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                        Créer le plat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<?php require('../HeaderFooter/Admin/Footer.php'); ?>