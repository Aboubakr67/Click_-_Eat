<?php
require('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}
require('../Actions/zone_admin_repo.php');

$listesUsers = getAllUsers();
?>

<h1>Liste des utilisateurs</h1>

<!-- Bouton pour ajouter un utilisateur -->
<a href="create_utilisateur.php">Créer un utilisateur</a>

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



<?php if (!empty($listesUsers)): ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($listesUsers as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nom']) ?></td>
                    <td><?= htmlspecialchars($row['prenom']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td>
                        <a href="edit_utilisateur.php?id=<?= htmlspecialchars($row['id']) ?>">Modifier</a> |
                        <a href="delete_utilisateur.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
<?php else: ?>

    <h2 colspan="6">Aucun utilisateur trouvé.</h2>

<?php endif; ?>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>