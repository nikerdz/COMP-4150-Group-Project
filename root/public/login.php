<?php
require_once('../src/config/constants.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ClubHub | Log In</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <section class="auth-section">

        <div class="auth-card">

            <h1>Log In</h1>
            <p class="auth-subtitle">Access your ClubHub account and stay connected with your clubs.</p>
            <?php if (isset($_GET['error'])): ?>
                <p class="contact-error" style="text-align:center; color:#d9534f; font-weight:bold;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </p>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="auth-toast auth-toast-success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            
            <form action="<?php echo PHP_URL; ?>auth_handle_login.php" method="POST" class="auth-form">

                <div class="auth-field">
                    <label>Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div class="auth-field">
                    <label>Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div style="display:flex; align-items:center; gap:8px; margin-top:10px;">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" style="color:#1c348d;">Remember me</label>
                </div>

                <button type="submit" class="auth-btn">Log In</button>

            </form>

            <p class="auth-footer-text">
                Don’t have an account?
                <a href="<?php echo PUBLIC_URL; ?>register.php">Register here</a>
            </p>

        </div>

    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
