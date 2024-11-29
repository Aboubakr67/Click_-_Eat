<?php
require_once('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE CUISINE') {
    header("Location: connexion.php");
    exit;
}

require_once("../Actions/zone_cuisine_repo.php");


// Récupérer l'ID de la commande depuis l'URL
if (isset($_GET['commande_id'])) {
    $commande_id = $_GET['commande_id'];
    $commandeDetails = getCommandeDetails($commande_id);
} else {
    echo "Commande non spécifiée.";
    exit;
}
?>

<div class="container">
    <h2>Détails de la commande #<?php echo htmlspecialchars($commande_id); ?></h2>

    <!-- Si des détails de la commande existent -->
    <?php if ($commandeDetails): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Plat</th>
                    <th>Modifications</th>
                    <th>Prix Supplément</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandeDetails as $detail): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detail['plat_name']); ?></td>
                        <td>
                            <?php
                            // Vérifier si modifications existe et n'est pas NULL
                            if ($detail['modifications'] && $detail['modifications'] != 'NULL') {
                                // Décoder le JSON
                                $modifications = json_decode($detail['modifications'], true);

                                // Vérifier si le JSON a été correctement décodé
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $modificationText = '';
                                    if (isset($modifications['ajouts']) && count($modifications['ajouts']) > 0) {
                                        $modificationText .= 'Ajouts : ' . implode(', ', $modifications['ajouts']) . '. ';
                                    }
                                    if (isset($modifications['suppression']) && count($modifications['suppression']) > 0) {
                                        $modificationText .= 'Suppression : ' . implode(', ', $modifications['suppression']) . '.';
                                    }
                                    echo $modificationText ? $modificationText : 'Aucune modification';
                                } else {
                                    echo 'Erreur de format JSON';
                                }
                            } else {
                                echo 'Aucune modification';
                            }
                            ?>
                        </td>
                        <td><?php echo number_format($detail['prix_supplément'], 2, ',', ' ') . ' €'; ?></td>
                        <td><?php echo htmlspecialchars($detail['quantite']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun détail trouvé pour cette commande.</p>
    <?php endif; ?>

    <!-- Lien pour revenir à la page principale des commandes -->
    <a href="zone_cuisine.php" class="btn btn-primary">Retour à la commande</a>
</div>