<?php
include '../../init.php';

$sub_id = $_POST['sub_id'] ?? '';
$grade_id = $_POST['grade_id'] ?? '';

$db = dbConn();

$sql = "SELECT DISTINCT t.Id, t.FirstName, t.LastName 
        FROM teachers t
        JOIN teacher_grades tg ON t.Id = tg.application_id
        WHERE t.Status = '1' 
          AND t.subject_id = '$sub_id' 
          AND tg.grade_level_id = '$grade_id'";

$result = $db->query($sql);
?>
<option value="">--</option>
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <option value="<?= $row['Id'] ?>"><?= $row['FirstName'] ?> <?= $row['LastName'] ?></option>
        <?php
    }
} else {
    ?>
    <option value="">No matching teachers</option>
    <?php
}
?>

