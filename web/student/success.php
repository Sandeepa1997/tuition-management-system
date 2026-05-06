<?php
ob_start();
include '../../init.php';
?>

<div class="card text-center shadow-sm mx-auto" style="max-width: 450px; margin-top: 50px; padding: 20px; border-radius: 12px; background-color: #f0f9ff;">
  <div class="card-header bg-primary text-white rounded-top">
    <h2> Registration Successful!</h2>
  </div>
  <div class="card-body">
    <h4 class="mb-3">Your Registration Number:</h4>
    <h3 class="text-success mb-4" style="letter-spacing: 2px; font-weight: 600;">
      <?= htmlspecialchars($_SESSION['reg_no'] ?? 'N/A') ?>
    </h3>
    <p class="mb-4" style="font-size: 1.1rem; color: #555;">
      Thank you for registering! You can now log in to access your dashboard.
    </p>
    <a href="../login.php" class="btn btn-success btn-lg px-5" style="border-radius: 30px; font-weight: 600;">
      Login
    </a>
  </div>
</div>


<?php

$content = ob_get_clean();
include '../layouts.php';
?>
