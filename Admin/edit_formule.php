<?php
require_once('../Actions/zone_admin_repo.php'); // Inclut les fonctions nécessaires
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/ft_extensions.php');


$platsPlats = getPlatsByType('PLAT');
$platsDesserts = getPlatsByType('DESSERT');
$platsBoissons = getPlatsByType('BOISSON');

// Vérifier si l'ID de la formule est passé en URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $formuleId = $_GET['id'];

    // Récupérer les données de la formule à modifier
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

        // Récupérer les plats associés à la formule avec leurs types
        $queryPlats = "
SELECT fp.plat_id, p.type 
FROM formule_plat fp
JOIN plats p ON fp.plat_id = p.id
WHERE fp.formule_id = :formule_id
";
        $stmtPlats = $con->prepare($queryPlats);
        $stmtPlats->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);
        $stmtPlats->execute();
        $platsAssocies = $stmtPlats->fetchAll(PDO::FETCH_ASSOC);

        // Organiser les IDs associés dans des variables en fonction du type
        $platId = null;
        $boissonId = null;
        $dessertId = null;

        foreach ($platsAssocies as $plat) {
            if ($plat['type'] == 'PLAT') {
                $platId = $plat['plat_id'];
            } elseif ($plat['type'] == 'BOISSON') {
                $boissonId = $plat['plat_id'];
            } elseif ($plat['type'] == 'DESSERT') {
                $dessertId = $plat['plat_id'];
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
            $imagePath = '../Assets/img/formules/' . $newImageName;

            // Supprimer l'ancienne image
            if (file_exists('../Assets/img/formules/' . $formule['image'])) {
                unlink('../Assets/img/formules/' . $formule['image']);
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

<h1>Modifier une Formule</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </p>
<?php endif; ?>

<form action="edit_formule.php?id=<?php echo $formuleId; ?>" method="POST" enctype="multipart/form-data">
    <div>
        <label for="nom">Nom de la Formule :</label>
        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($formule['nom']); ?>" required>
    </div>

    <div>
        <label for="prix">Prix :</label>
        <input type="number" name="prix" id="prix" value="<?php echo $formule['prix']; ?>" step="0.01" required>
    </div>

    <div>
        <label for="image">Image de la Formule :</label>
        <input type="file" name="image" id="image" accept="image/*">
        <?php if ($formule['image']): ?>
            <img src="../Assets/img/formules/<?php echo $formule['image']; ?>" alt="Image actuelle" style="width:100px; height:auto;">
        <?php endif; ?>
    </div>

    <div>
        <label for="plat_id">Plat :</label>
        <select name="plat_id" id="plat_id" required>
            <option value="">Sélectionnez un plat</option>
            <?php foreach ($platsPlats as $plat): ?>
                <option value="<?php echo $plat['id']; ?>" <?php echo ($plat['id'] == $platId) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($plat['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="boisson_id">Boisson :</label>
        <select name="boisson_id" id="boisson_id" required>
            <option value="">Sélectionnez une boisson</option>
            <?php foreach ($platsBoissons as $boisson): ?>
                <option value="<?php echo $boisson['id']; ?>" <?php echo ($boisson['id'] == $boissonId) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($boisson['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="dessert_id">Dessert :</label>
        <select name="dessert_id" id="dessert_id" required>
            <option value="">Sélectionnez un dessert</option>
            <?php foreach ($platsDesserts as $dessert): ?>
                <option value="<?php echo $dessert['id']; ?>" <?php echo ($dessert['id'] == $dessertId) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dessert['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <button type="submit" name="submit">Modifier la Formule</button>
    </div>
</form>