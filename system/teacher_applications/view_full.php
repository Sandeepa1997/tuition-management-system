<?php
ob_start();
include '../../init.php';
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
}
extract($_POST);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Id'])) {
    $id = $_POST['Id'];

    $db = dbConn();

    // Get main application
    $sql = "SELECT * FROM teachers WHERE Id = $id";
    $result = $db->query($sql);
    $app_details = $result->fetch_assoc();

    // Get subject name
   
    $subject_name = 'N/A';
    if (!empty($app_details['Subject_id'])) {
        $subject_id = $app_details['Subject_id'];
        $sql = "SELECT name FROM subjects WHERE id = $subject_id";
        $subject_result = $db->query($sql);
        if ($subject_result->num_rows > 0) {
            $subject_name = $subject_result->fetch_assoc()['name'];
        }
    }



    // Get experience
    $sql = "SELECT * FROM teacher_experience WHERE application_id = $id";
    $exp_result = $db->query($sql);

    // Get degrees
    $sql = "SELECT * FROM teacher_degrees WHERE application_id= $id";
    $deg_Result = $db->query($sql);

    // Get prefered grades
    $sql = "SELECT * FROM teacher_grades WHERE application_id= $id";
    $grade_Result = $db->query($sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == "verify") {
    $db = dbConn();
    $date = date('Y-m-d');
    $sql = "UPDATE teachers SET Status ='1',status_change_date='$date' WHERE Id='$Id'";
    $db->query($sql);
}
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Teacher Full Application</h4>
        </div>

        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
            <div class="card-body">
                <!-- Personal Info -->
                <h5 class="mb-3 text-success">Personal Information</h5>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <p><strong>Full Name:</strong> <?php echo $app_details['title'] . ' ' . $app_details['FirstName'] . ' ' . $app_details['LastName']; ?></p>
                        <p><strong>NIC:</strong> <?php echo $app_details['NIC_No']; ?></p>
                        <p><strong>Gender:</strong> <?php echo $app_details['Gender']; ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo $app_details['Dob']; ?></p>
                        <p><strong>Email:</strong> <?php echo $app_details['Email']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>City:</strong> <?php echo $app_details['City']; ?></p>
                        <p><strong>Address:</strong> <?php echo $app_details['Address']; ?></p>
                        <p><strong>Preferred Subject/s:</strong> <?php echo $subject_name; ?></p>
                        <?php
                        if ($grade_Result->num_rows > 0) {
                            $grades = [];
                            while ($row = $grade_Result->fetch_assoc()) {
                                $grade_id = $row['grade_level_id'];
                                $sql = "SELECT name FROM grade_levels WHERE id = $grade_id";
                                $grade_name_result = $db->query($sql);
                                if ($grade_name_result->num_rows > 0) {
                                    $grade_name_row = $grade_name_result->fetch_assoc();
                                    $grades[] = $grade_name_row['name'];
                                }
                            }
                            echo "<p><strong>Preferred grade/s:</strong> " . implode(", ", $grades) . "</p>";
                        }

                        ?>
                        <p><strong>A/L Stream:</strong> <?php echo $app_details['AL_stream']; ?></p>
                        <p><strong>A/L Results:</strong> <?php echo $app_details['AL_results']; ?></p>
                    </div>
                </div>

                <div class="mb-4">
                    <p><strong>Profile Picture:</strong><br>
                        <img src="../uploads/<?php echo $app_details['Profile_Picture']; ?>" alt="Profile Picture" class="img-thumbnail mt-2" width="150">
                    </p>
                </div>

                <!-- Experience -->
                <h5 class="mb-3 text-success">Teaching Experience</h5>
                <ul class="list-group mb-4">
                    <?php
                    if ($exp_result->num_rows > 0) {
                        while ($row = $exp_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <?php echo $row['School_Institute_name'] . ' (' . $row['type'] . ') - ' . $row['duration']; ?>
                            </li>
                    <?php
                        }
                    } ?>
                </ul>

                <!-- Degrees -->
                <h5 class="mb-3 text-success">Degree Information</h5>
                <ul class="list-group mb-4">
                    <?php
                    if ($deg_Result->num_rows > 0) {
                        while ($row = $deg_Result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <?php echo $row['university_name'] . ' - ' . $row['degree_name'] . ' (' . $row['degree_class'] . ')'; ?>
                            </li>
                    <?php
                        }
                    }
                    ?>
                </ul>

                <h5 class="text-success fw-bold">current status</h5>
                <?php
                if ($app_details['Status'] == '1') {
                    echo "Verified teacher";
                } else {
                    echo "Unverified teacher";
                }
                ?>
            </div>

            <div class="card-footer d-flex gap-1">
                <a href="view.php" class="btn btn-secondary">Back to Applications</a>
                <?php
                if ($app_details['Status'] != '1') {
                ?>
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" id="frmverify">
                        <input type="hidden" name="Id" id="Id" value="<?= $app_details['Id'] ?>">
                        <button type="submit" name="action" value="verify" class="btn btn-primary">verify</button>
                    </form>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>