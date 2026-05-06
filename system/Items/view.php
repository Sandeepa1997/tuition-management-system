<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();

// Fetch items
$sql = "SELECT * FROM items";
$result = $db->query($sql);
?>

<div class="container mt-4">
    <h4 class="mb-3">Item List</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Reorder Level</th>
                    <th>Image</th>
                    <th>Category ID</th>
                    <th>Sub-category ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $row['item_name'] ?></td>
                            <td><?= $row['reorder_level'] ?></td>
                            <td>
                                <img src="../uploads/<?= $row['item_image'] ?>" alt="Item Image" width="60" height="60" class="img-thumbnail">
                            </td>
                            <td><?= $row['category'] ?></td>
                            <td><?= $row['sub_category'] ?></td>
                            <td>
                                <a href="delete_item.php?id=<?= $row['Id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                <?php }
                } else { ?>
                    <tr>
                        <td colspan="7" class="text-center">No items found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
