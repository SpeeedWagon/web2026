<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
             <div class="card-body p-4">
                <h2 class="card-title text-center text-primary mb-4"><?php echo htmlspecialchars($pageTitle); ?></h2>

                <?php if (!empty($loginError)): // Display login errors if they exist ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($loginError); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                         <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                         <label class="form-check-label" for="remember_me">Remember me</label>
                    </div>
                    <div class="d-grid"> <!-- Makes button full width -->
                         <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>
                
                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a  href="index.php?page=register">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>