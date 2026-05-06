<?php
extract($_GET);
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($payslip)) {

    $file_extension = pathinfo($payslip, PATHINFO_EXTENSION);

    if ($file_extension == 'pdf') {

?>
        <iframe src="<?= $payslip ?>" frameborder="0" width="100%" height="100%"></iframe>

    <?php
    } else {

    ?>
        <img src="<?= $payslip ?>" alt="" width="100%" height="auto">
<?php
    }
}




?>