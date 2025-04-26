<nav>
    <ul>
        <li><a href="index.php?page=home">Home</a></li>
        <li><a href="index.php?page=about">About</a></li>
        <?php if (isset($_SESSION['user_id'])): // Check if user is logged in ?>
            <li><a href="index.php?page=dashboard">Dashboard</a></li>
            <li>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</li>
            <li><a href="index.php?page=logout">Logout</a></li>
            <?php 
            $name_id = $_SESSION['user_id'] ??'';
    $sql = "SELECT profile_image_path FROM users WHERE id=$name_id"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $altText = "uploads/avatars/alice.jpg"
?>    
<div>
<img class="avatar" src="<?= htmlspecialchars($info["profile_image_path"]) ?>" alt="<?= htmlspecialchars($altText) ?>" />
</div>
        <?php else: ?>
            <li><a href="index.php?page=login">Login</a></li>|
            <li><a href="index.php?page=register">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
<style>
        /* --- CSS for the Avatar --- */
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
    </style>