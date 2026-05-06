<?php
ob_start();
include '../../init.php';

// Confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit;
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

<html>
<head>
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
</head>

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
</body>
</html>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
