<?php
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_admin_repo.php');
require_once('../Actions/ft_extensions.php');

// Vérifie si l'utilisateur est autorisé
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

// Vérification de la soumission du formulaire
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
            $imagePath = '../Assets/img/' . $newImageName;

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

<h1>Créer un plat</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </p>
<?php endif; ?>


<!-- Formulaire HTML pour créer un plat -->
<form action="create_plat.php" method="POST" enctype="multipart/form-data">
    <div>
        <label for="nom">Nom du Plat :</label>
        <input type="text" name="nom" id="nom" required>
    </div>

    <div>
        <label for="prix">Prix :</label>
        <input type="number" name="prix" id="prix" step="0.01" required>
    </div>

    <div>
        <label for="type">Type :</label>
        <select name="type" id="type" required>
            <option value="PLAT">PLAT</option>
            <option value="ENTREE">ENTREE</option>
            <option value="DESSERT">DESSERT</option>
            <option value="BOISSON">BOISSON</option>
        </select>
    </div>

    <div>
        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*">
    </div>

    <div>
        <label for="ingredients">Ingrédients :</label>
        <div>
            <?php
            // Récupérer tous les ingrédients depuis la base de données
            $ingredients = getIngredients();

            foreach ($ingredients as $ingredient) {
                echo "<div style='margin-bottom: 10px;'>";
                echo "<img src='../Assets/img/ingredients/{$ingredient['image']}' alt='{$ingredient['nom']}' style='width:50px; height:50px; margin-right:10px;'>";
                echo "<label>";
                echo "<input type='checkbox' name='ingredients[]' value='{$ingredient['id']}'>";
                echo htmlspecialchars($ingredient['nom']);
                echo "</label>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <div>
        <button type="submit" name="submit">Créer le Plat</button>
    </div>
</form>