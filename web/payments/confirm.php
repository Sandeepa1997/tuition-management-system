<?php
ob_start();
include '../../init.php';

$messages = array();
$regno = $_POST['regno'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($regno)) {
        $messages['regno'] = "Please enter a Registration Number!";
    } else {
        $db = dbConn();

        // 1. Get student by reg_no
        $sqlStudent = "SELECT * FROM students WHERE reg_no = '$regno'";
        $resultStudent = $db->query($sqlStudent);

        if ($resultStudent->num_rows == 0) {
            $messages['regno'] = "No student found with this Registration Number!";
        } else {
            $studentRow = $resultStudent->fetch_assoc();
            $studentId = $studentRow['Id'];
            $guardianId = $studentRow['guardian_id'];

            // 2. Get parent user NIC from session
            $loggedUserId = $_SESSION['ID']; // Logged-in user ID
            $sqlNIC = "SELECT NIC_No FROM users WHERE Id = $loggedUserId";
            $resultNIC = $db->query($sqlNIC);

            if ($resultNIC->num_rows == 0) {
                $messages['regno'] = "User details not found!";
            } else {
                $nicNo = $resultNIC->fetch_assoc()['NIC_No'];

                // 3. Get NIC of student's guardian (parent)
                $sqlGuardianNIC = "SELECT u.NIC_No 
                                   FROM parents p
                                   JOIN users u ON p.Userid = u.Id
                                   WHERE p.Id = $guardianId";
                $resultGuardian = $db->query($sqlGuardianNIC);

                if ($resultGuardian->num_rows == 0) {
                    $messages['regno'] = "Guardian data not found!";
                } else {
                    $guardianNIC = $resultGuardian->fetch_assoc()['NIC_No'];

                    if ($nicNo !== $guardianNIC) {
                        $messages['regno'] = "Please enter your own child's registration number!";
                    } else {
                        // . Fetch exam results

                        // Valid link, set session and redirect
                        $_SESSION['REGNO'] = $regno;
                        $_SESSION['PARENT_ID'] = $_SESSION['ID'];
                        header("Location: pay.php");
                        exit;
                    }
                }
            }
        }
    }
}







?>

<div class="container mt-5">
    <?php
    if (!isset($_SESSION['ID'])) {
    ?>
        <div class="alert alert-warning">Please login to make a payment.</div>
        <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>login.php">Log in</a>
        <?php
    } else {
        $userId = $_SESSION['ID'];
        $role = $_SESSION['USERROLE'];

        $db = dbConn();
        $sql = "SELECT * FROM users WHERE Id = $userId";
        $result = $db->query($sql);

        if ($result->num_rows == 0) {
        ?>
            <div class="alert alert-warning">You are not registered</div>
            <a class="btn btn-primary px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>register/register.php">Register Now</a>
        <?php
        } elseif ($role == 'Student') {
        ?>
            <div class="alert alert-info">Only parents can make payments.</div>
            <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>login.php">Log in</a>
        <?php
        } elseif ($role == 'parent') {
        ?>
            <div class="alert alert-success">Welcome! Enter your student's Registration Number to proceed with payment.</div>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" class="p-4 border rounded shadow-sm bg-light">
                <div class="mb-3">
                    <label for="regno" class="form-label fw-bold">Student Registration Number</label>
                    <input type="text" name="regno" id="regno" class="form-control" placeholder="e.g., R123456" value="<?= htmlspecialchars($regno) ?>">
                    <span class="text-danger" style="font-size: 13px;"><?= @$messages['regno'] ?></span>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-link me-2"></i>Submit
                    </button>
                </div>
            </form>
    <?php
        }
    }
    ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>