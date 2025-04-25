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
</form>