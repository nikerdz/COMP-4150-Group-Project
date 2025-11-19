<?php
require_once('../src/config/constants.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ClubHub | Register</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <section class="auth-section">
        <div class="auth-card">
            <h1>Register</h1>
            <p class="auth-subtitle">
                Join ClubHub to discover clubs, explore events, and manage your campus life all in one place.
            </p>
            <?php if (isset($_SESSION['register_error'])): ?>
                <p class="contact-error" style="text-align:center; color:#d9534f; font-weight:bold; margin-bottom:10px;">
                    <?php 
                        echo htmlspecialchars($_SESSION['register_error']); 
                        unset($_SESSION['register_error']); // clear it so it doesn't persist
                    ?>
                </p>
            <?php endif; ?>

            <form action="<?php echo PHP_URL; ?>auth_handle_register.php" method="POST" class="auth-form">
                
                <div class="auth-row">
                    <div class="auth-field">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="auth-field">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="email">UWindsor Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="auth-field">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="faculty">Faculty</label>
                        <select id="faculty" name="faculty" required>
                            <option value="" disabled selected>Select your faculty</option>
                            <option value="Arts, Humanities & Social Sciences">Arts, Humanities & Social Sciences</option>
                            <option value="Odette School of Business">Business (Odette)</option>
                            <option value="Education">Education</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Graduate Studies">Graduate Studies</option>
                            <option value="Human Kinetics">Human Kinetics</option>
                            <option value="Law">Law</option>
                            <option value="Nursing">Nursing</option>
                            <option value="Science">Science</option>
                        </select>
                    </div>

                        <div class="auth-field">
                            <label for="level_of_study">Level of Study</label>
                            <select id="level_of_study" name="level_of_study" required>
                                <option value="" disabled selected>Select your study level</option>
                                <option value="undergraduate">Undergraduate</option>
                                <option value="graduate">Graduate</option>
                            </select>
                        </div>

                </div>

                <div class="auth-row">

                    <div class="auth-field">
                        <label for="year_of_study">Year of Study</label>
                        <input type="number" id="year_of_study" name="year_of_study" min="1" max="999999">
                    </div>

                    <div class="auth-field">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>Select your gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>
                </div>

                    <button type="submit" class="auth-btn">Create Account</button>

                    <p class="auth-footer-text">
                        Already have an account?
                        <a href="<?php echo PUBLIC_URL; ?>login.php">Log in</a>
                    </p>
            </form>
        </div>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
