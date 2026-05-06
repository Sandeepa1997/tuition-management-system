<?php
ob_start();
include '../../init.php';
 ?>

 <div class="row">

 <div class="col-md-12">
    <h1>Delete New User Account</h1>

    <?php
      extract($_POST);
      $db = dbConn();
      $sql = "DELETE FROM users WHERE Id = '$Id'";
      $db->query($sql);

      header("Location:view.php");
    ?>

 </div>
</div>




<?php
$content= ob_get_clean();
include '../layouts.php';
 ?>