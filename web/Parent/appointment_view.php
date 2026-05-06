<?php
ob_start();
include '../../init.php';
?>

<?php
$db = dbConn();
$appointment = null;

if (isset($_SESSION['ID'])) {
    $userId = $_SESSION['ID'];

    // Get parent ID
    $sql = "SELECT Id FROM parents WHERE Userid = '$userId'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $parentId = $row['Id'];

    // Get latest appointment with teacher details
    $sql = "SELECT a.*, t.FirstName, t.LastName 
            FROM appointments a
            LEFT JOIN teachers t ON a.teacher_id = t.Id
            WHERE a.parent_id = '$parentId'
            ORDER BY a.appointment_date DESC 
            LIMIT 1";

    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
    }
}
?>

<div class="container py-5">
    <?php if ($appointment): ?>
        <div class="alert alert-success shadow-sm">
            <h5>Your Appointment Details</h5>
            <p><strong>Date:</strong> <?= $appointment['appointment_date'] ?></p>
            <p><strong>Time:</strong> <?= $appointment['appointment_time'] ?></p>
            <p><strong>Teacher:</strong> <?= $appointment['FirstName'] . ' ' . $appointment['LastName'] ?></p>
            <p><strong>Reason:</strong> <?= $appointment['reason'] ?></p>
            <p><strong>Reference Number:</strong> <?= $appointment['appointment_ref'] ?></p>

            <?php
            // Show QR Code
            $qr_path = '../../qr_codes/';
            $filename = $qr_path . 'qr_' . md5($appointment['appointment_ref']) . '.png';
            if (file_exists($filename)) {
                echo "<img src='$filename' class='img-thumbnail mt-3' style='max-width:150px'>";
            }
            ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No appointment found.</div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
