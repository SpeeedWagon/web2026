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


$page = $_GET['page'] ?? 'home';
$allowedPages = ['home', 'about', 'login', 'logout', 'register', 'dashboard','admin'];


$pageTitle = 'Numele siteul meu';
$contentFile = '';         
$loginError = '';          

if (!isset($_SESSION['user_id'])) {
    loginWithRememberMeCookie($pdo);

}
if ($page === 'logout') {
    session_unset();   
    session_destroy();  
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        clearUserTokens($pdo, $userId);
    }
    clearRememberMeCookie();
    $_SESSION = [];

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

    header('Location: index.php?page=login'); 
    exit; 

}elseif($page == 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST'  && isset($pdo))
{
    // Preserve search term for redirect
    $redirectSearchTerm = isset($_POST['search_term_hidden']) ? trim($_POST['search_term_hidden']) : (isset($_GET['search_term']) ? trim($_GET['search_term']) : '');
    $redirectQuery = !empty($redirectSearchTerm) ? '&search_term=' . urlencode($redirectSearchTerm) : '';


    if (isset($_POST['action']) && $_POST['action'] === 'delete_comment_admin') {
        $commentIdToDelete = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!$commentIdToDelete || $commentIdToDelete <= 0) {
            header('Location: index.php?page=admin&status=admin_delete_invalid_id' . $redirectQuery);
            exit;
        }

        try {
            $sql = "DELETE FROM comments WHERE id = :comment_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':comment_id' => $commentIdToDelete]);

            if ($stmt->rowCount() > 0) {
                header('Location: index.php?page=admin&status=admin_comment_deleted_success' . $redirectQuery);
                exit;
            } else {
                header('Location: index.php?page=admin&status=admin_comment_not_found_or_delete_failed' . $redirectQuery);
                exit;
            }
        } catch (PDOException $e) {
            error_log("Admin: Error deleting comment ID {$commentIdToDelete}: " . $e->getMessage());
            header('Location: index.php?page=admin&status=admin_delete_error_db' . $redirectQuery);
            exit;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_comment_admin') { // <<-- NEW BLOCK
        $commentIdToUpdate = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
        $newContent = trim($_POST['comment_content'] ?? '');

        if (!$commentIdToUpdate || $commentIdToUpdate <= 0) {
            header('Location: index.php?page=admin&status=admin_update_invalid_id' . $redirectQuery);
            exit;
        }
        if (empty($newContent)) {
            header('Location: index.php?page=admin&status=admin_update_empty_content&comment_id_error=' . $commentIdToUpdate . $redirectQuery);
            exit;
        }

        try {
            // Check if comment exists before attempting update for better feedback
            $checkSql = "SELECT id FROM comments WHERE id = :comment_id";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([':comment_id' => $commentIdToUpdate]);
            if (!$checkStmt->fetch()) {
                header('Location: index.php?page=admin&status=admin_comment_not_found_for_update' . $redirectQuery);
                exit;
            }

            $sql = "UPDATE comments SET content = :content, updated_at = NOW() WHERE id = :comment_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':content' => $newContent,
                ':comment_id' => $commentIdToUpdate
            ]);

            if ($stmt->rowCount() > 0) {
                header('Location: index.php?page=admin&status=admin_comment_updated_success' . $redirectQuery);
                exit;
            } else {
                // rowCount might be 0 if the content was identical to the existing one.
                // We already checked if the comment exists.
                header('Location: index.php?page=admin&status=admin_comment_update_no_change' . $redirectQuery);
                exit;
            }
        } catch (PDOException $e) {
            error_log("Admin: Error updating comment ID {$commentIdToUpdate}: " . $e->getMessage());
            header('Location: index.php?page=admin&status=admin_update_error_db' . $redirectQuery);
            exit;
        }
    } else {
        // If other admin POST actions are added, handle them here.
        // For now, if no specific action matched, just redirect back to admin page.
        header('Location: index.php?page=admin' . $redirectQuery);
        exit;
    }
    
    // This line might be unreachable if all actions lead to an exit, but good for fallback.
    // header('Location: index.php?page=admin' . $redirectQuery); // Already handled by the final else
    // exit;
}
 elseif ($page === 'admin' && (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1)) {
    // If trying to access admin page but not logged in as an admin, redirect to login
    $_SESSION['login_error'] = 'You must be an admin to access this page.';
    header('Location: index.php?page=home');
    exit;
}
elseif($page == 'dashboard' && $_SERVER['REQUEST_METHOD'] === 'POST'  && isset($pdo)){
    if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Logativa va rog ca sa va vizualizati date.';
    
    header('Location: index.php?page=login');
    exit;
}

