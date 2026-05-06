<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['STUDENT_ID'])) {
    header('Location: ../login.php');
    exit;
}

// Get and clear the success message
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<div class="card text-center shadow-sm mx-auto" style="max-width: 450px; margin-top: 50px; padding: 20px; border-radius: 12px; background-color: #f0f9ff;">
  <div class="card-header bg-primary text-white rounded-top">
    <h2>Upload Successful!</h2>
  </div>
  <div class="card-body">
    <h3 class="text-success mb-4" style="letter-spacing: 2px; font-weight: 600;">
      <?= htmlspecialchars($successMessage) ?>
    </h3>
    <p class="mb-4" style="font-size: 1.1rem; color: #555;">
      Your assignment has been successfully submitted.
    </p>
    <a href="../student/dashboard.php" class="btn btn-success btn-lg px-5" style="border-radius: 30px; font-weight: 600;">
      Back to dashboard
    </a>
  </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
