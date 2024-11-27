<?php
require('../HeaderFooter/Client/Header.php');
require("../Actions/client_repo.php");

if (!isset($_GET['formule_id'])) {
    echo "Formule non spécifiée.";
    exit;
}
$formule_id = intval($_GET['formule_id']);

// Charger le plat et ses ingrédients
$plat = getPlatFromFormule($formule_id);
$ingredients = $plat ? getIngredientsFromPlat($plat['id']) : [];

// Récupérer les suppléments disponibles
$supplements = getSupplementIngredients();

?>

<h1>Choix ingredients</h1>

<div class="container">
    <?php if ($plat): ?>
        <h1><?php echo htmlspecialchars($plat['nom']); ?></h1>

        <h2>Ingrédients</h2>
        <ul>
            <?php if (!empty($ingredients)): ?>
                <?php foreach ($ingredients as $ingredient): ?>
                    <li>
                        <?php echo htmlspecialchars($ingredient['ingredient_nom']); ?>
                        (Disponible : <?php echo $ingredient['ingredient_quantite']; ?>)
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Aucun ingrédient disponible pour ce plat.</li>
            <?php endif; ?>
        </ul>
    <?php else: ?>
        <p>Aucun plat principal trouvé dans cette formule.</p>
    <?php endif; ?>
</div>

<?php
require('../HeaderFooter/Client/Footer.php');
?>