<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}



$db = dbConn();
$teacherId = $_SESSION['TEACHER_ID'];

// Get subject ID
$sqlSubject = "SELECT s.id AS subject_id FROM teachers t
               JOIN subjects s ON t.subject_id = s.id
               WHERE t.Id = '$teacherId' LIMIT 1";
               
$resultSubject = $db->query($sqlSubject);
$rowSubject = $resultSubject->fetch_assoc();
$subjectId = $rowSubject['subject_id'] ?? null;

// Get class list
$sqlClasses = "SELECT c.Id AS class_id, g.name AS grade_name, s.name AS subject_name
               FROM classes c
               JOIN grade_levels g ON c.Grade_Level_id = g.id
               JOIN subjects s ON c.Subject_id = s.id
               WHERE c.Teacher_id = '$teacherId'";
$resultClasses = $db->query($sqlClasses);



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    $db=dbConn();
    $sql = "INSERT INTO learning_material_titles (class_id, title,created_at,status)
            VALUES ('$class_id', '$title', '$date',$status)";
    $db->query($sql);
        echo '<script>
          Swal.fire({
    position: "center",
    icon: "success",
    title: "Your work has been saved",
    showConfirmButton: false,
    timer: 3500
    }).then(function(){window.location="view.php"});
    </script>';
}

?>


<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Add Materials</h3>
    </div>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="card-body">
            <input type="hidden" name="subject_id" value="<?= $subjectId ?>">

            <div class="mb-3">
                <label>Select Class</label>
                <select name="class_id" class="form-control">
                    <option value="">-- Select Class --</option>
                    <?php while ($row = $resultClasses->fetch_assoc()) { ?>
                        <option value="<?= $row['class_id'] ?>">
                            <?= $row['grade_name'] ?> - <?= $row['subject_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control">
            </div>

            

            <div class="mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control">
            </div>

                <div class="mb-3">
                <label>Status</label>
                <select name="status" id="status" class="form-control">
                     <option value="">--</option>
                     <option value="1">Actice</option>
                     <option value="0">Inactive</option>

                </select>
            </div>


            <div class="text-center mb-3">
                <button type="submit" class="btn btn-primary">Proceed</button>
            </div>
        </div>

    </form>

</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>