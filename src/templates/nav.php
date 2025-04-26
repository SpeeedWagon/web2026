

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

<style>
    .avatar {
            width: 40px;           /* Set desired small width */
            height: 40px;          /* Set desired small height (same as width for circle) */
            border-radius: 50%;    /* This makes the image circular */
            object-fit: cover;     /* Scales the image nicely within the circle, cropping if needed */
            vertical-align: middle;/* Optional: Aligns nicely if placed next to text */
            border: 1px solid #ccc; /* Optional: Adds a subtle border */
            background-color: #f0f0f0; /* Optional: Background for broken images */
        }

        /* You can define other sizes too */
        .avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            vertical-align: middle;
        }
    .navbar-avatar {
        width: 30px; /* Adjust size as needed */
        height: 30px;
        border-radius: 50%; /* Make it circular */
        object-fit: cover; /* Scale image nicely */
        vertical-align: middle;
        border: 1px solid rgba(255, 255, 255, 0.6); /* Optional subtle border for dark backgrounds */
    }
    /* Ensure items align vertically in the navbar */
    .navbar .nav-item {
        display: flex;
        align-items: center;
    }
     /* Add some spacing between nav items */
    .navbar-nav > .nav-item {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }
    .navbar-nav > .nav-item:first-child {
        margin-left: 0;
    }
     .navbar-nav > .nav-item:last-child {
        margin-right: 0;
    }
    /* Style for the non-link welcome text */
    .navbar-text-custom {
        color: rgba(255, 255, 255, 0.85); /* Lighter text color for dark navbar */
        padding-top: 0.5rem; /* Align with nav-link padding */
        padding-bottom: 0.5rem; /* Align with nav-link padding */
    }
</style>