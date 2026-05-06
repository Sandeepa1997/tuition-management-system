<?php
ob_start();
include '../../init.php';

// Confirm whether login to the system    
if (!isset($_SESSION['ID'])) {
  header("Location: ../login.php");
  exit;
}
$regNo = $_SESSION['REGNO'];
$studentId = $_SESSION['STUDENT_ID'];


$db = dbConn();

// Check if there's at least one scheduled exam
$sqlCheck = "SELECT s.name AS subject, t.term AS exam_term,e.status 
             FROM exams e
             JOIN classes c ON e.class_id = c.Id
             JOIN terms t ON e.term_id = t.Id
             JOIN subjects s ON c.Subject_id = s.Id
             JOIN student_enroll se ON se.class_id = c.Id
             WHERE se.student_id = $studentId AND e.status IN ('Scheduled','Postponed','Cancelled')";


$result = $db->query($sqlCheck);

$sqlresults = "SELECT s.name AS subject, t.term AS exam_term, e.status, ea.attended
               FROM exams e
               JOIN classes c ON e.class_id = c.Id
               JOIN terms t ON e.term_id = t.Id
               JOIN subjects s ON c.Subject_id = s.Id
               JOIN student_enroll se ON se.class_id = c.Id
               LEFT JOIN exam_attendance ea ON ea.exam_id = e.id AND ea.student_id = se.student_id
               WHERE se.student_id = $studentId 
                 AND e.status = 'Completed'";


$exm_results = $db->query($sqlresults);

//Learning Material query
$material_sql = "SELECT DISTINCT s.Id,s.name AS subject,lm.status
 FROM student_enroll se 
 JOIN classes c ON se.class_id=c.Id
 JOIN learning_material_titles lm ON c.Id = lm.class_id
 JOIN subjects s ON c.Subject_id = s.Id
 WHERE se.student_id = $studentId";

$material_result = $db->query($material_sql);

// Quiz notification query
  $sqlQuiz = "SELECT q.title, s.name AS subject
            FROM quizzes q
            JOIN classes c ON q.class_id = c.Id
            JOIN subjects s ON c.Subject_id = s.Id
            JOIN student_enroll se ON se.class_id = c.Id
            WHERE se.student_id = $studentId AND q.status='Ongoing'";
$quiz_result = $db->query($sqlQuiz);

//Assignment Notification Query
$sqlass = "SELECT at.title, s.name AS subject,a.due_date,t.term
            FROM assignment_titles at
            JOIN classes c ON at.class_id = c.Id
            JOIN terms t ON at.term_id = t.Id
            JOIN assignments a ON a.title_id = at.Id
            JOIN subjects s ON c.Subject_id = s.Id
            JOIN student_enroll se ON se.class_id = c.Id
            WHERE se.student_id = $studentId AND at.status='Ongoing'";
$assignment = $db->query($sqlass);

?>






<section id="why-us" class="section why-us">
  <div class="container">
    <div class="row gy-4">

    



      <!-- Registration Number Card -->
      <div class="row justify-content-end mb-4">
        <div class="col-md-3 col-sm-4">
          <div class="card text-center shadow-sm" style="background-color: #e0f2f1; border-radius: 12px;">
            <div class="card-body p-2">
              <small class="text-muted">Registration No</small>
              <p class="card-text fw-bold mb-0" style="font-size: 1rem;"><?= $regNo ?></p>
            </div>
          </div>
        </div>
      </div>

<div class="row justify-content-start mb-3 ms-1">
  <div class="col-auto">
    <a href="../forum/show_subjects.php" class="btn btn-primary btn-lg px-5 py-2" style="border-radius: 30px;">
      <i class="bi bi-chat-dots-fill"></i> Forum
    </a>
  </div>
