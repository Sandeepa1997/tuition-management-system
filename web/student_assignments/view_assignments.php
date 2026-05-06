<?php 
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];
$subjectId = $_GET['subject_id'];

// Get class ID
$sqlClass = "SELECT c.Id 
             FROM student_enroll se 
             JOIN classes c ON se.class_id = c.Id 
             WHERE se.student_id = $studentId AND c.Subject_id = '$subjectId'";
$classResult = $db->query($sqlClass);
$classRow = $classResult->fetch_assoc();
$classId = $classRow['Id'];

// Get assignments (both Ongoing and Complete)
$sql = "SELECT 
            ats.Id AS title_id,
            ats.title,
            ats.Status,
            t.term,
            ats.term_id,
            a.Id AS assignment_id,
            a.description,
            a.due_date
        FROM assignment_titles ats
        JOIN assignments a ON a.title_id = ats.Id
        JOIN terms t ON ats.term_id = t.Id
        WHERE ats.class_id = $classId";

$result = $db->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Assignments</h4>
        </div>
        <div class="card-body">

            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) {
                    $assignmentId = $row['assignment_id'];
                    $termId = $row['term_id'];
                    $status = $row['Status'];
                    $dueDate = $row['due_date'];
                    $today = date('Y-m-d');
                ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold">
                                <?= $row['title'] ?> <small>(<?= $row['term'] ?>)</small>
                            </h5>
                            <p><?= $row['description'] ?></p>

                            <?php if ($status == 'Ongoing') { ?>
                                <p class="text-danger"><strong>Due:</strong> <?= $dueDate ?></p>
                                <?php if ($dueDate == $today) { ?>
                                    <p class="text-warning"><strong>Today is the last day of submission.</strong></p>
                                <?php } ?>

                                <?php
                                $sqlCheck = "SELECT * FROM assignment_submissions 
                                             WHERE assignment_id = '$assignmentId' 
                                             AND student_id = '$studentId' 
                                             AND term_id = '$termId'";
                                $resCheck = $db->query($sqlCheck);
                                $existing = $resCheck->fetch_assoc();
                                ?>

                                <?php if ($existing) { ?>
                                    <div class="mb-2">
                                        <p class="text-success">
                                            Submitted: 
                                            <a href="../../system/uploads/<?= htmlspecialchars($existing['file_name']) ?>" target="_blank">
                                                <?= htmlspecialchars($existing['file_name']) ?>
                                            </a>
                                        </p>
                                        <p class="text-muted">You can update before due date.</p>
                                    </div>
                                <?php } ?>

                                <form action="upload_assignment.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">
                                    <input type="hidden" name="term_id" value="<?= $termId ?>">

                                    <div class="mb-2">
                                        <input type="file" name="assignment_file" class="form-control" required>
                                    </div>

                                    <button type="submit" class="btn btn-sm btn-<?= $existing ? 'warning' : 'success' ?>">
                                        <i class="bi bi-upload"></i> <?= $existing ? 'Update Submission' : 'Upload Assignment' ?>
                                    </button>
                                </form>

                            <?php } else { 
                                // Status is Complete: show marks
                                $sqlMark = "SELECT am.marks
                                            FROM assignment_submissions s
                                      
                                            LEFT JOIN assignments_marks am ON s.Id = am.submission_id
                                            WHERE s.assignment_id = '$assignmentId' AND s.student_id = '$studentId'";
                                $resMark = $db->query($sqlMark);
                                $markRow = $resMark->fetch_assoc();

                                if ($markRow) {
                                    $marks = $markRow['marks'];
                                  
                                    // Grade logic
                                    if (is_null($marks)) {
                                        $grade = 'Absent';
                                        $rowClass = 'style="background-color:rgba(235, 143, 39, 1);"';
                                    } elseif ($marks >= 75) {
                                        $grade = 'A';
                                        $rowClass = 'class="bg-success text-white"';
                                    } elseif ($marks >= 65) {
                                        $grade = 'B';
                                        $rowClass = 'style="background-color:rgba(109, 241, 57, 1);"';
                                    } elseif ($marks >= 55) {
                                        $grade = 'C';
                                        $rowClass = 'style="background-color:rgba(142, 247, 100, 1);"';
                                    } elseif ($marks >= 35) {
                                        $grade = 'S';
                                        $rowClass = 'class="bg-warning"';
                                    } else {
                                        $grade = 'F';
                                        $rowClass = 'class="bg-danger text-white"';
                                    }
                                ?>
                                    <table class="table table-bordered mt-3">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Term</th>                                              
                                                <th>Marks</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr <?= $rowClass ?>>
                                                <td><?= $row['term'] ?></td>
                                                <td><?= is_null($marks) ? '0' : $marks ?></td>
                                                <td><?= $grade ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <div class="alert alert-warning">No submission or marks found.</div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-info text-center">No assignments available.</div>
            <?php } ?>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
