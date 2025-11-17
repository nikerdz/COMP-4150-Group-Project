<?php
require_once('../src/config/constants.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - Discover Your Campus Community">
    <meta property="og:description" content="Join ClubHub and explore clubs, events, and connect with fellow students on campus.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="https://khan661.myweb.cs.uwindsor.ca/COMP-4150-Group-Project/root/public/">
    <meta property="og:type" content="website"> <!-- Enhance link previews when shared on Facebook, LinkedIn, and other platforms -->

    <title>ClubHub | Contact Us</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

   

    <div class="contact-container">
        <h1>Contact Us</h1>
        <p>
            We’d love to hear from you! Whether you have questions, feedback, concerns, 
            or ideas to improve ClubHub, feel free to reach out. Your voice helps us make 
            the platform better for everyone.
        </p>
        <?php if (isset($_GET['success'])): ?>
            <p class="contact-success">Your message has been sent!</p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="contact-error">Something went wrong. Please try again.</p>
        <?php endif; ?>

        <form action="<?php echo PHP_URL; ?>send_contact.php" method="POST" class="contact-form">
            
            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Subject</label>
            <input type="text" name="subject" required>

            <label>Message</label>
            <textarea name="message" rows="6" required></textarea>

            <button type="submit">Send Message</button>

        </form>
    </div>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
