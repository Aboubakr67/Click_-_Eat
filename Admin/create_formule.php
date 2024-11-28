<?php
require_once('../Actions/zone_admin_repo.php'); // Inclut les fonctions nécessaires
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/ft_extensions.php');

$platsPlats = getPlatsByType('PLAT');
$platsDesserts = getPlatsByType('DESSERT');
$platsBoissons = getPlatsByType('BOISSON');

// Vérification de la soumission du formulaire
if (isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $image = null;
    $platId = $_POST['plat_id'];
    $boissonId = $_POST['boisson_id'];
    $dessertId = $_POST['dessert_id'];

    // Vérification des champs obligatoires
    if (empty($nom) || empty($prix) || empty($platId) || empty($boissonId) || empty($dessertId)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: create_formule.php");
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
            $imagePath = '../Assets/img/formules/' . $newImageName;

            // Déplacer l'image vers le dossier de destination
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

    // Insertion de la formule
    try {
        // Insérer la formule dans la table 'formules'
        $con = connexion();
        $query = "INSERT INTO formules (nom, prix, image) VALUES (:nom, :prix, :image)";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->execute();

        // Récupérer l'ID de la dernière formule insérée
        $formuleId = $con->lastInsertId();

        // Insérer les plats dans la table 'formule_plat'
        $queryFormulePlat = "INSERT INTO formule_plat (formule_id, plat_id) VALUES (:formule_id, :plat_id)";
        $stmtFormulePlat = $con->prepare($queryFormulePlat);
        $stmtFormulePlat->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);

        // Insérer les plats associés à la formule
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

<h1>Créer une Formule</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </p>
<?php endif; ?>

<form action="create_formule.php" method="POST" enctype="multipart/form-data">
    <div>
        <label for="nom">Nom de la Formule :</label>
        <input type="text" name="nom" id="nom" required>
    </div>

    <div>
        <label for="prix">Prix :</label>
        <input type="number" name="prix" id="prix" step="0.01" required>
    </div>

    <div>
        <label for="image">Image de la Formule :</label>
        <input type="file" name="image" id="image" accept="image/*" required>
    </div>

    <div>
        <label for="plat_id">Plat :</label>
        <select name="plat_id" id="plat_id" required>
            <option value="">Sélectionnez un plat</option>
            <?php foreach ($platsPlats as $plat): ?>
                <option value="<?php echo $plat['id']; ?>">
                    <?php echo htmlspecialchars($plat['nom']); ?>
                    <!-- <img src="../Assets/img/<?php echo $plat['image']; ?>" alt="<?php echo $plat['nom']; ?>" style="width:30px; height:30px;"> -->
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="boisson_id">Boisson :</label>
        <select name="boisson_id" id="boisson_id" required>
            <option value="">Sélectionnez une boisson</option>
            <?php foreach ($platsBoissons as $boisson): ?>
                <option value="<?php echo $boisson['id']; ?>">
                    <?php echo htmlspecialchars($boisson['nom']); ?>
                    <!-- <img src="../Assets/img/<?php echo $boisson['image']; ?>" alt="<?php echo $boisson['nom']; ?>" style="width:30px; height:30px;"> -->
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="dessert_id">Dessert :</label>
        <select name="dessert_id" id="dessert_id" required>
            <option value="">Sélectionnez un dessert</option>
            <?php foreach ($platsDesserts as $dessert): ?>
                <option value="<?php echo $dessert['id']; ?>">
                    <?php echo htmlspecialchars($dessert['nom']); ?>
                    <!-- <img src="../Assets/img/<?php echo $dessert['image']; ?>" alt="<?php echo $dessert['nom']; ?>" style="width:30px; height:30px;"> -->
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <button type="submit" name="submit">Créer la Formule</button>
    </div>
</form>