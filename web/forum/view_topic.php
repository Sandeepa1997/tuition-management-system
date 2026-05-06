<?php
ob_start();
include '../../init.php';
$db = dbConn();

extract($_GET);
$topic_id = $id;

// Get topic details
$sql = "SELECT T.*, U.FirstName, U.LastName 
        FROM forum_topics T
        INNER JOIN users U ON U.Id = T.created_by
        WHERE T.id = $topic_id";
$topic = $db->query($sql)->fetch_assoc();

// Get parent posts
$sql = "SELECT P.*, U.FirstName, U.LastName 
        FROM forum_posts P
        INNER JOIN users U ON U.Id = P.user_id
        WHERE P.topic_id = $topic_id AND P.parent_post_id = 0
        ORDER BY P.created_at ASC";
$posts = $db->query($sql);
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4><?= htmlspecialchars($topic['title']) ?></h4>
            <small>Started by <?= $topic['FirstName'] . ' ' . $topic['LastName'] ?> on <?= $topic['created_at'] ?></small>
        </div>
        <div class="card-body">
            <?php if ($posts->num_rows > 0) { ?>
                <?php while ($post = $posts->fetch_assoc()) { ?>
                    <div class="mb-4 border rounded p-3">
                        <p><strong><?= $post['FirstName'] . ' ' . $post['LastName'] ?></strong> said:</p>
                        <p><?= nl2br(htmlspecialchars($post['message'])) ?></p>
                        <small class="text-muted"><?= $post['created_at'] ?></small>

                        <!-- Reply form -->
                        <form method="post" action="post_reply.php" class="mt-2">
                            <input type="hidden" name="topic_id" value="<?= $topic_id ?>">
                            <input type="hidden" name="parent_post_id" value="<?= $post['id'] ?>">
                            <textarea name="message" rows="2" class="form-control" placeholder="Write a reply..." required></textarea>
                            <button type="submit" class="btn btn-sm btn-outline-primary mt-1">Reply</button>
                        </form>

                        <!-- Replies -->
                        <?php
                        $post_id = $post['id'];
                        $replies = $db->query("SELECT R.*, U.FirstName, U.LastName 
                                               FROM forum_posts R
                                               INNER JOIN users U ON U.Id = R.user_id
                                               WHERE R.parent_post_id = $post_id
                                               ORDER BY R.created_at ASC");
                        if ($replies->num_rows > 0) {
                            echo '<div class="mt-3 ms-4">';
                            while ($reply = $replies->fetch_assoc()) {
                                ?>
                                <div class="border-start ps-3 mb-2">
                                    <p><strong><?= $reply['FirstName'] . ' ' . $reply['LastName'] ?></strong> replied:</p>
                                    <p><?= nl2br(htmlspecialchars($reply['message'])) ?></p>
                                    <small class="text-muted"><?= $reply['created_at'] ?></small>
                                </div>
                                <?php
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No posts yet. Be the first to comment!</p>
            <?php } ?>
        </div>
    </div>

    <!-- Add new post -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h5>Add a New Post</h5>
        </div>
        <div class="card-body">
            <form method="post" action="post_reply.php">
                <input type="hidden" name="topic_id" value="<?= $topic_id ?>">
                <textarea name="message" rows="4" class="form-control" placeholder="Write your message..." required></textarea>
                <button type="submit" class="btn btn-success mt-2">Post</button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../layouts.php';
?>