$addCommentError = '';
$deleteCommentError = '';
$statusMessage = '';
$fetchDbError = null; 
$userComments = [];
$currentUserId = $_SESSION['user_id']; 
    if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
        $content = trim($_POST['comment_content'] ?? '');

        if (empty($content)) {
            
            header('Location: index.php?page=dashboard&status=comment_add_empty'); 
            exit;
        } else {
            try {
                $sql = "INSERT INTO comments (user_id, content) VALUES (:user_id, :content)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':user_id' => $currentUserId, ':content' => $content]);
                header('Location: index.php?page=dashboard&status=comment_added');
                exit;
            } catch (PDOException $e) {
                error_log("Error adding comment for user {$currentUserId}: " . $e->getMessage());
                header('Location: index.php?page=dashboard&status=comment_add_error');
                exit;
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
        $commentIdToDelete = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!$commentIdToDelete || $commentIdToDelete <= 0) {
            header('Location: index.php?page=dashboard&status=comment_invalid_id');
            exit;
        } else {
            try {
                $authSql = "SELECT user_id FROM comments WHERE id = :comment_id";
                $authStmt = $pdo->prepare($authSql);
                $authStmt->execute([':comment_id' => $commentIdToDelete]);
                $commentOwner = $authStmt->fetchColumn();

                if ($commentOwner === false) {
                    header('Location: index.php?page=dashboard&status=comment_not_found');
                    exit;
                } elseif ($commentOwner != $currentUserId) {
                    error_log("Auth failed: User {$currentUserId} attempting to delete comment {$commentIdToDelete} owned by {$commentOwner}");
                    header('Location: index.php?page=dashboard&status=comment_auth_error');
                    exit;
                } else {
                    // Proceed with deletion
                    $deleteSql = "DELETE FROM comments WHERE id = :comment_id AND user_id = :user_id";
                    $deleteStmt = $pdo->prepare($deleteSql);
                    $deleteStmt->execute([':comment_id' => $commentIdToDelete, ':user_id' => $currentUserId]);

                    if ($deleteStmt->rowCount() > 0) {
                        header('Location: index.php?page=dashboard&status=comment_deleted');
                        exit;
                    } else {
                        
                        header('Location: index.php?page=dashboard&status=comment_delete_failed');
                        exit;
                    }
                }
            } catch (PDOException $e) {
                error_log("Error deleting comment {$commentIdToDelete} for user {$currentUserId}: " . $e->getMessage());
                header('Location: index.php?page=dashboard&status=comment_delete_error'); 
                exit;
            }
        }
    }
}

elseif ($page == 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_nou = new Inregistrare(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
    $originalFileName = basename($_FILES['avatar_user_nou']['name']);
    $temporaryPath = $_FILES['avatar_user_nou']['tmp_name'];
    $destinationPath = $user_nou->getUploadDirectory() . $originalFileName;
    move_uploaded_file($temporaryPath, $destinationPath);
    if (empty($user_nou->getUserName()) || empty($user_nou->getPassword())) {
        $loginError = 'Nu a fost dat user sau parola';
        echo "<script type='text/javascript'>alert('$loginError');</script>";
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
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']); 
    $_SESSION['robot'] = true;
    if (empty($username) || empty($password)) {
        $loginError = 'Nu a fost dat user sau parola';
        echo "<script type='text/javascript'>alert('$loginError');</script>";
    } else {
        $stmt = $pdo->prepare("SELECT id, user_name ,  password_hash FROM users WHERE user_name = ?");
        try {
            $stmt->execute([$username]);
            $user = $stmt->fetch(); 
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['user_name'];
                if ($rememberMe) {

                    createRememberMeToken($pdo, $user['id']);
                } else {
                    
                    clearUserTokens($pdo, $user['id']);
                    clearRememberMeCookie();
                }
                header('Location: index.php?page=home'); 
                exit;
            } else {
                
                $loginError = 'Invalid username or password.';
            }
        } catch (\PDOException $e) {
            error_log('Eroare logare baza de date ' . $e->getMessage());
            $loginError = 'A aparut o eroare la logare';
        }
    }
    $page = 'login';
}

switch ($page) {
    case 'about':
        $pageTitle = 'Despre noi';
        $contentFile = 'pages/about.php';
        break;
    case 'register':
        $pageTitle = 'Inregistrare';
        $contentFile = 'pages/register.php';
        break;
    case 'dashboard':
        $pageTitle = 'Administrare date';
        $contentFile = 'pages/dashboard.php';
        break;
    case 'admin':
        $pageTitle = 'admin';
        $contentFile = 'pages/admin.php';
        break;
    case 'login':
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=home');
            exit;
        }
        $pageTitle = 'Logare';
        $contentFile = 'pages/login.php';
        break;

    case 'home':
    default:
        if (!in_array($page, $allowedPages) && $page !== 'home') {
            http_response_code(404); 
            $pageTitle = 'Page Not Found';
            $contentFile = 'pages/404.php';
            error_log("404 Not Found: Tried to access non-existent page '{$page}'");
        } else {
            $pageTitle = 'Welcome Home';
            $contentFile = 'pages/home.php';
        }
        break;
}

require_once 'templates/header.php';

if (!empty($contentFile) && file_exists($contentFile)) {
    include $contentFile; 
} elseif ($page !== '404') { 
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