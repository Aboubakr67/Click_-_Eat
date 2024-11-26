<?php
require('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE CUISINE') {
    header("Location: connexion.php");
    exit;
}

require("../Actions/zone_cuisine_repo.php");



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $commandeId = (int)$_POST['commande_id'];
    if (setCommandePrete($commandeId)) {
        $message = "La commande a été marquée comme prête.";
    } else {
        $error = "Erreur lors de la mise à jour de la commande.";
    }
}


?>

<h1>Commandes en cours</h1>

<?php if (isset($message)): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php elseif (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php
$commandes = getCommandes();

if (count($commandes) > 0): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Code Commande</th>
                <th>Créée il y a</th>
                <th>Total</th>
                <th>Méthode de Paiement</th>
                <th>Action</th>
                <th>Info Commande</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $commande):
                // Calcul du temps écoulé
                $dateCommande = new DateTime($commande['created_at']);
                $now = new DateTime();
                // Calcul du temps écoulé en secondes
                $intervalInSeconds = $now->getTimestamp() - $dateCommande->getTimestamp();

                // Conversion en heures, minutes et secondes
                $hours = floor($intervalInSeconds / 3600);
                $minutes = floor(($intervalInSeconds % 3600) / 60);
                $seconds = $intervalInSeconds % 60;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($commande['code_commande']); ?></td>
                    <td id="temps-<?php echo $commande['id']; ?>">
                        <?php
                        // Formatage initial du temps écoulé
                        $tempsEcoule = '';
                        if ($hours > 0) {
                            $tempsEcoule .= $hours . 'h ';
                        }
                        if ($minutes > 0 || $hours > 0) {
                            $tempsEcoule .= $minutes . 'm ';
                        }
                        $tempsEcoule .= $seconds . 's';
                        echo $tempsEcoule;
                        ?>
                    </td>
                    <td><?php echo number_format($commande['total'], 2, ',', ' ') . ' €'; ?></td>
                    <td><?php echo htmlspecialchars($commande['paiement_method']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
                            <button type="submit">Prête</button>
                        </form>
                    </td>
                    <td>
                        <a href="info_commande.php?commande_id=<?php echo $commande['id']; ?>">Voir détails</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Fonction pour mettre à jour le temps écoulé en fonction du temps réel
        function updateElapsedTime() {
            var commandes = <?php echo json_encode($commandes); ?>;

            commandes.forEach(function(commande) {
                var createdAt = new Date(commande.created_at);
                var now = new Date();
                var elapsedTimeInSeconds = Math.floor((now - createdAt) / 1000); // Temps écoulé en secondes

                var hours = Math.floor(elapsedTimeInSeconds / 3600);
                var minutes = Math.floor((elapsedTimeInSeconds % 3600) / 60);
                var seconds = elapsedTimeInSeconds % 60;

                var timeString = '';
                if (hours > 0) {
                    timeString += hours + 'h ';
                }
                if (minutes > 0 || hours > 0) {
                    timeString += minutes + 'm ';
                }
                timeString += seconds + 's';

                // Mise à jour de l'affichage du temps
                document.getElementById('temps-' + commande.id).textContent = timeString;
            });
        }

        // Appeler la fonction pour mettre à jour toutes les secondes
        setInterval(updateElapsedTime, 1000);
    </script>


<?php else: ?>
    <p>Aucune commande en cours.</p>
<?php endif; ?>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>