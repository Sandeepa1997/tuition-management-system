<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $meetingId = $_POST['meeting_id'];
    $status = $_POST['status'];

    $sqlUpdate = "UPDATE meetings SET status='$status' WHERE Id = '$meetingId'";
    $db->query($sqlUpdate);
}


$sql ="SELECT m.*,c.Class_Name,m.Id AS meeting_id FROM meetings m INNER JOIN classes c ON m.class_id=c.Id
WHERE m.teacher_id='$teacherId'";

$result = $db->query($sql);

?>
<div class="card card-info">
    <div class="card-header bg-info text-white">
        <h3 class="card-title">View Meetings</h3>
    </div>

    <div class="card-body table-responsive">
     
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
                        <?php if($result->num_rows>0) {
                            while($row=$result->fetch_assoc()){

                             ?>
                            <tr>
                         
                                <td><?= $row['Class_Name'] ?></td>
                                <td><?= $row['type'] ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= $row['start_time'] ?></td>
                                <td><?= $row['end_time'] ?></td>
                           
                                <td>
                                    <form method="post" action="view.php">
                                        <input type="hidden" name="meeting_id" value="<?= $row['meeting_id'] ?>">
                                        <select name="status" class="form-select form-select-sm form-control">
                                            <option value="">Select Status</option>
                                            <option value="Scheduled" <?= $row['status'] == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                            <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" name="update_status" value="1" class="btn btn-sm btn-success">Proceed</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="edit.php" method="post">
                                        <input type="hidden" name="Id" value="<?= $row['meeting_id'] ?>">
                                        <button type="submit" name="action" value="edit" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } 
                        }
                        ?>
                            
                    </tbody>
                </table>
           
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
