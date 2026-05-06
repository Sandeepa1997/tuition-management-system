<?php
ob_start();
include '../../init.php';

if (!isset($_SESSION['ID'])) {
    header('Location: ../login.php');
    exit;
}

$db = dbConn();


$classId = $_GET['class_id'] ?? null; // get class_id from URL

if (!$classId) {
    echo "Invalid class ID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);

    $title = dataclean($title);
    $user_id = $_SESSION['ID'];

    if (!empty($title)) {
        $sql = "INSERT INTO forum_topics (title,class_id,created_by) 
                VALUES ('$title','$classId','$user_id')";
        $db->query($sql);

        // Get subject_id to redirect properly
        $sqlSubject = "SELECT Subject_id FROM classes WHERE Id = '$classId'";
        $resultSubject = $db->query($sqlSubject);
        $rowSubject = $resultSubject->fetch_assoc();
        $subjectId = $rowSubject['Subject_id'];

        header("Location: forum_index.php?subject_id=$subjectId");
        exit;
    }
}
?>
<div class="container" style="max-width: 600px; margin-top: 30px;">
    <h2 style="margin-bottom: 20px;"> Start a New Forum Topic</h2>

    <form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <div style="margin-bottom: 15px;">
            <label for="title" style="font-weight: bold;">Topic Title:</label><br>
            <input type="text" id="title" name="title" size="60" required 
                   style="padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <button type="submit" style="padding: 10px 20px; background-color: #007BFF; color: #fff;
                border: none; border-radius: 5px; cursor: pointer;">
            Create Topic
        </button>
    </form>
</div>


<?php
$content = ob_get_clean();
include '../layouts.php';
?>
