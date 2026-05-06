<?php
ob_start();
include '../init.php';
?>



</div>
</header>

<main class="main">

  <!-- Hero Section -->
  <section id="hero" class="hero section dark-background">

    <img src="img/my.jpg" alt="" data-aos="fade-in">

    <div class="container">
      <h2 data-aos="fade-up" data-aos-delay="100">Learning Today,<br>Leading Tomorrow</h2>
      <p data-aos="fade-up" data-aos-delay="200"></p>
      <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">

      </div>
    </div>

  </section> <!-- /Hero Section -->



  <!-- Counts Section -->
  <section id="counts" class="section counts light-background">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row gy-4">

        <div class="col-lg-3 col-md-6">
          <div class="stats-item text-center w-100 h-100">
            <span data-purecounter-start="0" data-purecounter-end="1000" data-purecounter-duration="1" class="purecounter"></span>
            <p>Students</p>
          </div>
        </div><!-- End Stats Item -->

        <div class="col-lg-3 col-md-6">
          <div class="stats-item text-center w-100 h-100">
            <span data-purecounter-start="0" data-purecounter-end="3" data-purecounter-duration="1" class="purecounter"></span>
            <p>Courses</p>
          </div>
        </div><!-- End Stats Item -->


        <div class="col-lg-3 col-md-6">
          <div class="stats-item text-center w-100 h-100">
            <span data-purecounter-start="0" data-purecounter-end="3" data-purecounter-duration="1" class="purecounter"></span>
            <p>Teachers</p>
          </div>
        </div><!-- End Stats Item -->

      </div>

    </div>

  </section><!-- /Counts Section -->





  <!-- Courses Section -->
  <section id="courses" class="courses section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <p class="text-success">Find Your Class<br>which Grade are You in?</p>
      <div class="border-bottom border-success border-3 mt-2" style="width: 10%;"></div>

    </div><!-- End Section Title -->

    <div class="container">

      <div class="row">

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
          <div class="course-item">

            <div class="course-content">
              <div class="d-flex justify-content-center align-items-center mb-3">
                <a href="subjects/subjects.php?Grade_Level_id=1" style="text-decoration: none;">
                  <p class="category mx-auto fs-4 bg-success" style="border: none;">Grade 9</p>
                </a>
              </div>

              <h3><a href="course-details.html"></a></h3>

              <div class="trainer d-flex justify-content-between align-items-center">
                <div class="trainer-profile d-flex align-items-center">

                </div>

              </div>
            </div>
          </div>
        </div> <!-- End Course Item-->

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="200">
          <div class="course-item">

            <div class="course-content">
              <div class="d-flex justify-content-between align -items-center mb-3">

                <a href="subjects/subjects.php?Grade_Level_id=2" style="text-decoration: none;">
                  <p class="category mx-auto fs-4 bg-success" style="border: none;">Grade 10</p>
                </a>

              </div>



              <div class="trainer d-flex justify-content-between align-items-center">

              </div>
            </div>
          </div>
        </div> <!-- End Course Item-->

        <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in" data-aos-delay="300">
          <div class="course-item">

            <div class="course-content">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="subjects/subjects.php?Grade_Level_id=3" style="text-decoration: none;">
                  <p class="category mx-auto fs-4 bg-success" style="border: none;">Grade 11</p>
                </a>

              </div>


              <div class="trainer d-flex justify-content-between align-items-center">
                <div class="trainer-profile d-flex align-items-center">

                </div>
              </div>
            </div>
          </div>
        </div> <!-- End Course Item-->

      </div>

    </div>

  </section>

  <!-- Feedback Form-->
  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate method="post" enctype="multipart/form-data">
  <div class="container my-5">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-header bg-success text-white rounded-top-4 py-3">
        <h3 class="card-title mb-0 fw-bold">📝 Give Us Your Feedback</h3>
        <?php
        extract($_POST);
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
          $comment = dataclean($comment);

          $messages = array();
          if (empty($subject_id)) $messages['subject_id'] = "Please select a subject!";
          if (empty($grade_id)) $messages['grade_id'] = "Please select a grade!";
          if (empty($rate)) $messages['rate'] = "Please select a rating!";
          if (empty($teacher_id)) $messages['teacher_id'] = "Please select a teacher!";
          if (empty($comment)) $messages['comment'] = "Please type a comment!";

          if (empty($messages)) {
            $db = dbConn();
            $sql = "INSERT INTO feedback (Subject_id, grade_level_id, Teacher, comment, rate) VALUES ('$subject_id','$grade_id','$teacher_id','$comment','$rate')";
            $db->query($sql);
            echo "<div class='alert alert-success mt-3'>Thank you for your feedback!</div>";
          }
        }
        ?>
      </div>

      <div class="card-body p-4 bg-light">
        <div class="row g-4">

          <!-- Subject -->
          <div class="col-md-6">
            <label for="subject_id" class="form-label fw-semibold">📘 Subject</label>
            <select name="subject_id" id="subject_id" class="form-select" onchange="loadTeachers()">
              <option value="">-- Select Subject --</option>
              <?php
              $db = dbConn();
              $sql = "SELECT * FROM subjects";
              $result = $db->query($sql);
              while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
              }
              ?>
            </select>
            <small class="text-danger"><?= @$messages['subject_id'] ?></small>
          </div>

          <!-- Grade -->
          <div class="col-md-6">
            <label for="grade_id" class="form-label fw-semibold">🎓 Grade</label>
            <select name="grade_id" id="grade_id" class="form-select" onchange="loadTeachers()">
              <option value="">-- Select Grade --</option>
              <?php
              $sql = "SELECT * FROM grade_levels";
              $result = $db->query($sql);
              while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
              }
              ?>
            </select>
            <small class="text-danger"><?= @$messages['grade_id'] ?></small>
          </div>

          <!-- Rating -->
          <div class="col-md-6">
            <label for="rate" class="form-label fw-semibold">⭐ Rating</label>
            <select name="rate" id="rate" class="form-select">
              <option value="">-- Select Rating --</option>
              <option value="1">1 - Poor</option>
              <option value="2">2 - Good</option>
              <option value="3">3 - Excellent</option>
            </select>
            <small class="text-danger"><?= @$messages['rate'] ?></small>
          </div>

          <!-- Teacher -->
          <div class="col-md-6">
            <label for="teacher_id" class="form-label fw-semibold">👩‍🏫 Teacher</label>
            <select name="teacher_id" id="teacher_id" class="form-select">
              <option value="">-- Select Teacher --</option>
            </select>
            <small class="text-danger"><?= @$messages['teacher_id'] ?></small>
          </div>

          <!-- Comment -->
          <div class="col-12">
            <label for="comment" class="form-label fw-semibold">🗣️ Your Feedback</label>
            <textarea class="form-control" name="comment" id="comment" rows="5" placeholder="Write your comment here..."><?= @$comment ?></textarea>
            <small class="text-danger"><?= @$messages['comment'] ?></small>
          </div>

          <!-- Submit -->
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
              📤 Submit Feedback
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</form>

  <!--  End Feedback Form-->



  <!-- Ajax section -->
  <script>
    function loadTeachers() {
      var subjectId = $('#subject_id').val();
      var gradeId = $('#grade_id').val();

      if (subjectId && gradeId) {
        $.ajax({
          type: 'POST',
          url: 'Teachers/feedback.php',
          data: {
            sub_id: subjectId,
            grade_id: gradeId
          },
          success: function(response) {

            $('#teacher_id').html(response);
          }
        });
      } else {
        $('#teacher_id').html('<option value="">--</option>');
      }
    }
  </script>
  <!-- End -->


  </div>

  </div>
  </div>
  </div>










</main>
<?php
$content = ob_get_clean();
include 'layouts.php';
?>