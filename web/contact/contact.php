<?php
ob_start();
include '../../init.php';
?>

<!-- Header/Navbar here if needed -->

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Left Column: Contact Info -->
        <div class="col-md-6">
            <h2 class="text-success mb-4">Contact Us</h2>
            <p><strong>Address:</strong><br>Negombo Road,<br>Godigamuwa, Badalgama</p>
            <p><strong>Phone:</strong> 077 9221617 / 031-4478147</p>
            <p><strong>Email:</strong> <a href="mailto:sciencemoreinfo@gmail.com">sciencemoreinfo@gmail.com</a></p>
            <p><strong>Follow Us:</strong></p>
            <div class="d-flex gap-3 fs-4">
                <a href="#" class="text-success"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-success"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-success"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="col-md-6">
            <h2 class="text-success mb-4">Send a Message</h2>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Your Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Your Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Send</button>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<?php
$content = ob_get_clean();
include '../layouts.php';
?>
