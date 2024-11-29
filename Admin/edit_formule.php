<?php
require_once('../Actions/zone_admin_repo.php');
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/ft_extensions.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

$platsPlats = getPlatsByType('PLAT');
$platsDesserts = getPlatsByType('DESSERT');
$platsBoissons = getPlatsByType('BOISSON');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $formuleId = $_GET['id'];
    try {
        $con = connexion();
        $query = "SELECT * FROM formules WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $formuleId, PDO::PARAM_INT);
        $stmt->execute();
        $formule = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$formule) {
            $_SESSION['error'] = "Formule introuvable.";
            header("Location: liste_formules.php");
            exit;
        }

        $queryPlats = "
            SELECT fp.plat_id, p.type 
            FROM formule_plat fp
            JOIN plats p ON fp.plat_id = p.id
            WHERE fp.formule_id = :formule_id";
        $stmtPlats = $con->prepare($queryPlats);
        $stmtPlats->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);
        $stmtPlats->execute();
        $platsAssocies = $stmtPlats->fetchAll(PDO::FETCH_ASSOC);

        $platId = null;
        $boissonId = null;
        $dessertId = null;

        foreach ($platsAssocies as $plat) {
            switch ($plat['type']) {
                case 'PLAT':
                    $platId = $plat['plat_id'];
                    break;
                case 'BOISSON':
                    $boissonId = $plat['plat_id'];
                    break;
                case 'DESSERT':
                    $dessertId = $plat['plat_id'];
                    break;
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la récupération de la formule : " . $e->getMessage();
        header("Location: liste_formules.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID de la formule manquant.";
    header("Location: liste_formules.php");
    exit;
}

// Vérification de la soumission du formulaire
if (isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $image = $formule['image']; // Garder l'image actuelle par défaut
    $platId = $_POST['plat_id'];
    $boissonId = $_POST['boisson_id'];
    $dessertId = $_POST['dessert_id'];

    // Vérification des champs obligatoires
    if (empty($nom) || empty($prix) || empty($platId) || empty($boissonId) || empty($dessertId)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: edit_formule.php?id=$formuleId");
        exit;
    }

    // Vérification de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $imageMimeType = mime_content_type($imageTmpName);

        // Vérifier si l'image est valide
        if (getAndVerify("." . $imageExt, $imageMimeType)) {
            // L'image est valide, on génère un nouveau nom pour éviter les conflits
            $newImageName = uniqid() . '.' . $imageExt;
            $imagePath = '../Assets/images/formules/' . $newImageName;

            // Supprimer l'ancienne image
            if (file_exists('../Assets/images/formules/' . $formule['image'])) {
                unlink('../Assets/images/formules/' . $formule['image']);
            }

            // Déplacer l'image vers le dossier de destination
            move_uploaded_file($imageTmpName, $imagePath);
            $image = $newImageName;
        } else {
            $_SESSION['error'] = "L'image téléchargée n'est pas valide. Formats autorisés : jpg, jpeg, png, gif, bmp, tiff.";
            header("Location: edit_formule.php?id=$formuleId");
            exit;
        }
    }

    // Mise à jour de la formule
    try {
        // Mettre à jour la formule dans la table 'formules'
        $con = connexion();
        $query = "UPDATE formules SET nom = :nom, prix = :prix, image = :image WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':id', $formuleId, PDO::PARAM_INT);
        $stmt->execute();

        // Mise à jour des plats associés
        $queryDelete = "DELETE FROM formule_plat WHERE formule_id = :formule_id";
        $stmtDelete = $con->prepare($queryDelete);
        $stmtDelete->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);
        $stmtDelete->execute();

        $queryFormulePlat = "INSERT INTO formule_plat (formule_id, plat_id) VALUES (:formule_id, :plat_id)";
        $stmtFormulePlat = $con->prepare($queryFormulePlat);
        $stmtFormulePlat->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);

        // Insérer les plats associés à la formule
        foreach ([$platId, $boissonId, $dessertId] as $plat) {
            $stmtFormulePlat->bindParam(':plat_id', $plat, PDO::PARAM_INT);
            $stmtFormulePlat->execute();
        }

        $_SESSION['success'] = "Formule modifiée avec succès.";
        header("Location: liste_formules.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la modification de la formule : " . $e->getMessage();
        header("Location: edit_formule.php?id=$formuleId");
        exit;
    }
}
?>

<div class="flex">
    <div class="p-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Modifier une Formule</h1>
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
            <form action="edit_formule.php?id=<?php echo $formuleId; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom de la Formule</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($formule['nom']); ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>

                    <!-- Prix -->
                    <div>
                        <label for="prix" class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                        <input type="number" id="prix" name="prix" value="<?php echo $formule['prix']; ?>" step="0.01" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image de la Formule</label>
                    <div class="flex items-center space-x-4">
                        <?php if ($formule['image']): ?>
                            <img src="../Assets/images/formules/<?php echo $formule['image']; ?>"
                                alt="Image actuelle"
                                class="w-24 h-24 object-cover rounded-lg">
                        <?php endif; ?>
                        <input type="file"
                            id="image"
                            name="image"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D84315] file:text-white hover:file:bg-[#BF360C]">
                    </div>
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
                                <option value="<?php echo $plat['id']; ?>" <?php echo ($plat['id'] == $platId) ? 'selected' : ''; ?>>
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
                                <option value="<?php echo $boisson['id']; ?>" <?php echo ($boisson['id'] == $boissonId) ? 'selected' : ''; ?>>
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
                                <option value="<?php echo $dessert['id']; ?>" <?php echo ($dessert['id'] == $dessertId) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dessert['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" name="submit"
                        class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                        Mettre à jour la formule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<?php require('../HeaderFooter/Admin/Footer.php'); ?>