<?php
session_start();

class SiteData{
    private $host;
    private $user;
    private $dbname;
    private $pass;
    private $port;
    function __construct($host, $dbname,$user, $pass, $port){
        $this->host = $host;
        $this->user = $user;
        $this->dbname = $dbname;
        $this->pass = $pass;
        $this->port = $port;
    }
    function getHost(){
        return $this->host;
    }
    function getUser(){
        return $this->user;
    }
    function getDbname(){
        return $this->dbname;
    }
    function getPass(){
        return $this->pass;
    }
    function getPort(){
        return $this->port;
    }

}



class Inregistrare{
    private $sql = "INSERT INTO users ( user_name, password_hash, profile_image_path) VALUES (:username, :password , :profile_path )";
    private $uploadDirectory= 'uploads/avatars/';
    private $username;
    private $password;
    function __construct($username, $password ){
        $this->username = $username;
        $this->password = $password;
    }
    function getHash(){
        return password_hash($this->password, PASSWORD_DEFAULT);
    }
    function getSql(){
        return $this->sql;
    }
    function getUploadDirectory(){
        return $this->uploadDirectory;
    }
    function getUserName(){
        return $this->username;
    }
    function getPassword(){
        return $this->password;
    }

}

$site_data = new SiteData('db',getenv('MYSQL_DATABASE'),getenv('MYSQL_USER'),getenv('MYSQL_PASSWORD'),3306);

$host = $site_data->getHost();
$dbname = $site_data->getDbname();
$user = $site_data->getUser();
$pass = $site_data->getPass();
$port = $site_data->getPort();


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
    }
} catch (\PDOException $e) {
    error_log("Eroare conectare la baza de date" . $e->getMessage());
    exit; 
}

require_once 'functions/auth_token_functions.php';


$page = $_GET['page'] ?? 'home'; // Default page is 'home'
$allowedPages = ['home', 'about', 'login', 'logout', 'register', 'dashboard']; // Whitelist allowed pages

// Variables for the views
$pageTitle = 'Numele siteul meu'; // Default Title
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
    $user_nou = new Inregistrare(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
    $originalFileName = basename($_FILES['avatar_user_nou']['name']);
    $temporaryPath = $_FILES['avatar_user_nou']['tmp_name'];
    $destinationPath = $user_nou->getUploadDirectory() . $originalFileName;
    move_uploaded_file($temporaryPath, $destinationPath);
    if (empty($user_nou->getUserName()) || empty($user_nou->getPassword())) {
        $loginError = 'Nu a fost dat user sau parola';
    } else {
        try {
            $stmt = $pdo->prepare($user_nou->getSql());
            $stmt->execute([
                ':username' => $user_nou->getUserName(),
                ':profile_path' => "uploads/avatars/$originalFileName",
                ':password' => $user_nou->getHash()

            ]);
        } catch (\PDOException $e) {

            $loginError = 'Eroare la logare';
        }
        $page = 'login';
    }
} elseif ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
            error_log('Eroare logare baza de date ' . $e->getMessage());
            $loginError = 'A aparut o eroare la logare';
        }
    }
    $page = 'login';
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
        $pageTitle = 'Dashboard';
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
    default:
        if (!in_array($page, $allowedPages) && $page !== 'home') {
            http_response_code(404); // Set HTTP status code to 404
            $pageTitle = 'Page Not Found';
            $contentFile = 'pages/404.php'; // Assume you have a 404 content page
            error_log("404 Not Found: Tried to access non-existent page '{$page}'");
        } else {
            $pageTitle = 'Welcome Home';
            $contentFile = 'pages/home.php';
        }
        break;
}

require_once 'templates/header.php';

if (!empty($contentFile) && file_exists($contentFile)) {
    include $contentFile; // Include the main content for the specific page
} elseif ($page !== '404') { // Avoid error message if 404.php itself is missing
    echo "<p>Eroare 404 nu poate fi gasita aceasta pagina pe site</p>";
}


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