<?php
ob_start();
include '../../init.php';
?>
<?php
extract($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && @$action == 'check_date') {

   $db = dbConn();


   $sql = "SELECT ADDTIME(TIME(appointment_time),'00:15:00') AS nexttime FROM appointments WHERE 
appointment_date='$date' ORDER BY appointment_time DESC LIMIT 1;";

   $result = $db->query($sql);

   $row = $result->fetch_assoc();




   //$next_app_time = new DateTime($row['appointment_time']);
   //$next_app_time->modify('+15 minutes');

   //echo $next_time = $next_app_time->format('H:i');

   $nextapptime = null;

   if ($result->num_rows > 0) {
   
      $nextapptime = $row['nexttime'];
   } else {
     $nextapptime = '09:30 Am';
   }

   
   
   echo "Date: $date";
   echo "<br>";
   echo "Next available time : " . $nextapptime;
   echo "<br>";

   $sql = "SELECT COUNT(*)+1 AS next_app_no FROM appointments WHERE appointment_date='$date'";

   $result = $db->query($sql);

   $row = $result->fetch_assoc();

   echo "Appointment Number:" . $row['next_app_no'];

 $_SESSION['APP_DATE']=$date;
 $_SESSION['APP_TIME']=$nextapptime;
 $_SESSION['APP_NO']=$row['next_app_no'];

 echo '<br>';

echo '<a href="confirm.php">Confirm My Appointment</a>';

}
?>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>

