<?php
include '../init.php';

$email="prashankannangara@gmail.com";
$subject = "BIT Registration";
$body = "<h1>Registration Success</h1>";
$body="<p> your Registration No is 202574849";

sendEmail($email,$subject,$body);
?>