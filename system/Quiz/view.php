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
    $titleId = $_POST['quiz_id'];
    $newStatus = $_POST['status'];

    $updateSql = "UPDATE quizzes SET status = '$newStatus' WHERE Id = '$titleId'";
    $db->query($updateSql);
}

// Fetch all quizzes for the teacher
$sql = "SELECT q.id, q.title, q.attempts, c.Class_Name, t.term, q.status, q.type 
        FROM quizzes q
        JOIN classes c ON q.class_id = c.Id
        JOIN terms t ON q.term_id = t.Id
        WHERE c.Teacher_id = '$teacherId'
        ORDER BY c.Class_Name, t.Id DESC";
$result = $db->query($sql);

// Group quizzes by class name
$groupedQuizzes = [];
while ($row = $result->fetch_assoc()) {
    $className = $row['Class_Name'];
    $groupedQuizzes[$className][] = $row;
}
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">My Quizzes</h3>
    </div>

    <div class="card-body">
        <?php if (!empty($groupedQuizzes)) { ?>
            <?php foreach ($groupedQuizzes as $className => $quizzes) { ?>
                <h5 class="mt-3 mb-2 text-primary"><?= $className ?></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Term</th>
                            <th>Type</th>
                            <th>Action</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $row) { ?>
                            <tr>
                                <td><?= $row['title'] ?></td>
                                <td><?= $row['term'] ?></td>
                                <td><?= $row['type'] ?></td>
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="quiz_id" value="<?= $row['id'] ?>">
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="Ongoing" <?= $row['status'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                                            <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-success">Proceed</button>
                                        <?php if ($row['status'] == 'Ongoing') { ?>
                                            <a href="create_quiz.php?quiz_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Add Questions</a>
                                        <?php } ?>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        <?php } else { ?>
            <div class="text-center">No quizzes found</div>
        <?php } ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
