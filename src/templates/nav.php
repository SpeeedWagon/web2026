

<!-- Bootstrap Navbar - Non-Responsive -->
<!-- Classes used:
     navbar: Basic navbar styling
     navbar-dark: Use for dark background colors (sets text color to light)
     bg-primary: Sets the background to Bootstrap's primary blue color
     NOTE: We OMIT navbar-expand-* to PREVENT collapsing -->
<nav class="navbar navbar-dark bg-primary px-3"> <!-- px-3 adds padding on left/right -->
    <!-- Optional Brand/Logo -->
    <a class="navbar-brand" href="index.php?page=home">YourSite</a>

    <!-- Navigation Links - Use flex-row to force inline layout -->
    <ul class="navbar-nav flex-row ms-auto align-items-center"> <!-- ms-auto pushes nav items to the right -->

       
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php 
            $name_id = $_SESSION['user_id'] ??'';
            $sql = "SELECT profile_image_path FROM users WHERE id=$name_id"; 
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            $altText = "uploads/avatars/alice.jpg";
            ?>

            <li class="nav-item">
                 <span class="navbar-text-custom">Welcome, <?php echo $_SESSION['username'] ; ?>!</span> <!-- Non-link text -->
            </li>
            <li>
            <img class="avatar" src="<?= htmlspecialchars($info["profile_image_path"]) ?>" alt="<?= htmlspecialchars($altText) ?>" />
            </li>
            <li class="nav-item">
            <a class="nav-link" href="index.php?page=home">Home</a>
             </li>
            <li class="nav-item">
            <a class="nav-link" href="index.php?page=about">About</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?page=dashboard">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=logout">Logout</a>
            </li>

        <?php else: // Display if user is logged out ?>
            <a class="nav-link" href="index.php?page=home">Home</a>
             </li>
            <li class="nav-item">
            <a class="nav-link" href="index.php?page=about">About</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?page=login">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=register">Register</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
