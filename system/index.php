 <?php
      ob_start();
      include '../init.php';
      //confirm whether login to the system
      if (!isset($_SESSION['ID'])) {
            header("Location:login.php");
      }
      ?>

  <?php
      include 'dashboard_' . $_SESSION['USERROLE'] . '.php';

      ?>
        <?php
            $content = ob_get_clean();
            include 'layouts.php';
            ?>