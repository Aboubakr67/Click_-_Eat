<?php
require_once('Databases.php');
require_once('zone_admin_repo.php');

header('Content-Type: application/json');

// Recevoir les données JSON
$data = json_decode(file_get_contents('php://input'), true);

try {
    $con = connexion();
    $con->beginTransaction();

    // Générer un code de commande unique
    //$code_commande = 'CMD' . uniqid();
    $code_commande = generateCodeCommande($data['paymentMethod']);;

    // Insérer la commande principale
    $stmt = $con->prepare("
        INSERT INTO commandes (
            created_at,
            statut,
            total,
            paiement_method,
            code_commande
        ) VALUES (
            NOW(),
            'EN COURS',
            :total,
            :paiement_method,
            :code_commande
        )
    ");

    $stmt->execute([
        'total' => $data['total'],
        'paiement_method' => $data['paymentMethod'],
        'code_commande' => $code_commande
    ]);

    $commande_id = $con->lastInsertId();

    // Insérer les menus
    if (!empty($data['items'])) {
        foreach ($data['items'] as $item) {
            // Insérer le plat principal du menu
            $modifications = [];
            if (!empty($item['removedIngredients'])) {
                $modifications['suppression'] = array_map(function($ing) {
                    return $ing['name'];
                }, $item['removedIngredients']);
            }

            $stmt = $con->prepare("
                INSERT INTO contenu_commande (
                    commande_id,
                    plat_id,
                    quantite,
                    modifications
                ) VALUES (
                    :commande_id,
                    :plat_id,
                    1,
                    :modifications
                )
            ");

            $stmt->execute([
                'commande_id' => $commande_id,
                'plat_id' => $item['id'],
                'modifications' => json_encode($modifications)
            ]);

            // Insérer les entrées du menu
            if (!empty($item['entrees'])) {
                foreach ($item['entrees'] as $entree) {
                    $stmt = $con->prepare("
                        INSERT INTO contenu_commande (
                            commande_id,
                            plat_id,
                            quantite
                        ) VALUES (
                            :commande_id,
                            :plat_id,
                            :quantite
                        )
                    ");

                    $stmt->execute([
                        'commande_id' => $commande_id,
                        'plat_id' => $entree['id'],
                        'quantite' => $entree['quantity']
                    ]);
                }
            }

            // Insérer les boissons du menu
            if (!empty($item['boissons'])) {
                foreach ($item['boissons'] as $boisson) {
                    $stmt = $con->prepare("
                        INSERT INTO contenu_commande (
                            commande_id,
                            plat_id,
                            quantite
                        ) VALUES (
                            :commande_id,
                            :plat_id,
                            :quantite
                        )
                    ");

                    $stmt->execute([
                        'commande_id' => $commande_id,
                        'plat_id' => $boisson['id'],
                        'quantite' => $boisson['quantity']
                    ]);
                }
            }
        }
    }

    // Insérer les entrées standalone
    if (!empty($data['entrees'])) {
        foreach ($data['entrees'] as $entree) {
            $stmt = $con->prepare("
                INSERT INTO contenu_commande (
                    commande_id,
                    plat_id,
                    quantite
                ) VALUES (
                    :commande_id,
                    :plat_id,
                    :quantite
                )
            ");

            $stmt->execute([
                'commande_id' => $commande_id,
                'plat_id' => $entree['id'],
                'quantite' => $entree['quantity']
            ]);
        }
    }

    // Insérer les boissons standalone
    if (!empty($data['boissons'])) {
        foreach ($data['boissons'] as $boisson) {
            $stmt = $con->prepare("
                INSERT INTO contenu_commande (
                    commande_id,
                    plat_id,
                    quantite
                ) VALUES (
                    :commande_id,
                    :plat_id,
                    :quantite
                )
            ");

            $stmt->execute([
                'commande_id' => $commande_id,
                'plat_id' => $boisson['id'],
                'quantite' => $boisson['quantity']
            ]);
        }
    }

    // Insérer les desserts standalone
    if (!empty($data['desserts'])) {
        foreach ($data['desserts'] as $dessert) {
            $stmt = $con->prepare("
                INSERT INTO contenu_commande (
                    commande_id,
                    plat_id,
                    quantite
                ) VALUES (
                    :commande_id,
                    :plat_id,
                    :quantite
                )
            ");

            $stmt->execute([
                'commande_id' => $commande_id,
                'plat_id' => $dessert['id'],
                'quantite' => $dessert['quantity']
            ]);
        }
    }

    // Récupérer les ingrédients nécessaires
    $ingredients = getIngredientsForCommande($commande_id);

    // Mettre à jour le stock et l'historique
    updateStockAndAddToHistory($ingredients);

    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Commande enregistrée avec succès',
        'commande_id' => $commande_id,
        'code_commande' => $code_commande,
        'data' => $data
    ]);

} catch (Exception $e) {
    if ($con) {
        $con->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement de la commande',
        'error' => $e->getMessage()
    ]);
}
?>
