<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit();
}

$db = dbConn();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Get enrollment + student + class info
    $sql = "SELECT 
                
                se.date AS enroll_date,
                s.*,
                u.FirstName,
                u.LastName,
                u.Email,
                u.Profile_Picture,
                u.Primary_Contact,
                u.Gender,
                u.Address,
                u.City,
                c.Class_Name
            FROM 
                student_enroll se
            LEFT JOIN 
                students s ON se.student_id = s.Id
            LEFT JOIN 
                users u ON s.userid = u.Id
            LEFT JOIN 
                classes c ON se.class_id = c.Id
            WHERE 
                s.Id = $student_id
            ORDER BY se.id DESC
            LIMIT 1"; // in case multiple enrollments, show latest
            
    $result = $db->query($sql);

    $student = $result->fetch_assoc();
}
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Enrolled Student Full Details</h4>
        </div>

        <div class="card-body">
            <h5 class="mb-3 text-success">Enrollment Information</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    
                    <p><strong>Enrolled Date:</strong> <?= $student['enroll_date'] ?></p>
                    <p><strong>Class Name:</strong> <?= $student['Class_Name'] ?></p>
                </div>
            </div>

            <h5 class="mb-3 text-success">Personal Information</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Full Name:</strong> <?= $student['FirstName'] . ' ' . $student['LastName'] ?></p>
                   
                    <p><strong>Gender:</strong> <?= $student['Gender'] ?></p>
                    <p><strong>Date of Birth:</strong> <?= $student['dob'] ?></p>
                    <p><strong>Email:</strong> <?= $student['Email'] ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>City:</strong> <?= $student['City'] ?></p>
                    <p><strong>Address:</strong> <?= $student['Address'] ?></p>
                    <p><strong>Primary Contact:</strong> <?= $student['Primary_Contact'] ?></p>
                      <p><strong>Guardian Contact:</strong> <?= $student['guardian_contact1'] ?></p>
                    

                </div>
            </div>

            <div class="mb-4">
                <p><strong>Profile Picture:</strong><br>
                    <img src="../uploads/<?= $student['Profile_Picture'] ?>" alt="Profile Picture" class="img-thumbnail mt-2" width="150">
                </p>
            </div>
        </div>

        <div class="card-footer">
            <a href="view.php" class="btn btn-secondary">Back to Enrollment List</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
