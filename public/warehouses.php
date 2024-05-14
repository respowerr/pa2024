<?php 
include_once('maintenance_check.php');

if (!isset($_SESSION['accessToken'])) {
    header("Location: login.php");
    exit;
}

$baseUrl = "http://ddns.callidos-mtf.fr:8080/warehouse";
$authHeader = "Authorization: Bearer " . $_SESSION['accessToken'];

function makeHttpRequest($url, $method, $data = null) {
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\n" . $GLOBALS['authHeader'] . "\r\n",
            "method" => $method
        ]
    ];

    if ($data !== null) {
        $options['http']['content'] = json_encode($data);
    }

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result === FALSE ? [] : json_decode($result, true);
}

$warehouses = makeHttpRequest($baseUrl, "GET");
$selectedWarehouse = null;
$operationMessage = "";

if (isset($_GET['warehouse_id'])) {
    $selectedWarehouse = makeHttpRequest($baseUrl . "/" . $_GET['warehouse_id'], "GET");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $warehouseId = $_POST['warehouse_id'] ?? null;

    if (isset($_POST['update_stock'])) {
        $updatedData = ['current_stock' => $_POST['current_stock']];
        $operationMessage = makeHttpRequest($baseUrl . "/$warehouseId", "PUT", $updatedData);
    } elseif (isset($_POST['add_item'])) {
        $newItemData = ['itemName' => $_POST['itemName'], 'count' => $_POST['count']];
        $operationMessage = makeHttpRequest($baseUrl . "/$warehouseId", "POST", $newItemData);
    } elseif (isset($_POST['modify_item'])) {
        $modifyItemData = ['item_id' => $_POST['item_id'], 'itemName' => $_POST['itemName'], 'count' => $_POST['count']];
        $operationMessage = makeHttpRequest($baseUrl . "/{location}", "PUT", $modifyItemData);
    } elseif (isset($_POST['delete_item'])) {
        $deleteItemData = ['item_id' => $_POST['item_id']];
        $operationMessage = makeHttpRequest($baseUrl . "/{location}", "DELETE", $deleteItemData);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Warehouses - ATD</title>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/head.php'); ?>
</head>
<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
        <main>
            <div class="content">
                <div class="container">
                    <h1 class="title">Warehouse Management</h1>
                    
                    <?php if ($operationMessage): ?>
                        <p><?= htmlspecialchars($operationMessage); ?></p>
                    <?php endif; ?>

                    <?php if ($selectedWarehouse): ?>
                        <h2>Details for Warehouse #<?= htmlspecialchars($selectedWarehouse['warehouse_id']); ?></h2>
                        <p>Location: <?= htmlspecialchars($selectedWarehouse['location']); ?></p>
                        <p>Capacity: <?= htmlspecialchars($selectedWarehouse['rack_capacity']); ?></p>
                        <p>Current Stock: <?= htmlspecialchars($selectedWarehouse['current_stock']); ?></p>
                        <p>Utilization: <?= htmlspecialchars($selectedWarehouse['utilization']); ?>%</p>
                    <?php else: ?>
                        <h2>All Warehouses</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Location</th>
                                    <th>Capacity</th>
                                    <th>Current Stock</th>
                                    <th>Utilization</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($warehouse['warehouse_id']); ?></td>
                                        <td><?= htmlspecialchars($warehouse['location']); ?></td>
                                        <td><?= htmlspecialchars($warehouse['rack_capacity']); ?></td>
                                        <td><?= htmlspecialchars($warehouse['current_stock']); ?></td>
                                        <td><?= htmlspecialchars($warehouse['utilization']); ?>%</td>
                                        <td><a href="?warehouse_id=<?= $warehouse['warehouse_id']; ?>">View Details</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>
    </div>
</body>
</html>
