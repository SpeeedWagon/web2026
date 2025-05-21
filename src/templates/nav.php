<nav class="navbar navbar-dark bg-primary">
    <!-- Use container-fluid for full width, and px-md-4 for padding on medium screens and up, px-3 for smaller -->
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand" href="index.php?page=home">
            <img src="../media/svgs/brain-circuit.svg" alt="Site Logo" width="50" height="50">
        </a>
        <ul class="navbar-nav flex-row ms-auto align-items-center">

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                // Ensure $pdo is available in this scope
                // It's good practice to check if $pdo is set and is a PDO object
                $name_id = $_SESSION['user_id'] ?? '';
                $profile_image_path = "uploads/avatars/default.png"; // Default avatar
                $altText = "User Avatar";

                if ($name_id && isset($pdo)) { // Check if $pdo is available
                    try {
                        $sql = "SELECT profile_image_path FROM users WHERE id = :id"; // Use prepared statements correctly
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $name_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $info = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($info && !empty($info["profile_image_path"])) {
                            $profile_image_path = $info["profile_image_path"];
                        }
                    } catch (PDOException $e) {
                        // Log error or handle it gracefully
                        // For now, we'll just use the default avatar
                        error_log("PDOException in navbar: " . $e->getMessage());
                    }
                }
                ?>

                <!-- Added me-3 for spacing, fs-5 for font size (on the span) -->
                <li class="nav-item me-3">
                    <span class="navbar-text fs-5">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                </li>
                <!-- Added me-3 for spacing -->
                <li class="nav-item me-3">
                    <img class="avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" src="<?= htmlspecialchars($profile_image_path) ?>"
                        alt="<?= htmlspecialchars($altText) ?>" />
                </li>
                <!-- Added me-3 for spacing, fs-5 for font size -->
                <li class="nav-item me-3">
                    <a class="nav-link fs-5" href="index.php?page=home">Home</a>
                </li>
                <!-- Added me-3 for spacing, fs-5 for font size -->
                <li class="nav-item me-3">
                    <a class="nav-link fs-5" href="index.php?page=about">About</a>
                </li>
                <!-- Added me-3 for spacing, fs-5 for font size -->
                <li class="nav-item me-3">
                    <a class="nav-link fs-5" href="index.php?page=dashboard">Dashboard</a>
                </li>
                <!-- No me-3 on the last item in this group, fs-5 for font size -->
                <li class="nav-item">
                    <a class="nav-link fs-5" href="index.php?page=logout">Logout</a>
                </li>

            <?php else:  ?>
                <!-- Added me-3 for spacing, fs-5 for font size -->
                <li class="nav-item me-3">
                    <a class="nav-link fs-5" href="index.php?page=home">Home</a>
                </li>
                <!-- Added me-3 for spacing, fs-5 for font size -->
                <li class="nav-item me-3">
                    <a class="nav-link fs-5" href="index.php?page=about">About</a>
                </li>
                <!-- Added me-3 for spacing, fs-5 for font size -->
                <li class="nav-item me-3">
                    <a class="nav-link fs-5" href="index.php?page=login">Login</a>
                </li>
                <!-- No me-3 on the last item, fs-5 for font size -->
                <li class="nav-item">
                    <a class="nav-link fs-5" href="index.php?page=register">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>