</div>


      <!--results Notifications-->
      <?php if ($exm_results->num_rows > 0 && empty($_SESSION['viewed_results'])) { ?>
        <?php while ($row_results = $exm_results->fetch_assoc()) { ?>
          <?php if ($row_results['status'] == 'Completed') { ?>
            <?php if ($row_results['attended'] === '1') { ?>
              <div class="alert alert-info text-center mt-3">
                📢 <p>The results of <strong><?= $row_results['exam_term'] ?> - <?= $row_results['subject'] ?></strong> have been released. Check your results section.</p>
              </div>
            <?php } elseif ($row_results['attended'] === '0') { ?>
              <div class="alert alert-warning text-center mt-3">
                ⚠️ <p>You were <strong>absent</strong> for <strong><?= $row_results['exam_term'] ?> - <?= $row_results['subject'] ?></strong> exam.</p>
              </div>
            <?php } ?>
          <?php } ?>
        <?php } ?>
      <?php } ?>


      <!--material Notifications-->
      <?php if ($material_result->num_rows > 0 && empty($_SESSION['viewed_material'])) { ?>
        <?php while ($row_results_1 = $material_result->fetch_assoc()) { ?>
          <?php if ($row_results_1['status'] == '1') { ?>
            <div class="alert alert-info text-center mt-3">
              📢 <p>new material of <strong><?= $row_results_1['subject'] ?></strong> available. Check your learning material section.</p>
            </div>
          <?php } ?>
        <?php } ?>
      <?php } ?>



      <!--Exam Notifications-->
      <div class="container">
        <?php if ($result->num_rows > 0) { ?>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="alert alert-info text-center mt-3">
              <?php if ($row['status'] == 'Scheduled') { ?>
                📢 <strong><?= $row['exam_term'] ?> </strong> test of <strong><?= $row['subject'] ?></strong> has been <strong>Scheduled</strong>. Check your exam section.
              <?php } elseif ($row['status'] == 'Postponed') { ?>
                ⚠️ <strong><?= $row['exam_term'] ?></strong> test of <strong><?= $row['subject'] ?></strong> has been <strong>Postponed</strong>. Stay tuned for updates.
              <?php } elseif ($row['status'] == 'Cancelled') { ?>
                ❌ <strong><?= $row['exam_term'] ?></strong> test of <strong><?= $row['subject'] ?></strong> has been <strong>Cancelled</strong>.
              <?php } ?>
            </div>
          <?php } ?>
        <?php } ?>
      </div>


      <!-- Quiz Notifications -->
      <?php if ($quiz_result->num_rows > 0 && empty($_SESSION['viewed_quiz'])) { ?>
        <?php while ($quiz_row = $quiz_result->fetch_assoc()) { ?>
          <div class="alert alert-info text-center mt-3">
            📝 <strong><?= $quiz_row['title'] ?></strong> quiz in <strong><?= $quiz_row['subject'] ?></strong> is now available. Check your quiz section to attempt it.
          </div>
        <?php } ?>
      <?php } ?>


      <!-- Feature Cards Grid -->
      <div class="col-12">
        <div class="row gy-4 gx-4">     

          <div class="col-xl-4 col-md-6">
            <a href="cls_schedule.php" style="text-decoration: none; color: inherit;">
              <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100 p-3"
                style="background-color: #e6f4ea; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 40px;">
                <i class="bi bi-clipboard-data" style="font-size: 2rem;"></i>
                <h4 class="mt-2">Classes</h4>
                <span style="font-size: 0.9rem;">View your enrolled classes and subjects</span>
              </div>
            </a>
          </div>

          <div class="col-xl-4 col-md-6">
            <a href="../student_exam/exam_results.php" style="text-decoration: none; color: inherit;">
              <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100 p-3"
                style="background-color: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; border-radius: 10px; cursor:pointer;"
                data-bs-toggle="collapse" data-bs-target="#resultsTable">
                <i class="bi bi-bar-chart" style="font-size: 2rem;"></i>
                <h4 class="mt-2">View Results</h4>
                <span style="font-size: 0.9rem;">Click to see your marks</span>
              </div>
            </a>
          </div>


          <div class="col-xl-4 col-md-6">
            <a href="../student_exam/exam_card.php" style="text-decoration: none; color: inherit;">
              <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100 p-3"
                style="background-color: #f3e5f5; color: #6a1b9a; border: 1px solid #e1bee7; border-radius: 10px;">
                <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                <h4 class="mt-2">Exams</h4>
              </div>
            </a>
          </div>


          <div class="col-xl-4 col-md-6">
            <a href="../student_assignments/student_assignments.php" style="text-decoration: none; color: inherit;">
              <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100 p-3"
                style="background-color: #fff3e0; color: #ef6c00; border: 1px solid #ffe0b2; border-radius: 40px; position: relative;">
                <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                <h4 class="mt-2 mb-1">Your Assignments</h4>

                <?php if ($assignment->num_rows > 0) : ?>
                  <?php while ($row = $assignment->fetch_assoc()) : ?>
                    <?php
                    $due = date('M d', strtotime($row['due_date']));
                    $today = date('Y-m-d');
                    $isUpcoming = strtotime($row['due_date']) >= strtotime($today);
                    ?>
                    <?php if ($isUpcoming): ?>
                      <div class="text-center mt-2" style="font-size: 0.75rem; background-color: #74ec64ff; color: #092007ff; padding: 4px 8px; border-radius: 8px;">
                        <?= $row['term'] ?>-
                        <?= $row['title'] ?>-
                        Due <?= $due ?>
                      </div>
                      <?php break; // Only show one upcoming assignment notice 
                      ?>
                    <?php endif; ?>
                  <?php endwhile; ?>
                <?php endif; ?>
              </div>
            </a>
          </div>


          <div class="col-xl-4 col-md-6">
            <a href="../student_materials/learning_materials.php" style="text-decoration: none; color: inherit;">
              <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100 p-3"
                style="background-color: #e0f7fa; color: #00838f; border: 1px solid #b2ebf2; border-radius: 40px;">
                <i class="bi bi-cloud-arrow-down" style="font-size: 2rem;"></i>
                <h4 class="mt-2">Download Learning Materials</h4>
              </div>
            </a>
          </div>

          <div class="col-xl-4 col-md-6">
            <div class="icon-box d-flex flex-column justify-content-center align-items-center h-100 p-3"
              style="background-color: #fff8e1; color: #f9a825; border: 1px solid #ffecb3; border-radius: 40px;">
              <i class="bi bi-question-circle" style="font-size: 2rem;"></i>
              <a href="../student_quiz/student_quizzes.php" style="text-decoration:none; color:inherit;">
                <h4 class="mt-2">Attempt Quizzes</h4>
              </a>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>