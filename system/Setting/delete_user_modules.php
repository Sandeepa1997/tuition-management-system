<?php
ob_start();
include '../../init.php';
if (!isset($_SESSION['ID'])) {
    header("Location:../../login.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

extract($_POST);

$db = dbConn();
$sql = "UPDATE user_modules SET Status='$Status' WHERE Id='$Id'";
$db->query($sql);

header("Location:user_modules.php");
exit();
}
?>


<?php
$content = ob_get_clean();
include '../layouts.php';
?>