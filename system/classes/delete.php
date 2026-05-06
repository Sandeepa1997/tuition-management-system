<?php
ob_start();
include '../../init.php';
?>

<div class="row">
  <div class="col-md-12">
    <h1>Delete Class</h1>

    <?php
    extract($_POST);
    $db = dbConn();

    // Optional: validate if class ID exists before deletion
    if (!empty($Id)) {
        $sql = "DELETE FROM classes WHERE Id = '$Id'";
        $db->query($sql);
    }

    header("Location:view.php");
    ?>
    
  </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
