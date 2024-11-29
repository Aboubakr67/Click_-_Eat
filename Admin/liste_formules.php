<?php
require_once('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_admin_repo.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}

function getAllFormulesWithPlatsAndImages()
{
    try {
        $con = connexion();
        $query = "
            SELECT f.id AS formule_id, f.nom AS formule_nom, f.prix AS formule_prix, f.image AS formule_image,
                   p.id AS plat_id, p.nom AS plat_nom, p.prix AS plat_prix, p.type AS plat_type, p.image AS plat_image
            FROM formules f
            LEFT JOIN formule_plat fp ON f.id = fp.formule_id
            LEFT JOIN plats p ON fp.plat_id = p.id
            ORDER BY f.id, p.nom";

        $stmt = $con->prepare($query);
        $stmt->execute();

        $formules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($formules[$row['formule_id']])) {
                $formules[$row['formule_id']] = new Formule(
                    $row['formule_id'],
                    $row['formule_nom'],
                    $row['formule_prix'],
                    $row['formule_image']
                );
            }

            if ($row['plat_id']) {
                $plat = new Plat(
                    $row['plat_id'],
                    $row['plat_nom'],
                    $row['plat_prix'],
                    $row['plat_type'],
                    $row['plat_image'],
                    []
                );
                $formules[$row['formule_id']]->ajouterPlat($plat);
            }
        }

        return array_values($formules);
    } catch (PDOException $e) {
        error_log("Erreur dans getAllFormulesWithPlatsAndImages: " . $e->getMessage());
        return [];
    }
}

$formules = getAllFormulesWithPlatsAndImages();
?>

<div class="flex">
    <div class="p-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Liste des Formules</h1>
            <a href="create_formule.php" class="px-4 py-2 bg-[#D84315] text-white rounded-lg hover:bg-[#BF360C] transition-colors">
                Créer une formule
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Formules Grid -->
        <div class="grid grid-cols-2 gap-6">
            <?php foreach ($formules as $formule): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <!-- Formule Header -->
                        <div class="flex items-start gap-4 mb-4">
                            <img src="../Assets/img/formules/<?php echo htmlspecialchars($formule->getImage()); ?>"
                                alt="<?php echo htmlspecialchars($formule->getNom()); ?>"
                                class="w-32 h-32 object-cover rounded-lg">
                            <div class="flex-1">
                                <h2 class="text-xl font-medium"><?php echo htmlspecialchars($formule->getNom()); ?></h2>
                                <p class="text-[#D84315] font-medium mt-1"><?php echo number_format($formule->getPrix(), 2); ?> €</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="edit_formule.php?id=<?php echo $formule->getId(); ?>"
                                    class="text-[#D84315] hover:text-[#BF360C]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="delete_formule.php?id=<?php echo $formule->getId(); ?>"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formule ?');"
                                    class="text-red-600 hover:text-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Plats de la formule -->
                        <?php if (!empty($formule->getPlats())): ?>
                            <div class="space-y-3">
                                <?php foreach ($formule->getPlats() as $plat): ?>
                                    <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg">
                                        <img src="../Assets/img/<?php echo htmlspecialchars($plat->getImage()); ?>"
                                            alt="<?php echo htmlspecialchars($plat->getNom()); ?>"
                                            class="w-12 h-12 object-cover rounded">
                                        <div>
                                            <p class="font-medium"><?php echo htmlspecialchars($plat->getNom()); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($plat->getType()); ?></p>
                                        </div>
                                        <span class="ml-auto text-gray-600"><?php echo number_format($plat->getPrix(), 2); ?> €</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">Aucun plat associé.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($formules)): ?>
            <div class="text-center py-8 text-gray-500">
                <p>Aucune formule trouvée.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>