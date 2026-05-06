<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header("Location: ../login.php");
    exit;
}

$db = dbConn();

// Join item_inventory with items and suppliers
$sql = "SELECT ii.*, i.item_name, s.StoreName 
        FROM item_inventory ii
        INNER JOIN items i ON ii.item_id = i.Id
        INNER JOIN suppliers s ON ii.supplier_id = s.Id
        ORDER BY ii.date DESC";

$result = $db->query($sql);
?>

<div class="container mt-4">
    <h4 class="mb-3">Inventory Items</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Supplier</th>
                    <th>Unit Price (Rs.)</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Serial Number</th>
                    <th>Warranty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $row['item_name'] ?></td>
                            <td><?= $row['StoreName'] ?></td>
                            <td><?= number_format($row['unit_price'], 2) ?></td>
                            <td><?= $row['qty'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['serial_number'] ?: '-' ?></td>
                            <td><?= $row['warranty_period'] ?: '-' ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">No inventory records found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
