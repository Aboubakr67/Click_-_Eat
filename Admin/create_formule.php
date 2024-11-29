<?php
require_once('../Actions/zone_admin_repo.php');
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/ft_extensions.php');

$platsPlats = getPlatsByType('PLAT');
$platsDesserts = getPlatsByType('DESSERT');
$platsBoissons = getPlatsByType('BOISSON');

// Tout le code PHP de traitement du formulaire reste inchangé
if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $image = null;
    $platId = $_POST['plat_id'];
    $boissonId = $_POST['boisson_id'];
    $dessertId = $_POST['dessert_id'];

    if (empty($nom) || empty($prix) || empty($platId) || empty($boissonId) || empty($dessertId)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: create_formule.php");
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $imageMimeType = mime_content_type($imageTmpName);

        if (getAndVerify("." . $imageExt, $imageMimeType)) {
            $newImageName = uniqid() . '.' . $imageExt;
            $imagePath = '../Assets/img/formules/' . $newImageName;
            move_uploaded_file($imageTmpName, $imagePath);
            $image = $newImageName;
        } else {
            $_SESSION['error'] = "L'image téléchargée n'est pas valide. Formats autorisés : jpg, jpeg, png, gif, bmp, tiff.";
            header("Location: create_formule.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "L'image est obligatoire.";
        header("Location: create_formule.php");
        exit;
    }

    try {
        $con = connexion();
        $query = "INSERT INTO formules (nom, prix, image) VALUES (:nom, :prix, :image)";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->execute();

        $formuleId = $con->lastInsertId();

        $queryFormulePlat = "INSERT INTO formule_plat (formule_id, plat_id) VALUES (:formule_id, :plat_id)";
        $stmtFormulePlat = $con->prepare($queryFormulePlat);
        $stmtFormulePlat->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);

        foreach ([$platId, $boissonId, $dessertId] as $plat) {
            $stmtFormulePlat->bindParam(':plat_id', $plat, PDO::PARAM_INT);
            $stmtFormulePlat->execute();
        }

        $_SESSION['success'] = "Formule créée avec succès.";
        header("Location: liste_formules.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la création de la formule : " . $e->getMessage();
        header("Location: create_formule.php");
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
                    <a href="liste_utilisateurs.php" class="text-black hover:text-[#D84315]">Gestion utilisateur</a>
                </li>
                <li>
                    <a href="liste_plats.php" class="text-black hover:text-[#D84315]">Gestion de stock</a>
                </li>
                <li>
                    <a href="liste_formules.php" class="text-[#D84315]">Management</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-[calc(100%-200px)]">
        <div class="p-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Créer une Formule</h1>
                <a href="liste_formules.php" class="text-[#D84315] hover:text-[#BF360C] flex items-center gap-2">
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
                <form action="create_formule.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom de la Formule</label>
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

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image de la Formule</label>
                        <input type="file" 
                               id="image" 
                               name="image"
                               accept="image/*"
                               required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D84315] file:text-white hover:file:bg-[#BF360C]">
                    </div>

                    <!-- Sélection des plats -->
                    <div class="grid grid-cols-3 gap-6">
                        <!-- Plat -->
                        <div>
                            <label for="plat_id" class="block text-sm font-medium text-gray-700 mb-1">Plat Principal</label>
                            <select name="plat_id" id="plat_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                                <option value="">Sélectionnez un plat</option>
                                <?php foreach ($platsPlats as $plat): ?>
                                    <option value="<?php echo $plat['id']; ?>">
                                        <?php echo htmlspecialchars($plat['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Boisson -->
                        <div>
                            <label for="boisson_id" class="block text-sm font-medium text-gray-700 mb-1">Boisson</label>
                            <select name="boisson_id" id="boisson_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                                <option value="">Sélectionnez une boisson</option>
                                <?php foreach ($platsBoissons as $boisson): ?>
                                    <option value="<?php echo $boisson['id']; ?>">
                                        <?php echo htmlspecialchars($boisson['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dessert -->
                        <div>
                            <label for="dessert_id" class="block text-sm font-medium text-gray-700 mb-1">Dessert</label>
                            <select name="dessert_id" id="dessert_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                                <option value="">Sélectionnez un dessert</option>
                                <?php foreach ($platsDesserts as $dessert): ?>
                                    <option value="<?php echo $dessert['id']; ?>">
                                        <?php echo htmlspecialchars($dessert['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                name="submit"
                                class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                            Créer la formule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require('../HeaderFooter/Admin/Footer.php'); ?>
