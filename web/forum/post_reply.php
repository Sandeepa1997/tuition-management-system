<?php
include '../../init.php';
$db = dbConn();
extract($_POST);

echo $parent_post_id = isset($parent_post_id ) ? $parent_post_id : 'NULL';

$studentid = $_SESSION['STUDENT_ID']; // Assumes login session

if ($message != '') {
    $db->query("INSERT INTO forum_posts (topic_id, user_id, message, parent_post_id) VALUES ('$topic_id', '$studentid', '$message', '$parent_post_id')");
}
header("Location: view_topic.php?id=$topic_id");
exit;