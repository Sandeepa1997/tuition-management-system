<?php
ob_start();
include '../../init.php';
?>

<div class="row">

    <div class="col-md-12">
        <h1>Delete </h1>

        <?php
        extract($_POST);
        $db = dbConn();
        $sql1 = "DELETE FROM teacher_degrees WHERE application_id = '$Id'";
        $sql2 = "DELETE FROM teacher_experience WHERE application_id   = '$Id'";
        $sql3 = "DELETE FROM teacher_grades WHERE application_id = '$Id'";

        $sql4 = "DELETE FROM teachers WHERE Id = '$Id'";
        $db->query($sql1);
        $db->query($sql2);
        $db->query($sql3);
        $db->query($sql4);

        header("Location:view.php");
        ?>

    </div>
</div>




<?php
$content = ob_get_clean();
include '../layouts.php';
?>