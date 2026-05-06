<?php
ob_start();
include '../../init.php';
?>
<div class="container mt-5" style="max-width: 600px;">
  <div class="card shadow-lg p-4 border-success">
    <div class="text-center">
	<i class="bi bi-exclamation-triangle-fill text-warning"></i>
      <h3 class="mt-3 text-danger">Payment Unsuccessful!</h3>
      <p class="text-muted">Please Try Again!.</p>
    </div>
    <div class="text-center mt-3">
      <a href="../Parent/dashboard.php" class="btn btn-success btn-sm">Go to Dashboard</a>

    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include '../layouts.php';
?>