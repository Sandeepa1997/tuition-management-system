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
    if (!empty($grade_id)) {
        $where .= " c.Grade_Level_id = '$grade_id' AND";
    }

     if (!empty($subject)) {
        $where .= " c.Subject_id = '$subject' AND";
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
            se.id AS enroll_id,
            s.id AS student_id,
            u.FirstName,
            u.LastName,
            u.Email,
            u.Profile_Picture,
            u.Primary_Contact,
            se.date,
            se.class_id,
            c.Class_Name,
            s.reg_no
        FROM 
            student_enroll se
        LEFT JOIN 
            students s ON se.student_id = s.Id
            LEFT JOIN 
            users u ON s.userid = u.Id
             LEFT JOIN 
            classes c ON se.class_id = c.Id
            $where
             ORDER BY 
            se.id DESC";

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
                    <div class="mb-3">

                        <label for="grade" class="form-label">Grade</label>
                        <select class="form-control" id="grade_id" name="grade_id">
                            <option value="">Select the Grade</option>
                            <?php

                            $db = dbConn();
                            $sqlgrade = "SELECT * FROM grade_levels";
                            $resultgrade = $db->query($sqlgrade);
                            if ($resultgrade->num_rows > 0) {
                                while ($rowgrade = $resultgrade->fetch_assoc()) {
                            ?>
                                    <option value="<?= $rowgrade['id'] ?>" <?= $rowgrade['id'] ==  @$grade_id ? 'selected' : '' ?>><?= $rowgrade['name'] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <select class="form-control" id="subject" name="subject">
                            <option value="">Select the Subject</option>
                            <?php
                            $db = dbConn();
                            $sqlsubject = "SELECT * FROM subjects";
                            $resultsubject = $db->query($sqlsubject);
                            if ($resultsubject->num_rows > 0) {
                                while ($rowsubject = $resultsubject->fetch_assoc()) {
                            ?>
                                    <option value="<?= $rowsubject['id'] ?>" <?= $rowsubject['id'] == @$subject ? 'selected' : '' ?>>
                                        <?= $rowsubject['name'] ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>



                    <button type="submit">Search</button>
                </form>
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Reg-No</th>
                            <th>Student ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>

                            <th>Enrolled Date</th>
                            <th>Class Name</th>
                            <th>Profile Picture</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>

                                <td><?= $row['reg_no'] ?></td>
                                <td><?= $row['student_id'] ?></td>
                                <td><?= $row['FirstName'] ?></td>
                                <td><?= $row['LastName'] ?></td>


                                <td><?= $row['date'] ?></td>
                                <td><?= $row['Class_Name'] ?></td>
                                <td><img src="../uploads/<?= $row['Profile_Picture'] ?>" width="100"></td>
                                <td>
                                    <form action="view_full.php" method="post" id="frmview<?= $row['enroll_id'] ?>">
                                        <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                        <input type="hidden" name="enroll_id" value="<?= $row['enroll_id'] ?>">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>

                                </td>
                                <td>
                                    <form action="delete.php" method="post" id="frm<?= $row['enroll_id'] ?>">
                                        <input type="hidden" name="enroll_id" value="<?= $row['enroll_id'] ?>">
                                        <button type="button" onclick="confirmDelete(<?= $row['enroll_id'] ?>)" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
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