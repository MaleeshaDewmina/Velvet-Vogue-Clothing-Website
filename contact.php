<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Velvet Vogue</title>
    <link rel="stylesheet" href="css/contact.css?v=<?php echo time(); ?>">
</head>
<body>

<div id="preloader">
    <img src="images/logo.png" alt="Velvet Vogue Logo" class="preloader-logo">
    <div class="dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let preloader = document.getElementById("preloader");

        // Ensure preloader stays visible for at least 2 seconds
        let minimumTime = 2000; // 2 seconds
        let startTime = Date.now();

        window.addEventListener("load", function () {
            let elapsedTime = Date.now() - startTime;
            let delayTime = Math.max(0, minimumTime - elapsedTime);

            setTimeout(() => {
                preloader.classList.add("hidden"); // Apply fade-out effect
                setTimeout(() => {
                    preloader.style.display = "none"; // Remove preloader after fading out
                }, 500);
            }, delayTime);
        });
    });
</script>

<?php include 'includes/navbar.php'; ?>

<?php if (isset($_GET['success'])) { ?>
    <div class="success-message" onclick="this.style.display='none'">
        ✅ <?php echo htmlspecialchars($_GET['success']); ?> Click to dismiss.
    </div>
<?php } ?>

<div class="contact-container">
    <h1>Contact Us</h1>
    <p>Have questions? Get in touch with us!</p>

    <div class="contact-info">
        <div class="contact-box">
            <i class="fas fa-phone"></i>
            <h2>Phone</h2>
            <p>📞 +94 71 234 5678</p>
        </div>
        <div class="contact-box">
            <i class="fas fa-envelope"></i>
            <h2>Email</h2>
            <p>📧 support@velvetvogue.com</p>
        </div>
        <div class="contact-box">
            <i class="fas fa-map-marker-alt"></i>
            <h2>Address</h2>
            <p>📍 123 Fashion Street, Colombo, Sri Lanka</p>
        </div>
        <div class="contact-box">
            <i class="fas fa-clock"></i>
            <h2>Working Hours</h2>
            <p>🕒 Monday - Saturday: 9 AM - 8 PM</p>
        </div>
    </div>

    <h2>Send Us a Message or Feedback</h2>
    <form action="process_contact.php" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="message" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

<!-- FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
