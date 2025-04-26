<?php
session_start();


// Database connection details from docker-compose.yml environment variables
$host = 'db'; // IMPORTANT: Use the service name defined in docker-compose.yml
$dbname = getenv('MYSQL_DATABASE'); // Get DB name from environment
$user = getenv('MYSQL_USER');       // Get user from environment
$pass = getenv('MYSQL_PASSWORD');   // Get password from environment
$port = 3306; // Default MySQL port

// Use consistent variable names matching docker-compose.yml
// Fallback values if environment variables aren't directly available (less ideal)
$dbname = $dbname ?: 'my_app_db';
$user = $user ?: 'my_app_user';
$pass = $pass ?: 'your_strong_user_password';

// echo "<p>Attempting to connect to database '{$dbname}' on host '{$host}' with user '{$user}'...</p>";
try {
    // Check if $pdo is already defined (e.g., included elsewhere)
    if (!isset($pdo)) {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);
        // echo "<p>Successfully connected to the database!</p>"; // Optional success message
    }
} catch (\PDOException $e) {
    // Display a user-friendly error and log the detailed one
    echo "<p style='color: red; border: 1px solid red; padding: 10px;'>Database Connection Error: Could not connect. Please check configuration or try again later.</p>";
    // Log the actual error for debugging (don't show details like password in production browser output)
    error_log("Database Connection Error: " . $e->getMessage());
    // Optional: exit script if DB connection is critical for the whole page
    exit; // Or handle gracefully depending on your application logic
}

require_once 'functions/auth_token_functions.php';
// --- Routing ---


$page = $_GET['page'] ?? 'home'; // Default page is 'home'
$allowedPages = ['home', 'about', 'login', 'logout', 'register', 'dashboard']; // Whitelist allowed pages

// Variables for the views
$pageTitle = 'My Website'; // Default Title
$contentFile = '';         // Path to the page content file
$loginError = '';          // To display login errors on the login page

// --- Handle Specific Actions (Logout, Login POST) ---

if (!isset($_SESSION['user_id'])) {
    loginWithRememberMeCookie($pdo);

}
if ($page === 'logout') {
    // Clear session data
    session_unset();    // Unset $_SESSION variable for the run-time
    session_destroy();  // Destroy session data in storage
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        clearUserTokens($pdo, $userId);
    }
    clearRememberMeCookie();
    $_SESSION = [];

    // Optional: Clear the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    // session_destroy();

    header('Location: index.php?page=login'); // Redirect to login page
    exit; // Stop script execution

} elseif ($page == 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO users ( user_name, password_hash, profile_image_path) VALUES (:username, :password , :profile_path )";
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't trim password input
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $uploadDirectory = 'uploads/avatars/';
    $temporaryPath = $_FILES['fileToUpload']['tmp_name'];

    $originalFileName = basename($_FILES['fileToUpload']['name']);
    echo $originalFileName;
    $destinationPath = $uploadDirectory . $originalFileName;
    move_uploaded_file($temporaryPath, $destinationPath);

    if (empty($username) || empty($password)) {
        $loginError = 'Username and Password are required.';
    } else {
        // Prepare statement to prevent SQL injection
        // Replace 'users', 'username', 'id', 'password_hash' with your actual table/column names
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':profile_path' => "uploads/avatars/$originalFileName",
                ':password' => $hashed_pass

            ]);
        } catch (\PDOException $e) {

            $loginError = 'An error occurred during login. Please try again.';
        }
        $page = 'login';
    }
} elseif ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Process Login Attempt ---
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't trim password input
    $rememberMe = isset($_POST['remember_me']); // Check if the checkbox was checked
    // $_SESSION[''];
    $_SESSION['robot'] = true;
    if (empty($username) || empty($password)) {
        $loginError = 'Username and Password are required.';
    } else {
        // Prepare statement to prevent SQL injection
        // Replace 'users', 'username', 'id', 'password_hash' with your actual table/column names
        $stmt = $pdo->prepare("SELECT id, user_name ,  password_hash FROM users WHERE user_name = ?");
        try {
            $stmt->execute([$username]);
            $user = $stmt->fetch(); // Fetch the user record
            // Verify password using password_verify()
            if ($user && password_verify($password, $user['password_hash'])) {
                // Password is correct! Login successful.
                // session_regenerate_id(true); // Regenerate session ID for security
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['user_name'];
                if ($rememberMe) {
                    // Create and store a new token, also sets the cookie
                    createRememberMeToken($pdo, $user['id']);
                } else {
                    // If not checked, ensure any old tokens for this user are removed
                    // and any existing remember me cookie is cleared.
                    clearUserTokens($pdo, $user['id']);
                    clearRememberMeCookie();
                }
                header('Location: index.php?page=home'); // Redirect to home or dashboard
                exit;
            } else {
                // Invalid username or password
                $loginError = 'Invalid username or password.';
            }
        } catch (\PDOException $e) {
            error_log('Login DB Error: ' . $e->getMessage());
            $loginError = 'An error occurred during login. Please try again.';
        }
    }

    // If login failed, we continue below to re-display the login page with the error.
    // Ensure $page is still 'login' so the correct content is loaded.
    $page = 'login'; // Explicitly keep page as login
}

// --- Determine Page Content ---
switch ($page) {
    case 'about':
        $pageTitle = 'About Us';
        $contentFile = 'pages/about.php';
        break;
    case 'register':
        $pageTitle = 'Regisiter';
        $contentFile = 'pages/register.php';
        break;
    case 'dashboard':
        $pageTitle = 'My Private List';
        $contentFile = 'pages/dashboard.php'; // Point to the new file
        break;
    case 'login':
        // If user is already logged in, redirect them away from login page
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=home');
            exit;
        }
        $pageTitle = 'Login';
        $contentFile = 'pages/login.php';
        // $loginError might have been set above during POST handling
        break;

    case 'home':
    default: // Default to home page
        // If requested page wasn't in allowed list and isn't 'logout' (handled above)
        if (!in_array($page, $allowedPages) && $page !== 'home') {
            http_response_code(404); // Set HTTP status code to 404
            $pageTitle = 'Page Not Found';
            $contentFile = 'pages/404.php'; // Assume you have a 404 content page
            // Log this attempt? Maybe someone is probing URLs.
            error_log("404 Not Found: Tried to access non-existent page '{$page}'");
        } else {
            // Regular home page
            $pageTitle = 'Welcome Home';
            $contentFile = 'pages/home.php';
        }
        break;
}

// --- Render the Page ---
// Make variables available to the included files (header needs $pageTitle, login page needs $loginError)
require_once 'templates/header.php';

if (!empty($contentFile) && file_exists($contentFile)) {
    include $contentFile; // Include the main content for the specific page
} elseif ($page !== '404') { // Avoid error message if 404.php itself is missing
    echo "<p>Error: The requested content could not be found.</p>";
    // You could include a default 404 message here if 404.php doesn't exist
}


require_once 'templates/footer.php';
?>

<!DOCTYPE html>
<html>

<head>
    <link href="utils/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>PHP Docker Test</title>
</head>

<body>