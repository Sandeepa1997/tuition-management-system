<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header('Location: ../login.php');
    exit;
}
?>

<?php
$db=dbConn();
$studentId=$_SESSION['STUDENT_ID'];
$subject_id=$_GET['subject_id'];

//Get materials for the subject
$sql = "SELECT lm.description, lm.material, s.name AS subject,title
        FROM learning_materials lm
        JOIN learning_material_titles lmt ON lm.title_id = lmt.id
        JOIN classes c ON lmt.class_id = c.Id
        JOIN subjects s ON c.Subject_id = s.Id
        JOIN student_enroll se ON se.class_id = c.Id
        WHERE se.student_id = $studentId AND c.Subject_id = $subject_id AND lm.status = 1";


$result=$db->query($sql);

?>
<div class="container mt-4">
    <div class="card shadow bg-light">
    <h3 class="text-center mb-4 fw-bold mt-3">Learning Materials</h3>
    </div>
    <?php
    if ($result->num_rows>0){?>
    <table class="table table-bordered mt-3">
       <thead class="table-success">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Download</th>
        </tr>
       </thead>
    </tbody>
<?php
while($row=$result->fetch_assoc()){
    ?>
    <tr>
        <td><?=$row['title']?></td>
        <td><?=$row['description']?></td>
        <td>
            <a href="../../system/uploads/?=$row['material']?>" traget="_blank" class="btn btn-sm btn-primary">Download</a>
        </td>
        </tr>
        <?php } ?>
</tbody>


    </table>

    <?php } else{?>
<p class="text-center"> No learning materials found for this subject.</p>
  <?php  }?>
</div>




<?php
$content = ob_get_clean();
include '../layouts.php';
?>