<?php
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_admin_repo.php');

// Récupérer toutes les formules et leurs plats associés, avec les images
function getAllFormulesWithPlatsAndImages()
{
    try {
        $con = connexion();

        // Requête pour récupérer les formules, leurs images, et les plats associés avec leurs images
        $query = "
            SELECT f.id AS formule_id, f.nom AS formule_nom, f.prix AS formule_prix, f.image AS formule_image,
                   p.id AS plat_id, p.nom AS plat_nom, p.image AS plat_image
            FROM formules f
            LEFT JOIN formule_plat fp ON f.id = fp.formule_id
            LEFT JOIN plats p ON fp.plat_id = p.id
            ORDER BY f.id, p.nom";

        $stmt = $con->prepare($query);
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $formuleId = $row['formule_id'];
            if (!isset($result[$formuleId])) {
                $result[$formuleId] = [
                    'nom' => $row['formule_nom'],
                    'prix' => $row['formule_prix'],
                    'image' => $row['formule_image'],
                    'plats' => []
                ];
            }
            if ($row['plat_id']) {
                $result[$formuleId]['plats'][] = [
                    'id' => $row['plat_id'],
                    'nom' => $row['plat_nom'],
                    'image' => $row['plat_image']
                ];
            }
        }
        return $result;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}

$formules = getAllFormulesWithPlatsAndImages();
?>

<h1>Liste des Formules</h1>


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
<a href="create_formule.php">Créer une formule</a>

<?php if (!empty($formules)): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom de la formule</th>
                <th>Prix</th>
                <th>Plats inclus</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formules as $id => $formule): ?>
                <tr>
                    <td>
                        <?php if (!empty($formule['image'])): ?>
                            <img src="../Assets/img/formules/<?php echo htmlspecialchars($formule['image']); ?>" alt="<?php echo htmlspecialchars($formule['nom']); ?>" style="width: 100px; height: auto;">
                        <?php else: ?>
                            Pas d'image
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($formule['nom']); ?></td>
                    <td><?php echo number_format($formule['prix'], 2); ?> €</td>
                    <td>
                        <?php if (!empty($formule['plats'])): ?>
                            <ul>
                                <?php foreach ($formule['plats'] as $plat): ?>
                                    <li>
                                        <img src="../Assets/img/<?php echo htmlspecialchars($plat['image']); ?>" alt="<?php echo htmlspecialchars($plat['nom']); ?>" style="width: 50px; height: auto; margin-right: 5px;">
                                        <?php echo htmlspecialchars($plat['nom']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            Aucun plat associé.
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_formule.php?id=<?php echo $id; ?>">Modifier</a> |
                        <a href="delete_formule.php?id=<?php echo $id; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formule ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune formule trouvée.</p>
<?php endif; ?>