<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Handle status update form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $titleId = $_POST['title_id'];
    $newStatus = $_POST['status']; // 'Ongoing' or 'Completed'

    $updateSql = "UPDATE assignment_titles SET Status = '$newStatus' WHERE Id = '$titleId'";
    $db->query($updateSql);
}

// Fetch assignments
$sql = "SELECT at.Id, at.title, c.Class_Name, t.term, a.due_date, at.Status
        FROM assignment_titles at
        JOIN classes c ON at.class_id = c.Id
        LEFT JOIN assignments a ON a.title_id = at.Id
        JOIN terms t ON at.term_id = t.Id
        WHERE c.Teacher_id = '$teacherId'";

$result = $db->query($sql);
?>

<div class="card card-success">
    <div class="card-header text-white bg-success">
        <h3 class="card-title">Assignments</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Term</th>
                    <th>Due Date</th>
                    <th>Class Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['Id'] ?></td>
                            <td><?= $row['title'] ?></td>
                            <td><?= $row['term'] ?></td>
                            <td><?= $row['due_date'] ?></td>
                            <td><?= $row['Class_Name'] ?></td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="title_id" value="<?= $row['Id'] ?>">
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="Ongoing" <?= $row['Status'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                                        <option value="Completed" <?= $row['Status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                            </td>
                            <td>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-success">Update</button>
                                    <?php if ($row['Status'] == 'Ongoing') { ?>
                                        <a href="upload.php?title_id=<?= $row['Id'] ?>" class="btn btn-sm btn-primary mt-2">Upload</a>
                                    <?php } ?>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="text-center">No assignment found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
