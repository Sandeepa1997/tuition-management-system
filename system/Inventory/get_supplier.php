<?php
include '../../init.php';
extract($_POST);


$db=dbConn();

$sql="SELECT * FROM suppliers WHERE Id ='$supplier_id' ";
$result = $db->query($sql);
$row=$result->fetch_assoc();

echo $row['Title']." ".$row['FirstName']." ".$row['LastName']." ".$row['ContactNo'];



?>