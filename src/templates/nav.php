<nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand d-flex align-items-center" href="index.php?page=home"> 
            <img src="../media/svgs/brain-circuit.svg" alt="BineSoft Logo" width="40" height="40"> 
            <span class="ms-2 brand-name">BineSoft</span> 
        </a>
        <ul class="navbar-nav flex-row ms-auto align-items-center">

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $name_id = $_SESSION['user_id'] ?? '';
                $profile_image_path = "uploads/avatars/default.png";
                $altText = "User Avatar";

                if ($name_id && isset($pdo)) {
                    try {
                        $sql = "SELECT profile_image_path FROM users WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $name_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $info = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($info && !empty($info["profile_image_path"])) {
                            $profile_image_path = $info["profile_image_path"];
                        }
                    } catch (PDOException $e) {
                        error_log("PDOException in navbar: " . $e->getMessage());
                    }
                }
                ?>

                <li class="nav-item me-2"> 
                    <span class="navbar-text fs-5">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                </li>
                <li class="nav-item me-3">
                    <img class="avatar" src="<?= htmlspecialchars($profile_image_path) ?>"
                        alt="<?= htmlspecialchars($altText) ?>" />
                </li>
                <li class="nav-item me-2"> 
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'home' ? 'active' : ''); ?>" href="index.php?page=home">Home</a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'about' ? 'active' : ''); ?>" href="index.php?page=about">About</a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'dashboard' ? 'active' : ''); ?>" href="index.php?page=dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5" href="index.php?page=logout">Logout</a>
                </li>

            <?php else:  ?>
                <li class="nav-item me-2">
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'home' ? 'active' : ''); ?>" href="index.php?page=home">Home</a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'about' ? 'active' : ''); ?>" href="index.php?page=about">About</a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'login' ? 'active' : ''); ?>" href="index.php?page=login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5 <?php echo ($currentPage === 'register' ? 'active' : ''); ?>" href="index.php?page=register">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>