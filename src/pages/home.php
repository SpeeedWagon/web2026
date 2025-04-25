<h2><?php echo htmlspecialchars($pageTitle); ?></h2>
<p>This is the main landing page of the website.</p>

<?php if(isset($_SESSION['user_id'])): ?>
    <p>You are currently logged in.</p>
<?php else: ?>
    <p>You can <a href="index.php?page=login">log in here</a>.</p>
<?php endif; ?>