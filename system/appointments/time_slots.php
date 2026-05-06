<?php
ob_start();
include '../../init.php';
if (!isset($_SESSION['ID'])) {
    header("Location:../../login.php");
}


$db = dbConn();
$userid = $_SESSION['ID'];

// Get the teacher ID based on the logged-in user's ID
$sql_teacher = "SELECT Id FROM teachers WHERE userid = '$userid'";
$result_teacher = $db->query($sql_teacher);

if ($result_teacher->num_rows == 1) {
    $teacher_row = $result_teacher->fetch_assoc();
    $teacherId = $teacher_row['Id'];
}

// Get classes assigned to this teacher
$sql = "SELECT
            c.Id,
            c.Class_Date,           
            G.name AS Grade,
            c.start_time,
            c.end_time
        FROM
            classes AS c
        JOIN grade_levels AS G ON c.Grade_Level_id = G.id
        WHERE
            c.Teacher_id = '$teacherId'";

$result = $db->query($sql);

// Get current month and year
$currentMonthName = date('F');
$currentYear = date('Y');      
?>



    <style>
        th,
        tbody {
            background-color: rgb(174, 230, 176);
        }

        h1 {
            color: darkgreen;
            margin-top: 30px;
        }
    </style>


<body>
    <div class="container mt-5">
        <h1>Class Schedule - <?= $currentYear ?> - <?=$currentMonthName?></h1>

        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title text-white">Assigned Classes</h3>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap text-center">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Class Date</th>
                            <th>Time Slot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['Grade'] ?></td>
                                    <td><?= $row['Class_Date'] ?></td>
                                  
                                    <td><?= date("g:i A", strtotime($row['start_time'])) ?> -
                                        <?= date("g:i A", strtotime($row['end_time'])) ?>
                                    </td>
                                </tr>
                        <?php }
                        } else {
                            echo "<tr><td colspan='4'>No classes assigned.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>





<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Time Slot</h3>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                extract($_POST);

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
                    $db = dbConn();
                    $sql = "INSERT INTO user_modules(UserId, ModuleId, Status) VALUES ('$Username','$Module','$Status')";
                    $db->query($sql);
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
                                    <option value="<?= $row_user['Id'] ?>"> <?= $row_user['FirstName'] . " " . $row_user['LastName'] ." - ".$row_user['user_role'] ?>
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
                            $sql_mod = "SELECT * FROM modules WHERE Status='1'";
                            $result_mod = $db->query($sql_mod);

                            if ($result_mod->num_rows > 0) {
                                while ($row_mod = $result_mod->fetch_assoc()) {
                                    ?>
                                    <option value="<?= $row_mod['id'] ?>"> <?= $row_mod['ModuleName'] ?>
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
                            <option value="1" class="text-success">Active</option>
                            <option value="0" class="text-danger">Inactive</option>
                        </select>
                        <span class="text-danger"><?= @$message['Status'] ?></span>
                    </div>
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success float-right">Add User Module</button>
        </div>
        </form>
    </div>

    <div class="col-md-8">
        
              
        <?php

        $db = dbConn();
        $sql_um = "SELECT r.Id, u.Firstname, u.LastName, u.user_role, m.ModuleName, r.Status FROM user_modules r
        LEFT JOIN modules m ON m.Id = r.ModuleId
        LEFT JOIN users u ON u.Id = r.UserId";
        $result_um = $db->query($sql_um);
        ?>


        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>User Role</th>
                    <th>Module Name</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_um->num_rows > 0) {
                    while ($row_um = $result_um->fetch_assoc()) {
                        ?>
                        <tr>
                            <td> <?= $row_um['Id'] ?> </td>
                            <td> <?= $row_um['Firstname'] ?>         <?= $row_um['LastName'] ?></td>
                            <td> <?= $row_um['user_role'] ?> </td>
                            <td> <?= $row_um['ModuleName'] ?> </td>
                            <td> <?= $row_um['Status'] ?> </td>
                            <td>
                                <form action="edit_user_modules.php" method="post" id="frmedit <?= $row_um['Id'] ?>">
                                    <input type="hidden" name="Id" id="Id <?= $row_um['Id'] ?>" value="<?= $row_um['Id'] ?>">
                                    <button type="submit" name="action" value="edit" class="btn btn-warning"><i
                                            class="fas fa-edit"></i></button>
                                </form>
                            </td>
                            <td>
                                <form action="delete_user_modules.php" method="post" id="frm<?= $row_um['Id'] ?>">
                                    <input type="hidden" name="Id" value="<?= $row_um['Id'] ?>">
                                    <input type="hidden" name="Status" value="<?= $row_um['Status'] == '1' ? '0' : '1' ?>">
                                    <!-- (condition) ? (value if true) : (value if false) ---- MEANING => $row['Status'] == 'active' ? if true'0' : if false'1'  -->
                                    <button type="submit"
                                        onclick="confirmStatusChange('<?= $row_um['Id'] ?>', '<?= $row_um['Status'] ?>')"
                                        class="btn btn-<?= $row_um['Status'] == '1' ? 'danger' : 'success' ?>">
                                        <?= $row_um['Status'] == '1' ? 'Deactivate' : 'Activate' ?>
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