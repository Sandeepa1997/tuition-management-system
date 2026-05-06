<?php
ob_start();
include '../../init.php';

// Confirm whether login to the system
if (!isset($_SESSION['ID'])) {
    header("Location:../login.php");
    exit;
}
?>

<?php

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        extract($_POST);
        $db=dbConn();

 $sql="SELECT p.Id,s.Id as student_id FROM parents p
INNER JOIN students s ON s.guardian_id=p.Id
WHERE s.Id='$student_id'";






      }

?>
 <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" >

<label for="">notice</label>
<input type="text" name="notice" id="notice">
<button type="submit">Submit</button>
<input type="hidden" name="action" value="<?=$student_id?>">
</form>



<?php
$content = ob_get_clean();
include '../layouts.php';
?>
