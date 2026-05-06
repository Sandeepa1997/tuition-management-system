<?php
ob_start();
include '../../init.php';
if (!isset($_SESSION['ID'])) {
    header("Location:../../login.php");
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Module</h3>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                extract($_POST);

                $Module = dataClean($Module);

                $messasge = array();

                if (empty($Module)) {
                    $message['Module'] = "The Module Name should be filled!";
                }
                if (empty($Path)) {
                    $message['Path'] = "The Path should be filled!";
                }
                if (!isset($Level)) {
                    $message['Level'] = "The Level should be selected!";
                }
                if (!isset($Status)) {
                    $message['Status'] = "The Status should be selected!";
                }


                if (!empty($Module) && !empty($Path) && !empty($File)) {
                    $db = dbConn();
                    $sql = "SELECT * FROM modules WHERE ModuleName='$Module' AND Path='$Path' AND File='$File'";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        $message['Module'] = "The Module already exists!";
                    }
                }

                if (empty($message)) {
                    $db = dbConn();
                    $sql = "INSERT INTO modules(ModuleName, path, File, Level, Status) VALUES ('$Module','$Path','$File','$Level','$Status')";
                    $db->query($sql);
                }
            }

            // if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //     extract($_POST);

            //     $db = dbConn();
            //     $sql = "UPDATE modules SET Status='$Status' WHERE Id='$Id'";
            //     $db->query($sql);

            //     header("Location:modules.php");

            // }
            ?>

            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                <div class="card-body">
                    <div class="form-group">
                        <label for="Module" class="form-label">Module Name</label>
                        <input type="text" class="form-control" placeholder="Enter Module Name" id="Module"
                            name="Module">
                        <span class="text-danger"><?= @$message['Module'] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="Path" class="form-label">Path</label>
                        <input type="text" class="form-control" placeholder="Enter Path" id="Path" name="Path">
                        <span class="text-danger"><?= @$message['Path'] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="File" class="form-label">File</label>
                        <input type="text" class="form-control" placeholder="Enter File Name" id="File" name="File">
                        <span class="text-danger"><?= @$message['File'] ?></span>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 pb-2">
                            <label for="Level">Level</label>
                            <select class="form-control" name="Level">
                                <option value="">--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                            <span class="text-danger"><?= @$message['Level'] ?></span>
                        </div>
                        <div class="form-group col-md-6 pb-2">
                            <label for="Status">Status</label>
                            <select class="form-control" name="Status">
                                <option value="">--</option>
                                <option value="1" class="text-success">Active</option>
                                <option value="0" class="text-danger">Inactive</option>
                            </select>
                            <span class="text-danger"><?= @$message['Status'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right">Add Module</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <?php
        $db = dbConn();
        $sql = "SELECT * FROM modules";
        $result = $db->query($sql);
        ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Module Name</th>
                    <th>Path</th>
                    <th>File</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td> <?= $row['id'] ?> </td>
                            <td> <?= $row['ModuleName'] ?> </td>
                            <td> <?= $row['path'] ?> </td>
                            <td> <?= $row['File'] ?> </td>
                            <td> <?= $row['Level'] ?> </td>
                            <td> <?= $row['Status'] ?> </td>
                            <td>
                                <form action="edit_modules.php" method="post" id="frmedit <?= $row['id'] ?>">
                                    <input type="hidden" name="Id" id="Id <?= $row['id'] ?>" value="<?= $row['id'] ?>">
                                    <button type="submit" name="action" value="edit" class="btn btn-warning"><i
                                            class="fas fa-edit"></i></button>
                                </form>
                            </td>
                            <td>
                                <form action="delete_modules.php" method="post" id="frm<?= $row['id'] ?>">
                                    <input type="hidden" name="Id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="Status" value="<?= $row['Status'] == '1' ? '0' : '1' ?>">
                                    <!-- (condition) ? (value if true) : (value if false) ---- MEANING => $row['Status'] == 'active' ? if true'0' : if false'1'  -->
                                    <button type="submit"
                                        onclick="confirmStatusChange('<?= $row['id'] ?>', '<?= $row['Status'] ?>')"
                                        class="btn btn-<?= $row['Status'] == '1' ? 'danger' : 'success' ?>">
                                        <?= $row['Status'] == '1' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
    function confirmStatusChange(id, status) {
        if (status === '1') {
            // If current status is 1 (active), confirm deactivation
            let confirmDeactivate = confirm("Are you sure you want to deactivate this module?");
            if (confirmDeactivate) {
                document.getElementById('frm' + id).submit();
            }
        } else {
            // If current status is 0 (inactive), just submit to activate
            document.getElementById('frm' + id).submit();
        }
    }
</script>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>