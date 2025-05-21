<div class="row justify-content-center my-5"> <!-- Added my-5 for overall vertical margin -->
    <div class="col-md-6 col-lg-5">
         <div class="card shadow-sm">
             <div class="card-body p-4">
                <h2 class="card-title text-center text-primary mb-4"><?php echo htmlspecialchars($pageTitle); ?></h2>

                <?php
                // Note: Using $loginError variable here based on your original code.
                // Consider using a dedicated $registerError variable if possible.
                if (!empty($loginError)): // Assuming $loginError is a placeholder for $registerError
                ?>
                    <!-- Added mb-3 for margin-bottom if error exists -->
                    <div class="alert alert-danger mb-3" role="alert">
                        <?php echo htmlspecialchars($loginError); // Or $registerError ?>
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
                        <label for="avatar_user_nou" class="form-label">Profile Picture (Optional):</label>
                        <input class="form-control" type="file" name="avatar_user_nou" id="avatar_user_nou">
                        <div class="form-text">Max 5MB. Allowed types: JPG, PNG, GIF.</div>
                    </div>
                    <!-- Added mb-4 to create space before the <hr> if the button is the last element before it -->
                    <!-- However, the <hr class="my-4"> already provides substantial spacing, so this might be optional -->
                    <div class="d-grid mb-4"> <!-- You can adjust mb-4 or remove if <hr class="my-4"> is sufficient -->
                        <button type="submit" class="btn btn-primary btn-lg">Register</button>
                    </div>
                </form>
                 <hr class="my-4"> <!-- This already provides good vertical spacing (margin-top and margin-bottom of 4) -->
                 <div class="text-center">
                     <!-- Added mb-0 to prevent extra space at the very bottom due to card's p-4 -->
                     <p class="text-muted mb-0">Already have an account? <a href="index.php?page=login">Login here</a></p>
                 </div>
            </div>
        </div>
    </div>
</div>