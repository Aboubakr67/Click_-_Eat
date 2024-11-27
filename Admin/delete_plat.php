<?php
require('../HeaderFooter/Admin/Header.php');
require_once('../Actions/zone_admin_repo.php');
require_once('../Actions/ft_extensions.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}


// Récupérer l'ID du plat à supprimer
$platId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($platId == 0) {
    $_SESSION['error'] = "Id du plat égale à 0";
    header("Location: liste_plats.php");
    exit;
}

// checkPlatAssociation($platId);

// Vérifier si le plat est associé à une des trois tables
if (checkPlatAssociation($platId)) {
    $_SESSION['error'] = "Ce plat ne peut pas être supprimé car il est associé à un contenu de commande, ou à une formule.";
    header("Location: liste_plats.php");
    exit;
}

exit;

// Si le plat n'est pas associé, procéder à la suppression
try {
    $con = connexion();

    // Récupérer le nom de l'image associée au plat avant la suppression
    $queryPlatImage = "SELECT image FROM plats WHERE id = :plat_id";
    $stmtPlatImage = $con->prepare($queryPlatImage);
    $stmtPlatImage->bindParam(':plat_id', $platId, PDO::PARAM_INT);
    $stmtPlatImage->execute();
    $platImage = $stmtPlatImage->fetchColumn();

    // Supprimer les associations du plat dans la table plat_ingredient
    $deletePlatIngredientQuery = "DELETE FROM plat_ingredient WHERE plat_id = :plat_id";
    $deleteStmt = $con->prepare($deletePlatIngredientQuery);
    $deleteStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Supprimer les associations dans la table formule_plat
    $deleteFormulePlatQuery = "DELETE FROM formule_plat WHERE plat_id = :plat_id";
    $deleteStmt = $con->prepare($deleteFormulePlatQuery);
    $deleteStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Supprimer les associations dans la table contenu_commande
    $deleteContenuCommandeQuery = "DELETE FROM contenu_commande WHERE plat_id = :plat_id";
    $deleteStmt = $con->prepare($deleteContenuCommandeQuery);
    $deleteStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Supprimer le plat dans la table plats
    $deletePlatQuery = "DELETE FROM plats WHERE id = :plat_id";
    $deleteStmt = $con->prepare($deletePlatQuery);
    $deleteStmt->bindParam(':plat_id', $platId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Suppression de l'image associée au plat dans le dossier Assets/img/
    $imagePath = '../Assets/img/' . $platImage;
    if (file_exists($imagePath)) {
        unlink($imagePath); // Supprimer l'image
    }

    $_SESSION['success'] = "Plat et image supprimés avec succès.";
    header("Location: liste_plats.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la suppression du plat : " . $e->getMessage();
    header("Location: liste_plats.php");
    exit;
}



?>


<h1>aaaaaaaa</h1>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>