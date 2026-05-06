<?php
ob_start();
include '../../init.php';

$where = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);

    if (!empty($regno)) {
        $where .= " reg_no = '$regno' AND";
    }
    if (!empty($email)) {
        $where .= " Email = '$email' AND";
    }
    if (!empty($lastname)) {
        $where .= " LastName = '$lastname' AND";
    }
 

   



    if (!empty($where)) {
        $where = substr($where, 0, -3);
        $where = " WHERE" . $where;
    }
}

// Connect to database
$db = dbConn();

// SQL query to get enrolled students with their info
$sql = "SELECT 
            
            s.id AS student_id,
            u.FirstName,
            u.LastName,
            u.Email,
            u.Profile_Picture,
            u.Primary_Contact,
            s.reg_no
        FROM 
            students s
            INNER JOIN 
            users u ON s.userid = u.Id
         
            $where
             ORDER BY 
            s.id DESC";

$result = $db->query($sql);
?>

<html>

<head>
    <style>
        th,
        tbody {
            background-color: rgb(174, 230, 176);
        }
    </style>
</head>

<div class="row">
    <div class="col-md-12">
        <h1>Student Enrollments</h1>
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">Enrollment List</h3>
            </div>
            <div class="card-body table-responsive p-0">

                <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <label for="">Enter Reg No</label>
                    <input type="regno" name="regno" id="regno">

                    <label for="">Enter Email Address</label>
                    <input type="text" name="email" id="email">

                    <label for=""> Enter Last Name</label>
                    <input type="text" name="lastname" id="lastname">
                



                    <button type="submit">Search</button>
                </form>
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Reg-No</th>
                            <th>Student ID</th>
                            <th> Name</th>
                            <th>Profile Picture</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Notice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>

                                <td><?= $row['reg_no'] ?></td>
                                <td><?= $row['student_id'] ?></td>
                                <td><?= $row['FirstName'] ?> <?= $row['LastName'] ?></td>
                                
                                <td><img src="../uploads/<?= $row['Profile_Picture'] ?>" width="100"></td>
                                <td>
                                    <form action="view_full.php" method="post" id="frmview<?= $row['student_id'] ?>">
                                        <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                    
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>

                                </td>
                                <td>
                                    <form action="delete.php" method="post" id="frm<?= $row['student_id'] ?>">
                                        <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                        <button type="button" onclick="confirmDelete(<?= $row['student_id'] ?>)" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                                <td>
                                    <form action="performance.php" method="post" >
                                           <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                    <button type="submit" class="btn btn-primary">View Performance</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="notice.php" method="post" >
                                           <input type="text" name="student_id" value="<?= $row['student_id'] ?>">
                                    <button type="submit" class="btn btn-primary">Notice</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        let result = confirm("Are you sure you want to delete?");
        if (result) {
            document.getElementById('frm' + id).submit();
        }
    }
</script>

</html>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>