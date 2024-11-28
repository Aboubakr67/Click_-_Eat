<?php

require_once 'zone_stock_repo.php';

try {

    $ingredients = getStockRealTime();

    echo json_encode($ingredients);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
