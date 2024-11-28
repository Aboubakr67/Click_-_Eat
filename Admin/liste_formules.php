<?php
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_admin_repo.php');

// Récupérer toutes les formules et leurs plats associés, avec les images
// function getAllFormulesWithPlatsAndImages()
// {
//     try {
//         $con = connexion();

//         // Requête pour récupérer les formules, leurs images, et les plats associés avec leurs images
//         $query = "
//             SELECT f.id AS formule_id, f.nom AS formule_nom, f.prix AS formule_prix, f.image AS formule_image,
//                    p.id AS plat_id, p.nom AS plat_nom, p.image AS plat_image
//             FROM formules f
//             LEFT JOIN formule_plat fp ON f.id = fp.formule_id
//             LEFT JOIN plats p ON fp.plat_id = p.id
//             ORDER BY f.id, p.nom";

//         $stmt = $con->prepare($query);
//         $stmt->execute();

//         $result = [];
//         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//             $formuleId = $row['formule_id'];
//             if (!isset($result[$formuleId])) {
//                 $result[$formuleId] = [
//                     'nom' => $row['formule_nom'],
//                     'prix' => $row['formule_prix'],
//                     'image' => $row['formule_image'],
//                     'plats' => []
//                 ];
//             }
//             if ($row['plat_id']) {
//                 $result[$formuleId]['plats'][] = [
//                     'id' => $row['plat_id'],
//                     'nom' => $row['plat_nom'],
//                     'image' => $row['plat_image']
//                 ];
//             }
//         }
//         return $result;
//     } catch (PDOException $e) {
//         echo "Erreur : " . $e->getMessage();
//         return [];
//     }
// }

function getAllFormulesWithPlatsAndImages()
{
    try {
        $con = connexion();

        // Requête pour récupérer les formules et leurs plats associés avec les informations nécessaires
        $query = "
            SELECT f.id AS formule_id, f.nom AS formule_nom, f.prix AS formule_prix, f.image AS formule_image,
                   p.id AS plat_id, p.nom AS plat_nom, p.prix AS plat_prix, p.type AS plat_type, p.image AS plat_image,
                   '' AS ingredients
            FROM formules f
            LEFT JOIN formule_plat fp ON f.id = fp.formule_id
            LEFT JOIN plats p ON fp.plat_id = p.id
            ORDER BY f.id, p.nom";

        $stmt = $con->prepare($query);
        $stmt->execute();

        $formules = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Créer la formule si elle n'existe pas encore
            if (!isset($formules[$row['formule_id']])) {
                $formules[$row['formule_id']] = new Formule(
                    $row['formule_id'],
                    $row['formule_nom'],
                    $row['formule_prix'],
                    $row['formule_image']
                );
            }

            // Créer le plat
            $plat = new Plat(
                $row['plat_id'],
                $row['plat_nom'],
                $row['plat_prix'],
                $row['plat_type'],
                $row['plat_image'],
                [] // Ingrédients vides
            );

            // Ajouter le plat à la formule
            $formules[$row['formule_id']]->ajouterPlat($plat);
        }

        return $formules;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}



$formules = getAllFormulesWithPlatsAndImages();
// var_dump($formules);
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
                <th>Image de la formule</th>
                <th>Nom de la formule</th>
                <th>Prix</th>
                <th>Plats associés</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formules as $formule): ?>
                <tr>
                    <td>
                        <?php if (!empty($formule->getImage())): ?>
                            <img src="../Assets/img/formules/<?php echo htmlspecialchars($formule->getImage()); ?>" alt="<?php echo htmlspecialchars($formule->getNom()); ?>" style="width: 100px; height: auto;">
                        <?php else: ?>
                            Pas d'image
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($formule->getNom()); ?></td>
                    <td><?php echo number_format($formule->getPrix(), 2); ?> €</td>
                    <td>
                        <?php if (!empty($formule->getPlats())): ?>
                            <ul>
                                <?php foreach ($formule->getPlats() as $plat): ?>
                                    <li>
                                        <img src="../Assets/img/<?php echo htmlspecialchars($plat->getImage()); ?>" alt="<?php echo htmlspecialchars($plat->getNom()); ?>" style="width: 50px; height: auto; margin-right: 5px;">
                                        <?php echo htmlspecialchars($plat->getNom()); ?> (<?php echo htmlspecialchars($plat->getType()); ?>) - <?php echo number_format($plat->getPrix(), 2); ?> €
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            Aucun plat associé.
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_formule.php?id=<?php echo $formule->getId(); ?>">Modifier</a> |
                        <a href="delete_formule.php?id=<?php echo $formule->getId(); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formule ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune formule trouvée.</p>
<?php endif; ?>