<h2><?php echo htmlspecialchars($pageTitle); ?></h2>

<?php if (!empty($loginError)): // Display login errors if they exist ?>
    <p style="color: red;"><?php echo htmlspecialchars($loginError); ?></p>
<?php endif; ?>

<form action="index.php?page=login" method="POST">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
    <label for="remember_me">Remember me</label>
    <input type="checkbox" id="remember_me" name="remember_me"/>
        <button type="submit">Login</button>
    </div>
    <div class="form-group captcha-container">
        <label for="captcha_input">Please enter the code below:</label><br>
        <img src="captcha_image.php?_=<?php echo time(); ?>" alt="CAPTCHA Image" id="loginCaptchaImage" class="captcha-image">
        <button type="button" onclick="refreshCaptcha('loginCaptchaImage')" class="refresh-captcha-btn">Refresh</button><br>
        <input type="text" id="captcha_input" name="captcha_input" required autocomplete="off" class="captcha-input form-control">
    </div>

    <!-- Display login error if any -->
    <?php if (!empty($loginError)): // Assumes index.php passes $loginError ?>
        <p class="error-message"><?php echo htmlspecialchars($loginError); ?></p>
    <?php endif; ?>

    <div>
        <button type="submit">Login</button>
    </div>
<!-- Add JavaScript function (can be placed before </body> or in header) -->
<script>
function refreshCaptcha(imageId) {
    var img = document.getElementById(imageId);
    if (img) {
        // Append a changing timestamp to prevent browser caching
        img.src = 'captcha_image.php?_=' + new Date().getTime();
    }
}
</script>
</form>

