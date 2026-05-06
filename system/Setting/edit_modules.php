<?php
ob_start();
include '../../init.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Module</h3>
            </div>

            <!-- /.card-header -->
            <?php
         
            
            extract($_POST);

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == 'edit') {
                $db = dbConn();
                $sql = "SELECT * FROM modules WHERE id='$Id'";
                $result = $db->query($sql);

                $row = $result->fetch_assoc();

                $Module = $row['ModuleName'];
                $Path = $row['path'];
                $File = $row['File'];
                $Level = $row['Level'];
                $Status = $row['Status'];

                $Id = $row['id']; //comes from the submit button input field's name='id'
            
            }

            //Checks the form submit method (whether its post or get)
            if ($_SERVER['REQUEST_METHOD'] == "POST" && @$action == 'update') {



                /*The below dataClean function is defined in the function.php page 
                Dataclean only the text input fields (not the password, dropdown,radio buttons,checkboxes) */
                $Module = dataClean($Module);
                $Path = dataClean($path);
                $File = dataClean($File);



                //Form required fields validation
                $messasge = array();

                if (empty($Module)) {
                    $message['Module'] = "The Module Name should be filled!";
                }
                if (empty($path)) {
                    $message['path'] = "The Path should be filled!";
                }
                if (!isset($Level)) {
                    $message['Level'] = "The Level should be selected!";
                }
                if (!isset($Status)) {
                    $message['Status'] = "The Status should be selected!";
                }

                // if (!empty($Module) && !empty($Path) && !empty($File)) {
                //     $db = dbConn();
                //     $sql = "SELECT * FROM modules WHERE ModuleName='$Module' AND Path='$Path' AND File='$File'";
                //     $result = $db->query($sql);

                //     if ($result->num_rows > 0) {
                //         $message['Module'] = "The Module already exists!";
                //     }
                // }

                if (empty($message)) {

                    //Database connection
                    $db = dbConn();
                    $sql = "UPDATE modules SET ModuleName='$Module', path='$path', File='$File', Level='$Level', Status='$Status' WHERE id='$Id'";
                    $db->query($sql);

                    header('Location:modules.php');
                    exit();
                }
            }
            ?>



            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                <div class="card-body">
                    <div class="form-group">
                        <label for="Module" class="form-label">Module Name</label>
                        <input type="text" class="form-control" placeholder="Enter Module Name" id="Module"
                            name="Module" value="<?= @$Module ?>">
                        <span class="text-danger"><?= @$message['Module'] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="Path" class="form-label">Path</label>
                        <input type="text" class="form-control" placeholder="Enter Path" id="path" name="path"
                            value="<?= @$path ?>">
                        <span class="text-danger"><?= @$message['path'] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="File" class="form-label">File</label>
                        <input type="text" class="form-control" placeholder="Enter File Name" id="File" name="File"
                            value="<?= @$File ?>">
                        <span class="text-danger"><?= @$message['File'] ?></span>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 pb-2">
                            <label for="Level">Level</label>
                            <select class="form-control" name="Level">
                                <option value="">--</option>
                                <option value="1" <?php if (isset($Level) && $Level == '1') {
                                    echo 'selected';
                                } ?>>1</option>
                                <option value="2" <?php if (isset($Level) && $Level == '2') {
                                    echo 'selected';
                                } ?>>2</option>
                            </select>
                            <span class="text-danger"><?= @$message['Level'] ?></span>
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
                    <input type="hidden" name="id" id="id" value="<?= $id ?>">
                    <button type="submit" name="action" value="update" class="btn btn-success float-right">Update
                        Module</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>