<h2><?php echo htmlspecialchars($pageTitle); ?></h2>

<?php if (!empty($loginError)): // Display login errors if they exist ?>
    <p style="color: red;"><?php echo htmlspecialchars($loginError); ?></p>
<?php endif; ?>

<form action="index.php?page=register" method="POST">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>