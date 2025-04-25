<nav>
    <ul>
        <li><a href="index.php?page=home">Home</a></li>
        <li><a href="index.php?page=about">About</a></li>
        <?php if (isset($_SESSION['user_id'])): // Check if user is logged in ?>
            <li>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</li>
            <li><a href="index.php?page=logout">Logout</a></li>
        <?php else: ?>
            <li><a href="index.php?page=login">Login</a></li>|
            <li><a href="index.php?page=register">Register</a></li>
            <!-- <li><a href="index.php?page=logout">Log out</a></li> -->
        <?php endif; ?>
    </ul>
</nav>