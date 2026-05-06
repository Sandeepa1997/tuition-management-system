<?php
ob_start();
include '../../init.php';

$class_id = $_POST['class_id'] ?? null;
$db = dbConn();
?>

<div class="container mt-5">

<?php
// 1. Block parents from enrolling
if (isset($_SESSION['USERROLENAME']) && $_SESSION['USERROLENAME'] == 'Parent') {
?>
    <div class="alert alert-info">Only Students can Enroll to classes.</div>
<?php
    $content = ob_get_clean();
    include '../layouts.php';
    exit();
}

// 2. User is NOT logged in
if (!isset($_SESSION['ID'])) {
?>
    <div class="alert alert-info">Please login to enroll in a class.</div>
    <a href="../login.php" class="btn btn-primary">Login</a>
<?php
} else {

    // 3. Logged in — check student registration
    $Userid = $_SESSION['ID'];
    $sql = "SELECT Id,grade_levels_id FROM students WHERE Userid = '$Userid'";
    $result = $db->query($sql);

    if ($result->num_rows == 0) {
?>
        <div class="alert alert-danger">You are not registered! Please register to enroll.</div>
        <a href="../student/register.php" class="btn btn-primary">Register</a>
<?php
    } else {
        $row = $result->fetch_assoc();
        $studentid = $row['Id'];
        $studentGrade = $row['grade_levels_id'];

        // Get class grade
        $sqlClass = "SELECT Grade_Level_id FROM classes WHERE Id = '$class_id'";
        $resultClass = $db->query($sqlClass);
        $classRow = $resultClass->fetch_assoc();
        $classGrade = $classRow['Grade_Level_id'];

        // Grade mismatch check
        if ($studentGrade != $classGrade) {
?>
            <div class="alert alert-warning">You cannot enroll in classes from other grades.</div>
<?php
        } else {
            // Check if already enrolled
            $sql = "SELECT * FROM student_enroll WHERE student_id = '$studentid' AND class_id = '$class_id'";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
?>
                <div class="alert alert-warning">You are already enrolled in this class.</div>
<?php
            } else {
                $date = date('Y-m-d');
                $sql = "INSERT INTO student_enroll (student_id, class_id, date) 
                        VALUES ('$studentid', '$class_id', '$date')";
                $db->query($sql);
?>
                <div class="alert alert-success">You have successfully enrolled in the class.</div>
<?php
            }
        }
    }
}
?>

</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
