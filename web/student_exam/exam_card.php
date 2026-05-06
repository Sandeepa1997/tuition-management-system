<?php
ob_start();
include '../../init.php';

// Check student login
if (!isset($_SESSION['ID'])) {
  header("Location: ../login.php");
  exit;
}
?>
<?php
$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];

$sqlExams="SELECT e.*,c.Class_Name,su.name AS subject_name,s.reg_no,t.term
FROM exams e
JOIN classes c ON e.class_id = c.Id
JOIN terms t ON e.term_id = t.Id
JOIN subjects su ON c.Subject_id = su.Id
JOIN student_enroll se ON se.class_id = c.Id
JOIN students s ON se.student_id = s.Id
WHERE se.student_id = $studentId AND e.status = 'Scheduled'
ORDER BY e.date ASC";

$resultExams = $db->query($sqlExams);
?>
<div class="container">
<div class="row">
    <?php if ($resultExams->num_rows > 0) { ?>
        <?php while ($exam = $resultExams->fetch_assoc()) { ?>
            <div class="col-xl-4">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center"
                     style="background-color:rgb(165, 217, 122); color:rgb(33, 34, 34); border: 1px solid #e1bee7; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                    <h4 class="mt-2"><?= htmlspecialchars($exam['term']) ?> Exam</h4>
                    <p><strong>Subject:</strong> <?= htmlspecialchars($exam['subject_name']) ?></p>
                    <p><strong>Class:</strong> <?= htmlspecialchars($exam['Class_Name']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($exam['date']) ?></p>
                       <p><strong>Start Time:</strong> <?= htmlspecialchars($exam['start_time']) ?></p>
                    <p><strong>Duration:</strong> <?= htmlspecialchars($exam['duration']) ?></p>
                        <a href="exam_admission.php?exam_id=<?= $exam['Id'] ?>" target="_blank" class="btn btn-sm btn-primary mt-2">
                Download Admission
            </a>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                No scheduled exams available at the moment.
            </div>
        </div>
    <?php } ?>
</div>
</div>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>