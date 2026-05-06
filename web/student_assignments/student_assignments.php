<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();
$studentId = $_SESSION['STUDENT_ID'];

// Get subjects for the student
 $sqlSubjects = "SELECT s.Id, s.name 
                FROM student_enroll se
                JOIN classes c ON se.class_id = c.Id
                JOIN subjects s ON c.Subject_id = s.Id
                WHERE se.student_id = $studentId";

$resultSubjects = $db->query($sqlSubjects);
?>

<section class="section">
    <div class="container">
        <div class="card shadow">
        <h3 class="mb-4 text-center mt-2 fw-bold">📘 Your Assignments</h3>
        </div>
        <div class="row gy-4">

            <?php while ($sub = $resultSubjects->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <a href="view_assignments.php?subject_id=<?= $sub['Id'] ?>" style="text-decoration: none;" name="view">
                        <div class="card text-center p-3 shadow-sm mt-3" style="border-radius: 16px; background-color: #52da5eff;">
                            <h5 class="fw-bold"><?= $sub['name'] ?></h5>
                            <p class="mb-0">Click to view assignments</p>
                        </div>
                    </a>
                </div>
            <?php } ?>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
