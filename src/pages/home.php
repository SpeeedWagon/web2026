<h2 class="text-primary mb-3"><?php echo htmlspecialchars($pageTitle); ?></h2>
<p class="lead">This is the main landing page of the website.</p>

<?php if(isset($_SESSION['user_id'])): ?>
    <div class="alert alert-info" role="alert">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill me-2" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
      </svg>
      You are currently logged in. Welcome back!
    </div>
    <p><a href="index.php?page=dashboard" class="btn btn-primary">Go to Dashboard</a></p>

<?php else: ?>
    <div class="alert alert-secondary" role="alert">
         You can <a href="index.php?page=login" class="alert-link">log in here</a> or <a href="index.php?page=register" class="alert-link">create an account</a>.
    </div>
<?php endif; ?>