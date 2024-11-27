<?php
require_once('../Actions/zone_admin_repo.php');
require_once('../Actions/ft_extensions.php');
require_once('../HeaderFooter/Admin/Header.php');

// Vérifie si l'utilisateur est autorisé
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

// Récupérer l'ID du plat à modifier
$platId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($platId == 0) {
    $_SESSION['error'] = "Id du plat égale à 0";
    exit;
}

// Récupérer les détails du plat à modifier
$plat = getPlatById($platId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prix = htmlspecialchars($_POST['prix']);
    $type = htmlspecialchars($_POST['type']);
    $image = $plat['image']; // Garder l'ancienne image par défaut
    $ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];

    if ($plat['type'] == "PLAT" || $plat['type'] == "ENTREE" || $plat['type'] == "DESSERT") {
        if (empty($ingredients)) {
            $_SESSION['error'] = "Vous devez sélectionner au moins un ingrédient.";
            header("Location: edit_plat.php?id=$platId");
            exit; 
        }
    }

    // echo "Nom : " . $nom;
    // echo "<br/>";
    // echo "prix : " . $prix;
    // echo "<br/>";
    // echo "type : " . $type;
    // echo "<br/>";
    // echo "ingredients : ";
    // var_dump($ingredients);

    // Vérifier si une nouvelle image a été téléchargée
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $fileExtension = '.' . strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileMimeType = mime_content_type($file['tmp_name']);

        // var_dump('Type MIME détecté : ' . mime_content_type($file['tmp_name']));
        // exit;

        // Vérifier si l'image est valide
        if (getAndVerify($fileExtension, $fileMimeType)) {

            // var_dump("ACCEPTER");
            // Supprimer l'ancienne image du dossier
            $oldImagePath = '../Assets/img/' . $plat['image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Renommer et déplacer la nouvelle image
            $newImageName = $plat['image']; // Utiliser le nom actuel du plat
            $newImagePath = '../Assets/img/' . $newImageName;

            if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
                $image = $newImageName;
            } else {
                $_SESSION['error'] = "Erreur lors du téléchargement de l'image.";
                header("Location: edit_plat.php?id=$platId");
                exit;
            }
        } else {
            $_SESSION['error'] = "Le fichier téléchargé n'est pas une image valide.";
            header("Location: edit_plat.php?id=$platId");
            exit;
        }
    }

    // Mise à jour du plat avec les nouveaux détails
    $updated = updatePlat($platId, $nom, $prix, $type, $image, $ingredients);

    if ($updated) {
        $_SESSION['success'] = "Plat mis à jour avec succès.";
        header("Location: liste_plats.php");
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du plat.";
        header("Location: edit_plat.php?id=$platId");
        exit;
    }
}

?>

<h1>Modifier le plat</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </p>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <div>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo $plat['nom']; ?>" required>
    </div>

    <div>
        <label for="prix">Prix :</label>
        <input type="text" id="prix" name="prix" value="<?php echo $plat['prix']; ?>" required>
    </div>

    <div>
        <label for="type">Type :</label>
        <select id="type" name="type" required>
            <option value="ENTREE" <?php echo $plat['type'] == 'ENTREE' ? 'selected' : ''; ?>>ENTREE</option>
            <option value="PLAT" <?php echo $plat['type'] == 'PLAT' ? 'selected' : ''; ?>>PLAT</option>
            <option value="DESSERT" <?php echo $plat['type'] == 'DESSERT' ? 'selected' : ''; ?>>DESSERT</option>
            <option value="BOISSON" <?php echo $plat['type'] == 'BOISSON' ? 'selected' : ''; ?>>BOISSON</option>
        </select>
    </div>

    <div>
        <label for="ingredients">Ingrédients :</label>
        <div>
            <?php
            $ingredients = getIngredients(); // Récupérer tous les ingrédients
            $platIngredients = getIngredientsByPlat($platId); // Ingrédients associés au plat

            foreach ($ingredients as $ingredient) {
                $checked = in_array($ingredient['id'], $platIngredients) ? 'checked' : '';
                echo "<div style='margin-bottom: 10px;'>";
                echo "<img src='../Assets/img/ingredients/{$ingredient['image']}' alt='{$ingredient['nom']}' style='width:50px; height:50px; margin-right:10px;'>";
                echo "<label>";
                echo "<input type='checkbox' name='ingredients[]' value='{$ingredient['id']}' $checked>";
                echo htmlspecialchars($ingredient['nom']);
                echo "</label>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <div>
        <label for="image">Image actuelle :</label>
        <div>
            <?php if (!empty($plat['image']) && empty($_FILES['image']['name'])): ?>
                <img src="../Assets/img/<?php echo $plat['image']; ?>" alt="Image du plat" style="width: 100px; height: auto;">
            <?php endif; ?>
        </div>
        <label for="image">Changer l'image :</label>
        <input type="file" id="image" name="image">
    </div>

    <button type="submit">Mettre à jour le plat</button>
</form>

<?php require('../HeaderFooter/Admin/Footer.php'); ?>