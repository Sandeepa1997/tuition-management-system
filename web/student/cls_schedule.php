<?php
ob_start();
include '../../init.php';
$db = dbConn();

if (!isset($_SESSION['STUDENT_ID'])) {
    header("Location: ../login.php");
    exit;
}

$studentId = $_SESSION['STUDENT_ID'];

$sql = "SELECT 
            c.Class_Date,
            s.name AS subject,
            g.name AS grade,
            t.FirstName,
            t.LastName,
            c.start_time,
            c.end_time
        FROM student_enroll se
        INNER JOIN classes c ON se.class_id = c.Id
        INNER JOIN teachers t ON c.Teacher_id=t.Id
        INNER JOIN subjects s ON c.Subject_id = s.id
        INNER JOIN grade_levels g ON c.Grade_Level_id = g.id
        WHERE se.student_id = '$studentId'";

$result = $db->query($sql);

// Get current month and year
$currentMonthName = date('F');
$currentYear = date('Y');  
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <?php if ($result->num_rows > 0): ?>
                <h4><?=$currentMonthName?> - <?=$currentYear?></h4>
                <table class="table table-hover text-center align-middle ">
                    <thead class="table-info">
                        <tr>
                            <th>Teacher Name</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Time Slot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['FirstName'] ?> <?= $row['LastName'] ?></td>
                                <td><?= $row['subject'] ?></td>
                                <td><?= ($row['Class_Date']) ?></td>
                                <td>
                                    <?= date("g:i A", strtotime($row['start_time'])) ?>-
                                    <?= date("g:i A", strtotime($row['end_time'])) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center mb-0">
                    You are not enrolled in any classes yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>