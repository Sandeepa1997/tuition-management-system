<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header("Location: ../login.php");
    exit;
}

$regNo = $_SESSION['REGNO'];
$studentId = $_SESSION['STUDENT_ID'];
$_SESSION['viewed_results'] = true; 

$db = dbConn();

$selectedSubjectId = $_POST['subject_id'] ?? null;

// Step 1: Fetch enrolled subjects
$sqlSubjects = "SELECT DISTINCT s.Id, s.name 
                FROM subjects s
                JOIN classes c ON c.Subject_id = s.Id
                JOIN student_enroll se ON se.class_id = c.Id
                WHERE se.student_id = $studentId";
$resultSubjects = $db->query($sqlSubjects);
?>



    <!-- Step 1: Subject List -->
    <h4 class="text-center mb-3">Select a Subject</h4>
    <div class="row justify-content-center mb-4">
      <?php while ($subject = $resultSubjects->fetch_assoc()) { ?>
        <div class="col-md-3 mb-2">
          <form method="post">
            <input type="hidden" name="subject_id" value="<?= $subject['Id'] ?>">
            <button type="submit" class="btn btn-outline-primary w-100"><?= $subject['name'] ?></button>
          </form>
        </div>
      <?php } ?>
    </div>

    <!-- Step 2: Show Results If Subject Selected -->
    <?php if ($selectedSubjectId): 
      $sql = "SELECT 
                t.term AS exam_term,
                s.name AS subject,     
                r.marks
              FROM exam_results r
              JOIN exams e ON r.exam_id = e.Id
              JOIN terms t ON e.term_id = t.Id
              JOIN classes c ON e.class_id = c.Id
              JOIN subjects s ON c.Subject_id = s.Id
              WHERE r.student_id = $studentId 
                AND e.status = 'Completed'
                AND s.Id = $selectedSubjectId";
      $result = $db->query($sql);
    ?>


    <?php if ($result->num_rows > 0) { ?>
      <div class="container">
      <div class="table-responsive">
        <table class="table table-bordered text-center">
          <thead class="table-success">
            <tr>
              <th>Term</th>
              <th>Subject</th>           
              <th>Marks</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) {
              $status = '';
              $rowClass = '';

              if (($row['marks'])=='null') {
                  $status = 'Absent';
                  $rowClass = 'style="background-color:rgba(240, 116, 34, 1);"';
              } elseif ($row['marks'] >= 75) {
                  $status = 'A';
                  $rowClass = 'bg-success';
              } elseif ($row['marks'] >= 65) {
                  $status = 'B';
                  $rowClass = 'style="background-color:rgb(116, 245, 66);"';
              } elseif ($row['marks'] >= 55) {
                  $status = 'C';
                  $rowClass = 'style="background-color:rgb(116, 245, 66);"';
              } elseif ($row['marks'] >= 35) {
                  $status = 'S';
                  $rowClass = 'bg-warning';
              } else {
                  $status = 'F';
                  $rowClass = 'bg-danger';
              }
            ?>
              <tr <?= strpos($rowClass, 'style=') === 0 ? $rowClass : 'class="' . $rowClass . '"' ?>>
                <td><?= $row['exam_term'] ?></td>
                <td><?= $row['subject'] ?></td>       
                <td><?= $row['marks'] =='null'? '-' :$row['marks'] ?></td>
                <td><?= $status ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        </div>
      </div>
    <?php } else { ?>
      <div class="alert alert-warning text-center mt-3">No results available for this subject.</div>
    <?php } ?>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
