<?php
ob_start();
include '../../init.php';



$subjectId = $_GET['subject_id'] ?? null;
$gradeLevelId = $_GET['grade_level_id'] ?? $_SESSION['GRADE_LEVEL_ID'] ?? null;



$db = dbConn();

$sql = "SELECT c.*, 
               t.FirstName, t.LastName, t.Profile_Picture
        FROM classes c 
        LEFT JOIN teachers t ON c.Teacher_id = t.Id 
        WHERE c.Subject_id = $subjectId AND c.Grade_Level_id = $gradeLevelId";

$result = $db->query($sql);
?>




<div class="container mt-5">
    <h3 class="text-success mb-4 fw-bold text-center">Available Classes</h3>

    <?php if ($result->num_rows > 0) { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="row g-0 align-items-center">
                    
                    <!-- Left Column: Class Details + Enroll Button -->
                    <div class="col-md-8 p-4">
                        <h5 class="card-title text-primary fw-semibold"><?= $row['Class_Name'] ?></h5>
                        <p class="mb-2"><strong>Teacher:</strong> <?= $row['FirstName'] . ' ' . $row['LastName'] ?></p>
                        <p class="mb-2"><strong>Start Time:</strong> <?= date("g:i A", strtotime($row['start_time'])) ?></td>
                        <p class="mb-2"><strong>End Time:</strong> <?= date("g:i A", strtotime($row['end_time'])) ?></td>
                        <p class="mb-2"><strong>Class Date:</strong><?= $row['Class_Date'] ?></p>
                        <p class="mb-2"><strong>Maximum Students:</strong> <?= $row['Maximum_Students'] ?></p>
                        <p class="mb-3"><strong>Class Fee:</strong> <span class="text-success">Rs. <?= number_format($row['class_fee'], 2) ?></span></p>

                        <form method="post" action="enroll.php">
                            <input type="hidden" name="class_id" value="<?= $row['Id'] ?>">
                            <button type="submit" class="btn btn-success">Enroll</button>
                        </form>
                    </div>

                    <!-- Right Column: Profile Picture -->
                    <div class="col-md-4 p-4 text-center">
                        <img src="../../system/uploads/<?php echo $row['Profile_Picture']; ?>" 
                             alt="Profile Picture" 
                             class="img-fluid rounded shadow-sm" 
                             style="max-height: 180px; object-fit: cover;">
                    </div>

                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="alert alert-warning text-center">No classes available for this subject.</div>
    <?php } ?>
</div>







<?php
$content = ob_get_clean();
include '../layouts.php';
?>