<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
         <div class="card shadow-sm">
             <div class="card-body p-4">
                <h2 class="card-title text-center text-primary mb-4"><?php echo htmlspecialchars($pageTitle); ?></h2>

                <?php
                // Note: Using $loginError variable here based on your original code.
                // Consider using a dedicated $registerError variable if possible.
                if (!empty($loginError)):
                ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($loginError); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=register" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                     <div class="mb-3">
                        <label for="fileToUpload" class="form-label">Profile Picture (Optional):</label>
                        <!-- Bootstrap styles file inputs automatically with .form-control -->
                        <input class="form-control" type="file" name="fileToUpload" id="fileToUpload">
                        <div class="form-text">Max 5MB. Allowed types: JPG, PNG, GIF.</div> <!-- Example help text -->
                    </div>
                    <div class="d-grid"> <!-- Makes button full width -->
                        <button type="submit" class="btn btn-primary btn-lg">Register</button>
                    </div>
                </form>
                 <hr class="my-4">
                 <div class="text-center">
                     <p class="text-muted">Already have an account? <a href="index.php?page=login">Login here</a></p>
                 </div>
            </div>
        </div>
    </div>
</div>