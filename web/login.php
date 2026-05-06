<?php
ob_start();
include '../init.php';
?>

</div>
</header>

<div class="container">




    </header>

    <div class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow-lg p-4 rounded-4" style="width: 100%; max-width: 500px;">
            <div class="card-body">
                <h3 class="card-title text-center text-success fw-bold mb-3">ScienceMore</h3>


                <?php
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    extract($_POST);
                    $email = dataClean($email);

                    $messages = [];

                    if (empty($email)) {
                        $messages['email'] = 'Email should not be blank!';
                    }
                    if (empty($Password)) {
                        $messages['Password'] = "Password should not be blank!";
                    }

                    if (empty($messages)) {
                        $db = dbConn();
                        $sql = "SELECT 
    u.Id AS user_id, 
    u.LastName, 
    u.user_role, 
    r.description, 
    u.Password,
    s.reg_no,
    s.Id AS student_id,
    p.Id AS parent_id          
FROM users u
INNER JOIN user_roles r ON r.Id = u.user_role
LEFT JOIN students s ON u.Id = s.userid
LEFT JOIN parents p ON p.Userid = u.Id
WHERE u.Email = '$email'";

                        $result = $db->query($sql);
                        $row = $result->fetch_assoc();

                        if ($result->num_rows == 1 && password_verify($Password, $row['Password'])) {

                            // ALLOW ONLY Parent or Student
                            if (in_array($row['description'], ['Parent', 'Student'])) {
                                $_SESSION['ID'] = $row['user_id'];
                                $_SESSION['NAME'] = $row['LastName'];
                                $_SESSION['USERROLE'] = $row['user_role'];
                                $_SESSION['USERROLENAME'] = $row['description'];

                                // Redirect based on role
                                if ($row['description'] == 'Parent') {
                                    $_SESSION['PARENT_ID'] = $row['parent_id'];

                                    header("Location: parent/dashboard.php");
                                }
                                if ($row['description'] == 'Student') {
                                    $_SESSION['REGNO'] = $row['reg_no'];
                                    $_SESSION['STUDENT_ID'] = $row['student_id'];

                                    header("Location: student/dashboard.php");
                                }
                            }
                        } else {
                            $messages['Invalid'] = "Email or Password is incorrect.";
                        }
                    }
                }
                ?>

                <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate>
                    <?php if (isset($messages['Invalid'])): ?>
                        <div class="alert alert-danger text-center"><?= $messages['Invalid'] ?></div>
                    <?php endif; ?>

                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Your Email">
                        <label for="email">Email address</label>
                        <span class="text-danger"><?= @$messages['email'] ?></span>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="Password" id="Password" class="form-control" placeholder="Password">
                        <label for="Password">Password</label>
                        <span class="text-danger"><?= @$messages['Password'] ?></span>
                    </div>

                  

                    <button type="submit" class="btn btn-success w-100 py-2">Login</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    $content = ob_get_clean();
    include 'layouts.php';
    ?>