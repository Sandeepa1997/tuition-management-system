<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();

$userId=$_SESSION['ID'];
// Fetch exams with grade level
$sql = "SELECT Id FROM parents WHERE Userid = '$userId'";
$result = $db->query($sql);
$row = $result->fetch_assoc();
$parentId = $row['Id'];

// Check if there's an appointment for this parent
$sql = "SELECT m.*,s.guardian_id,c.Class_Name FROM meetings m
              INNER JOIN classes c ON c.Id=m.class_id
              INNER JOIN student_enroll se ON m.class_id=se.class_id
              INNER JOIN students s ON se.student_id = s.Id
              INNER JOIN parents p ON p.Id = s.guardian_id
              WHERE s.guardian_id = '$parentId' ORDER BY date DESC";
$result = $db->query($sql);



?>
<div class="card card-info">
    <div class="card-header bg-info text-white">
        <h3 class="card-title">View Meetings</h3>
    </div>

    <div class="card-body table-responsive">
<div class="container">
        <table class="table table-bordered table-hover text-center align-middle mb-4">
            <thead class="table-light">
                <tr>

                    <th>Class</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($meeting = $result->fetch_assoc()) { ?>

                        
                        <tr>

                            <td><?= $meeting['Class_Name'] ?></td>
                            <td><?= $meeting['type'] ?></td>
                            <td><?= $meeting['date'] ?></td>
                            <td><?= $meeting['start_time'] ?></td>
                            <td><?= $meeting['end_time'] ?></td>
                            <td><?= $meeting['status'] ?></td>
                        </tr>
                <?php }
                }
                ?>

            </tbody>
        </table>
</div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>