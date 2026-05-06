<?php
ob_start();
include '../../init.php';
$gradeLevelId = $_GET['Grade_Level_id'] ?? $_SESSION['GRADE_LEVEL_ID'];
?>

</div>
</header>

<div class="container">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <p class="text-success fs-2 fw-bold">Main Subjects</p>
        <div class="border-bottom border-success border-3 mt-2" style="width: 10%;"></div>
    </div>

    <!-- Row for Courses -->
    <div class="row">

        <!-- Science -->
        <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
            <a href="../show_classes/class_details.php?subject_id=1&grade_level_id=<?= $gradeLevelId ?>">
                <div class="course-item">
                    <img src="../img/pic1.jpg" class="img-fluid rounded-4" alt="...">
                    <div class="course-content">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <p class="category mx-auto fs-4 bg-success text-white px-3 py-1 rounded mt-2">Science</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- English -->
        <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="200">
            <a href="../show_classes/class_details.php?subject_id=2&grade_level_id=<?= $gradeLevelId ?>">
                <div class="course-item">
                    <img src="../img/english.jpg" class="img-fluid rounded-4" alt="...">
                    <div class="course-content">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <p class="category mx-auto fs-4 bg-success text-white px-3 py-1 rounded mt-2">English</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Mathematics -->
        <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="300">
            <a href="../show_classes/class_details.php?subject_id=3&grade_level_id=<?= $gradeLevelId ?>">
                <div class="course-item">
                    <img src="../img/maths.jpg" class="img-fluid rounded-4" alt="...">
                    <div class="course-content">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <p class="category mx-auto fs-4 bg-success text-white px-3 py-1 rounded mt-2">Mathematics</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div> <!-- End Row -->

</div> <!-- End Container -->

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
