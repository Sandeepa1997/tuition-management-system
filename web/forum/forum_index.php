<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];

if (!isset($_GET['subject_id'])) {
    echo "Invalid subject.";
    exit;
}
$subjectId = $_GET['subject_id'];

// Get subject name
$sqlSub = "SELECT name FROM subjects WHERE Id = '$subjectId'";
$resultSub = $db->query($sqlSub);
$rowSub = $resultSub->fetch_assoc();
$subjectName = $rowSub['name'];

// Get the class_id where the student is enrolled and that matches the selected subject
$sqlClass = "SELECT c.Id AS class_id 
             FROM student_enroll se 
             JOIN classes c ON se.class_id = c.Id 
             WHERE se.student_id = '$studentId' AND c.Subject_id = '$subjectId' 
             LIMIT 1";
$resultClass = $db->query($sqlClass);

if ($resultClass->num_rows == 0) {
    echo "<div class='container'><p class='text-danger'>No class found for this subject.</p></div>";
    exit;
}

$rowClass = $resultClass->fetch_assoc();
$classId = $rowClass['class_id'];

// Get forum topics for this class
$sql = "SELECT T.*, U.FirstName, U.LastName 
        FROM forum_topics T
        INNER JOIN users U ON U.Id = T.created_by
        WHERE T.class_id = '$classId'
        ORDER BY T.created_at DESC";
$result = $db->query($sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary">Forum Topics - <?= $subjectName ?></h3>
        <a href="new_topic.php?class_id=<?= $classId ?>" class="btn btn-success">➕ Start New Topic</a>
    </div>

    <?php if ($result->num_rows > 0) { ?>
        <table class="table table-bordered table-hover text-center">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Posted By</th>
                    <th>Date</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= $row['FirstName'] . ' ' . $row['LastName'] ?></td>
                        <td><?= date('Y-m-d h:i A', strtotime($row['created_at'])) ?></td>
                        <td><a href="view_topic.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info text-center mt-4">
            No topics posted yet for <strong><?= $subjectName ?></strong>. Be the first to start a discussion!
        </div>
    <?php } ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
