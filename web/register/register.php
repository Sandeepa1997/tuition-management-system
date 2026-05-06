<?php
ob_start();
include '../../init.php';
?>

<!-- Optional: Wrapper for alignment and spacing -->
<div class="container py-5 text-center">
  <h2 class="mb-4 text-success fw-bold">Welcome to ScienceMore!!!</h2>

  <!-- Transparent buttons with outline style -->
  <a class="btn btn-outline-success px-4 py-2 rounded-pill fw-semibold me-3 mb-2" 
     href="<?= WEB_URL ?>student/register.php">Register as a Student</a>

  <a class="btn btn-outline-success px-4 py-2 rounded-pill fw-semibold mb-2" 
     href="<?= WEB_URL ?>parent/register.php">Register as a Parent</a>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
