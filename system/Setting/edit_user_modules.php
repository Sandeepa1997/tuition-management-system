<?php
ob_start();
include '../../init.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit User Module Permission</h3>
            </div>

            <!-- /.card-header -->
            <?php
            // print_r($_POST);
            
            extract($_POST);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == 'edit') {
                $db = dbConn();
                $sql = "SELECT * FROM user_modules WHERE Id='$Id'";
                $result = $db->query($sql);

                $row = $result->fetch_assoc();

                $Module = $row['ModuleId'];
                $Username = $row['UserId'];
                $Status = $row['Status'];

                $Id = $row['Id']; //comes from the submit button input field's name='Id'
            
            }

            //Checks the form submit method (whether its post or get)
            if ($_SERVER['REQUEST_METHOD'] == "POST" && @$action == 'update') {


                //Form required fields validation
                $messasge = array();


                if (!isset($Module)) {
                    $message['Module'] = "The Module Name should be selected!";
                }
                if (!isset($Username)) {
                    $message['Username'] = "The User should be selected!";
                }
                if (!isset($Status)) {
                    $message['Status'] = "The Status should be selected!";
                }


                if (!empty($Module) && !empty($Username)) {
                    $db = dbConn();
                    $sql = "SELECT * FROM user_modules WHERE ModuleId='$Module' AND UserId='$Username'";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        $message['Module'] = "The User Module already exists!";
                    }
                }

                if (empty($message)) {

                    //Database connection
                    $db = dbConn();
                    $sql = "UPDATE user_modules SET ModuleId='$Module', UserId='$Username', Status='$Status' WHERE Id='$Id'";
                    $db->query($sql);

                    header('Location:user_modules.php');
                    exit();
                }
            }
            ?>



            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                <div class="card-body">
                    <div class="form-group col-md-6 pb-2">
                        <label for="Username">User</label>
                        <select class="form-control" name="Username" id="Username">
                            <option value="">--</option>
                            <?php
                            $db = dbConn();
                            $sql_user = "SELECT * FROM users WHERE user_role != 'Student' AND user_role != 'parent'";
                            $result_user = $db->query($sql_user);

                            if ($result_user->num_rows > 0) {
                                while ($row_user = $result_user->fetch_assoc()) {
                                    ?>
                                    <option value="<?= $row_user['Id'] ?>" <?php if (isset($Username) && $Username == $row_user['Id']) {
                                          echo 'selected';
                                      } ?>> <?= $row_user['FirstName'] . " " . $row_user['LastName'] . " - " . $row_user['user_role'] ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <span class="text-danger"><?= @$message['Username'] ?></span>
                    </div>
                    <div class="form-group col-md-6 pb-2">
                        <label for="Module">Module</label>
                        <select class="form-control" name="Module" id="Module">
                            <option value="">--</option>
                            <?php
                            $db = dbConn();
                            $sql_mod = "SELECT * FROM modules";
                            $result_mod = $db->query($sql_mod);

                            if ($result_mod->num_rows > 0) {
                                while ($row_mod = $result_mod->fetch_assoc()) {
                                    ?>
                                    <option value="<?= $row_mod['id'] ?>" <?php if (isset($Module) && $Module == $row_mod['id']) {
                                          echo 'selected';
                                      } ?>> <?= $row_mod['ModuleName'] ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <span class="text-danger"><?= @$message['Module'] ?></span>
                    </div>
                    <div class="form-group col-md-6 pb-2">
                        <label for="Status">Status</label>
                        <select class="form-control" name="Status">
                            <option value="">--</option>
                            <option value="1" class="text-success" <?php if (isset($Status) && $Status == '1') {
                                echo 'selected';
                            } ?>>Active</option>
                            <option value="0" class="text-danger" <?php if (isset($Status) && $Status == '0') {
                                echo 'selected';
                            } ?>>Inactive</option>
                        </select>
                        <span class="text-danger"><?= @$message['Status'] ?></span>
                    </div>
                </div>
        </div>
        <div class="card-footer">
            <input type="hidden" name="Id" id="Id" value="<?= $Id ?>">
            <button type="submit" name="action" value="update" class="btn btn-success float-right">Update
                permission</button>
        </div>
        </form>
    </div>
</div>
</div>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>