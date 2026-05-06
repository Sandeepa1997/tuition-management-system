<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header('Location: ../login.php');
    exit;
}
?>

<?php
$_SESSION['viewed_material'] = true; //to hide notification once it is opened

$db = dbConn();

$studentId = $_SESSION['STUDENT_ID'];

//Get enrolled subjects
$sql = "SELECT s.Id,s.name
 FROM student_enroll se 
 JOIN classes c ON se.class_id=c.Id
 JOIN subjects s ON c.Subject_id = s.Id
 WHERE se.student_id = $studentId";

$result = $db->query($sql);

?>

<div class="container mt-4">
    <div class="card shadow-sm bg-light">
        <h3 class="text-center mb-3 mt-2 text-dark fw-bold ">Enrolled Subjects</h3>
    </div>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="col-md-4 mb-4">

                    <a href="forum_index.php?subject_id=<?= $row['Id'] ?>" style="text-decoration:none;">
                        <div class="card shadow-sm p-3 text-center mt-3" style="border: radius 15px; background-color:#e0f2f1;">
                            <h5><?= $row['name'] ?></h5>
                        </div>
                    </a>
                </div>
            <?php }
        } else { ?>
            <div class="col-12">
                <p class="text-center">You are not enrolled in any subjects.</p>

            </div>
        <?php } ?>
    </div>
</div>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>