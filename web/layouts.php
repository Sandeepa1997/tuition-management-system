<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Sciencemore</title>
  <meta name="description" content="">
  <meta name="keywords" content="">


  <!-- Favicons -->
  <link href="<?= WEB_URL ?>img/favicon.png" rel="icon">
  <link href="<?= WEB_URL ?>img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= WEB_URL ?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= WEB_URL ?>vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= WEB_URL ?>vendor/aos/aos.css" rel="stylesheet">
  <link href="<?= WEB_URL ?>vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?= WEB_URL ?>vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <script src="<?= WEB_URL ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Main CSS File -->
  <link href="<?= WEB_URL ?>css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Mentor
  * Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">

    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">ScienceMore</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <div class="d-flex justify-content-between align-items-center flex-wrap w-100">

          <!-- Left Side: Navigation Links -->
          <ul class="d-flex gap-3 mb-0 flex-wrap">
            <li><a href="<?= WEB_URL ?>index.php" class="active">Home</a></li>
            <li><a href="<?= WEB_URL ?>Teachers/register.php">Teachers</a></li>
            <li><a href="<?= WEB_URL ?>contact/contact.php">Contact</a></li>

          </ul>

          <!-- Right Side: Auth Buttons -->
          <ul class="d-flex gap-2 mb-0 flex-wrap">
            <?php if (isset($_SESSION['ID']) && isset($_SESSION['USERROLENAME'])) { ?>
              <li>
                <span class="badge bg-success px-4 py-2 rounded-pill text-white fw-medium">
                  Welcome, <?= $_SESSION['NAME'] ?>
                </span>
              </li>

              <li>
                <?php if ($_SESSION['USERROLENAME'] == 'Student') { ?>
                  <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>student/dashboard.php">Dashboard</a>
                <?php } else { ?>
                  <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>parent/dashboard.php">Dashboard</a>
                <?php } ?>
              </li>

              <li>
                <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>logout.php">Log out</a>
              </li>
            <?php } else { ?>
              <li>
                <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>register/register.php">Register</a>
              </li>
              <li>
                <a class="btn btn-success px-4 py-2 rounded-pill text-white fw-medium" href="<?= WEB_URL ?>login.php">Log in</a>
              </li>
            <?php } ?>
          </ul>
        </div>

        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>



    </div>
  </header>

  <?= $content ?>

  <footer id="footer" class="footer position-relative light-background">
    <div class="container footer-top">
      <div class="row gy-4">

        <!-- About Section -->
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">ScienceMore</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Negombo Road</p>
            <p>Godigamuwa, Badalgama</p>
            <p class="mt-3"><strong>Phone:</strong> <span>077 9221617 / 031-4478147</span></p>
            <p><strong>Email:</strong> <span>sciencemoreinfo@gmail.com</span></p>
          </div>
        </div>

        <!-- Useful Links -->
        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Contact Us</a></li>
          </ul>
        </div>

        <!-- Subjects -->
        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Subjects</h4>
          <ul>
            <li><a href="#">Science</a></li>
            <li><a href="#">Mathematics</a></li>
            <li><a href="#">English</a></li>
          </ul>
        </div>

        <!-- Social Media -->
        <div class="col-lg-4 col-md-6 footer-social">
          <h4>Follow Us</h4>
          <p>Stay connected through social media</p>
          <div class="d-flex gap-3 mt-2">
            <a href="#"><i class="bi bi-facebook" style="font-size: 1.5rem;"></i></a>
            <a href="#"><i class="bi bi-instagram" style="font-size: 1.5rem;"></i></a>
            <a href="#"><i class="bi bi-youtube" style="font-size: 1.5rem;"></i></a>
          </div>
        </div>

      </div>
    </div>
  </footer>



  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="<?= WEB_URL ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= WEB_URL ?>vendor/php-email-form/validate.js"></script>
  <script src="<?= WEB_URL ?>vendor/aos/aos.js"></script>
  <script src="<?= WEB_URL ?>vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?= WEB_URL ?>vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="<?= WEB_URL ?>vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="<?= WEB_URL ?>js/main.js"></script>

</body>

</html>