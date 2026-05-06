<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['TEACHER_ID'])) {
    header('Location: ../login.php');
    exit;
}
?>




<?php
$content = ob_get_clean();
include '../layouts.php';
?>