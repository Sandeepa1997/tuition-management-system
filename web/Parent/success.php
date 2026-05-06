<?php
ob_start();
include '../../init.php';
?>

</div>
</header>

<div class="container">
<div class="alert alert-success text-center">
  <h3 class="fw-bold">"You are successfully registered!!!"</h3>  <br>
    
</div>
<a href="../login.php">Click Here to Login</a>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>