<div class="row justify-content-center my-5"> <!-- Added my-5 for overall vertical margin (top and bottom) -->
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title text-center text-primary mb-4"><?php echo htmlspecialchars($pageTitle); ?></h2>

                <?php if (!empty($loginError)): // Display login errors if they exist ?>
                    <!-- Added mb-3 for margin-bottom if error exists -->
                    <div class="alert alert-danger mb-3" role="alert">
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

                    <!-- Added mb-3 to this button for spacing below it -->
                    <!-- Also added w-100 to make it full width like the login button for consistency -->
                    <button id="playButton" type="button" class="btn btn-lg btn-danger shadow-sm w-100 mb-3" data-bs-toggle="modal"
                        data-bs-target="#gameModal">
                        Check if you are a robot
                    </button>
                    
                    <!-- This div.d-grid already makes the button full width -->
                    <div class="d-grid"> 
                        <button id="login" type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                    
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
                        crossorigin="anonymous"></script>


                    <!-- Bootstrap Modal -->
                    <div class="modal fade" id="gameModal" tabindex="-1" aria-labelledby="gameModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="gameModalLabel">Captcha</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p id="gameStatus" class="text-center fw-bold fs-5 mb-3">Your turn (X)</p>
                                    <div id="gameBoard" class="board">
                                        <button class="cell" data-index="0"></button>
                                        <button class="cell" data-index="1"></button>
                                        <button class="cell" data-index="2"></button>
                                        <button class="cell" data-index="3"></button>
                                        <button class="cell" data-index="4"></button>
                                        <button class="cell" data-index="5"></button>
                                        <button class="cell" data-index="6"></button>
                                        <button class="cell" data-index="7"></button>
                                        <button class="cell" data-index="8"></button>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button" id="restartButton" class="btn btn-secondary">Restart
                                        Game</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Added mt-4 for margin-top to separate from the form -->
                <div class="text-center mt-4">
                     <!-- Added mb-0 to the paragraph to avoid excessive space if card-body already has padding-bottom -->
                    <p class="text-muted mb-0">Don't have an account? <a href="index.php?page=register">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../utils/js/tic_tac.js"></script>