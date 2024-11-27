<?php
require('../HeaderFooter/Client/Header.php');
require("../Actions/client_repo.php");
$formules = getAllFormules();
?>

<h1>Menus</h1>

<div class="formules-container">
    <?php if (!empty($formules)): ?>
        <ul>
            <?php foreach ($formules as $formule): ?>
                <li>
                    <a href="choix_ingredients.php?formule_id=<?php echo htmlspecialchars($formule['id']); ?>">
                        <?php echo htmlspecialchars($formule['nom']); ?> - <?php echo number_format($formule['prix'], 2, ',', ' '); ?> €
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune formule disponible pour le moment.</p>
    <?php endif; ?>
</div>

<?php
require('../HeaderFooter/Client/Footer.php');
?>