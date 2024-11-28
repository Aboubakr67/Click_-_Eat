<?php
require('../HeaderFooter/Admin/Header.php');
require('../Actions/zone_admin_repo.php');

// Vérification de l'autorisation
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}



$plats = getPlats();
// var_dump($plats);
?>



<h1>Liste des plats</h1>

<?php

// Affichage du message de succès
if (isset($_SESSION['success'])) {
    echo "<p style='color:green;'>{$_SESSION['success']}</p>";
    unset($_SESSION['success']); // Supprimer le message après affichage
}

// Affichage du message d'erreur
if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>{$_SESSION['error']}</p>";
    unset($_SESSION['error']); // Supprimer le message après affichage
}


?>

<!-- Lien vers la page d'ajout -->
<a href="create_plat.php">Ajouter un nouveau plat</a>


<table border="1">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Type</th>
            <th>Prix</th>
            <th>Image</th>
            <th>Ingrédients</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($plats as $plat): ?>
            <tr>
                <td><?= htmlspecialchars($plat->getNom()) ?></td>
                <td><?= htmlspecialchars($plat->getType()) ?></td>
                <td><?= htmlspecialchars($plat->getPrix()) ?> €</td>
                <td><img src="../Assets/img/<?= htmlspecialchars($plat->getImage()) ?>" alt="Image du plat" width="100"></td>
                <td><?= htmlspecialchars(implode(', ', $plat->getIngredients()) ?: 'Aucun ingrédient') ?></td>
                <td>
                    <a href="edit_plat.php?id=<?= $plat->getId() ?>">Modifier</a> |
                    <a href="delete_plat.php?id=<?= $plat->getId() ?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php
require('../HeaderFooter/Admin/Footer.php');
?>