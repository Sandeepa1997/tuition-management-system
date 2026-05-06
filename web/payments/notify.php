<?php
ob_start();
include '../../init.php';
?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>