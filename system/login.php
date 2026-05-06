<?php
include '../init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in (v2)</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= SYS_URL ?>plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= SYS_URL ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= SYS_URL ?>dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary bg-success">
      <div class="card-header text-center">
        <a href="" class="h1"><b>ScienceMore</b></a>
      </div>
      <div class="card-body" style="background-color:rgb(174, 230, 176)" ;>
        <p class="login-box-msg text-dark">Sign in to start your session</p>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          extract($_POST);

          @$email = dataclean($email);

          $messages = array();

          //Empty Validation
          if (empty($email)) {
            $messages['email'] = "The email should not be blanked!...";

            if (empty($password)) {
              $messages['password'] = "The password should not be blanked!...";
            }
          }

          if (empty($messages)) {
            $db = dbConn();
            $sql = "SELECT  u.Id,u.LastName,u.user_role,r.description,u.Password FROM users u INNER JOIN user_roles r ON r.Id = u.user_role WHERE Email = '$email'";
            $result = $db->query($sql);
            $row = $result->fetch_assoc();

            if ($result->num_rows == 1) {

              if (password_verify($password, $row['Password'])) {
                $_SESSION['ID'] = $row['Id'];
                $_SESSION['NAME'] = $row['LastName'];
                $_SESSION['USERROLE'] = $row['user_role'];
                $_SESSION['USERROLENAME'] = $row['description'];

               // Store Teacher ID if role is teacher
                if ($row['description'] == 'Teacher') {
                  $userid = $row['Id'];
                  $sql_teacher = "SELECT Id FROM teachers WHERE userid = '$userid'";
                  $result_teacher = $db->query($sql_teacher);

                  if ($result_teacher->num_rows == 1) {
                    $teacher_row = $result_teacher->fetch_assoc();
                    $_SESSION['TEACHER_ID'] = $teacher_row['Id'];
                  }
                }
                header("Location:index.php");
              } else {
                $messages['Invalid'] = "1.Username or password is invalid!";
              }
            } else {
              $messages['Invalid'] = "2.Username or password is invalid!";
            }
          }
        }
        ?>


        <p class="login-box-msg text-danger"><?= @$messages['email'] ?></p>
        <p class=" login-box-msg text-danger"><?= @$messages['password'] ?></p>
        <p class=" login-box-msg text-danger"><?= @$messages['Invalid'] ?></p>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate>
          <div class="input-group mb-3">
            <input type="text" id="email" name="email" class="form-control" placeholder="Username">

            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" id="password" name="password" class="form-control" placeholder="Password">

            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember" class="text-dark">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block bg-success">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>


        <!-- /.social-auth-links -->

       

      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="<?= SYS_URL ?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= SYS_URL ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= SYS_URL ?>dist/js/adminlte.min.js"></script>
</body>

</html>