<?php
ob_start();
include '../../init.php';
$db = dbConn();
extract($_GET);


if (!empty($order_id)) {
    $db->query("UPDATE student_payment SET status='Paid' AND date='$payment_date' WHERE Id='$order_id'");
  
    
        echo '<script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Payment Success!!...",
                showConfirmButton: false,
                timer: 2000
            }).then(function(){
                window.location.href="../parent/dashboard.php"
            });
        </script>';
    
}

$content = ob_get_clean();
include '../layouts.php';
