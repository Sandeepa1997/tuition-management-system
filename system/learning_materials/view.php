
<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}
?>

<?php
$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

$sql = "SELECT lm.id, lm.title, lm.created_at,g.name AS Grade
        FROM learning_material_titles lm      
        JOIN classes c ON lm.class_id = c.Id
        JOIN grade_levels g ON c.Grade_Level_id=g.Id
        WHERE c.Teacher_id = '$teacherId'";
$result = $db->query($sql);
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Learning Materials</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Material ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Grade</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['title'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= $row['Grade'] ?></td>
                            <td>
                                <a href="upload.php?title_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Upload</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No Materials are found</td>
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
