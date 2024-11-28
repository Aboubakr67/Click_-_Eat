<?php
require_once('../Actions/zone_admin_repo.php');
require_once('../HeaderFooter/Admin/Header.php');

// Vérifier si l'ID de la formule est passé en URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $formuleId = $_GET['id'];

    // Récupérer les données de la formule à supprimer
    try {
        $con = connexion();

        // Récupérer l'image de la formule avant suppression
        $query = "SELECT image FROM formules WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $formuleId, PDO::PARAM_INT);
        $stmt->execute();
        $formule = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$formule) {
            $_SESSION['error'] = "Formule introuvable.";
            header("Location: liste_formules.php");
            exit;
        }

        // Supprimer les associations dans 'formule_plat'
        $queryDeletePlats = "DELETE FROM formule_plat WHERE formule_id = :formule_id";
        $stmtDeletePlats = $con->prepare($queryDeletePlats);
        $stmtDeletePlats->bindParam(':formule_id', $formuleId, PDO::PARAM_INT);
        $stmtDeletePlats->execute();

        // Supprimer la formule dans 'formules'
        $queryDeleteFormule = "DELETE FROM formules WHERE id = :id";
        $stmtDeleteFormule = $con->prepare($queryDeleteFormule);
        $stmtDeleteFormule->bindParam(':id', $formuleId, PDO::PARAM_INT);
        $stmtDeleteFormule->execute();

        // Supprimer l'image du dossier (si elle existe)
        if (file_exists('../Assets/img/formules/' . $formule['image'])) {
            unlink('../Assets/img/formules/' . $formule['image']);
        }

        $_SESSION['success'] = "Formule supprimée avec succès.";
        header("Location: liste_formules.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la suppression de la formule : " . $e->getMessage();
        header("Location: liste_formules.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID de la formule manquant.";
    header("Location: liste_formules.php");
    exit;
}
