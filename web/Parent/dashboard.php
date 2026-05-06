<?php
ob_start();
include '../../init.php';
?>

<?php
$db = dbConn();

$userId = $_SESSION['ID'];
$sqlUser = "SELECT NIC_No FROM users WHERE Id = $userId";
$resultUser = $db->query($sqlUser);
$userNIC = $resultUser->fetch_assoc()['NIC_No'];

$sqlstudent = "SELECT Id FROM students WHERE guardian_id = '$userNIC'";
$resultstudent = $db->query($sqlstudent);
?>

<div class="container mt-3">
  <?php
  if ($resultstudent->num_rows > 0) {
    while ($student = $resultstudent->fetch_assoc()) {
      $studentId = $student['Id'];

      //completed exams
      $sqlresults = "SELECT s.name AS subject,t.term AS exam_term,e.status
            FROM exams e
            JOIN classes c ON e.class_id= c.Id
            JOIN terms t ON e.term_id = t.Id
            JOIN subjects s ON c.Subject_id=s.Id
            JOIN student_enroll se ON se.class_id = c.Id
            Where se.student_id = $studentId AND e.status='Completed'";

      $exm_results = $db->query($sqlresults);
      if ($exm_results->num_rows > 0) {
        while ($row_results = $exm_results->fetch_assoc()) {
          if ($row_results['status'] == 'Completed') {
  ?>
            <div class="alert alert-info text-center">
              📢<strong><?= $row_results['exam_term'] ?> of <?= $row_results['subject'] ?></strong> results have been released.
            </div>
          <?php
          }
        }
      } // Missing closing bracket for if ($exm_results->num_rows > 0)
      
      $sqlmeeting = "SELECT c.Class_Name, m.type, m.status, m.date, m.start_time, m.end_time
            FROM meetings m
            JOIN classes c ON m.class_id = c.Id  
            JOIN student_enroll se ON se.class_id = c.Id        
            WHERE se.student_id = $studentId AND m.status = 'Scheduled'";

      $meeting_results = $db->query($sqlmeeting);
      if ($meeting_results->num_rows > 0) {
        while ($row_meetings = $meeting_results->fetch_assoc()) {
          ?>
            <div class="alert alert-success text-center">
              📅 <strong><?= $row_meetings['type'] ?> for <?= $row_meetings['Class_Name'] ?></strong> has been scheduled on <?= date('d-m-Y', strtotime($row_meetings['date'])) ?> at <?= date('g:i A', strtotime($row_meetings['start_time'])) ?>
            </div>
          <?php
        }
      } // Missing closing bracket for if ($meeting_results->num_rows > 0)
    } // Missing closing bracket for while ($student = $resultstudent->fetch_assoc())
  } // Missing closing bracket for if ($resultstudent->num_rows > 0)
  ?>
</div>

<section id="why-us" class="section why-us py-5">
  <div class="container">
    <div class="row text-center mb-4">
      <h2 class="text-success fw-bold">Welcome <?= $_SESSION['NAME'] ?></h2>
    </div>

    <div class="row gy-4 justify-content-center">

      <!-- Appointments -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 text-center p-3" style="background-color: #f0f4f8;">
          <div class="card-body">
            <i class="bi bi-calendar2-check display-4 text-primary mb-3"></i>
            <h5 class="fw-bold">Meetings</h5>
            <a href="view_meeting.php" class="btn btn-primary">View</a>
          </div>
        </div>
      </div>

      <!-- Payments -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 text-center p-3" style="background-color: #f0f4f8;">
          <div class="card-body">
            <i class="bi bi-cash-coin display-4 text-success mb-3"></i>
            <h5 class="fw-bold">Payments</h5>
            <p class="small text-muted">Make Payment from here</p>
            <a href="<?= WEB_URL ?>payments/payment_details.php" class="btn btn-sm btn-primary mt-4">Make Payments</a>
            <a href="view_pay.php" class="btn btn-sm btn-success mt-4">View Payments</a>
          </div>
        </div>
      </div>

      <!-- Child Progress -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 text-center p-3" style="background-color: #f0f4f8;">
          <div class="card-body">
            <i class="bi bi-bar-chart-line display-4 text-info mb-3"></i>
            <h5 class="fw-bold">Child Marks</h5>
            <p class="small text-muted">Check Your Child Marks</p>
            <a href="view_progress.php" class="btn btn-sm btn-primary mt-2">View Progress</a>
          </div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 text-center p-3" style="background-color: #f0f4f8;">
          <div class="card-body">
            <!-- New Icon -->
            <i class="fas fa-book fa-2x text-primary mb-2"></i>
            <h5 class="fw-bold">Progress Report</h5>
            <p class="small text-muted">Check Your Child's Progress</p>
            <a href="progress_report.php" class="btn btn-sm btn-primary mt-2">View All</a>
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