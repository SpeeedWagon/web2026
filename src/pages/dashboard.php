<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Authentication Check - Redirect immediately if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Logativa va rog ca sa va vizualizati date.';
    // If 'login.php' is the page, then use that, otherwise your main index with login page
    header('Location: index.php?page=login'); // Or wherever your login page is
    exit;
}

// Initialize variables
$addCommentError = '';
$deleteCommentError = '';
$statusMessage = '';
$fetchDbError = null; // Initialize as null or empty string
$userComments = [];
$currentUserId = $_SESSION['user_id']; // Already checked that it's set

// 2. Process Status Messages from URL (after potential redirects)
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'comment_added':
            $statusMessage = "Informatia adaugata cu succes.";
            break;
        case 'comment_deleted':
            $statusMessage = "Informatia stearsa cu succes.";
            break;
        case 'comment_delete_error':
            $deleteCommentError = "Nu a putut fi sters comentariul.";
            break;
        case 'comment_auth_error':
            $deleteCommentError = "Eroare de autentificare la stergere."; // More specific
            break;
        case 'comment_add_error':
            $addCommentError = "Nu a putut fi adaugat comentariul. Încercați din nou.";
            break;
        case 'comment_not_found':
            $deleteCommentError = "Comentariul nu a fost gasit.";
            break;
        case 'comment_delete_failed':
            $deleteCommentError = "Stergerea comentariului a esuat.";
            break;
        // Add more specific statuses as needed
    }
}

// 3. Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($pdo)) { // Ensure $pdo is available
    if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
        $content = trim($_POST['comment_content'] ?? '');

        if (empty($content)) {
            // It's better to handle empty content validation here rather than relying solely on 'required'
            // and redirect back with an error if needed, or set $addCommentError directly if not redirecting
            header('Location: index.php?page=dashboard&status=comment_add_empty'); // Example status
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
                // Check if comment exists and belongs to the user
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
                        // This case implies the comment existed but wasn't deleted (e.g., race condition or already deleted)
                        // Or, if the user_id check in WHERE clause failed, which shouldn't happen if auth check passed.
                        header('Location: index.php?page=dashboard&status=comment_delete_failed');
                        exit;
                    }
                }
            } catch (PDOException $e) {
                error_log("Error deleting comment {$commentIdToDelete} for user {$currentUserId}: " . $e->getMessage());
                header('Location: index.php?page=dashboard&status=comment_delete_error'); // General DB error on delete
                exit;
            }
        }
    }
}

// 4. Fetch User Comments (after all POST processing and redirects are done)
if (isset($pdo)) { // Ensure $pdo is available
    try {
        $sql = "SELECT id, content, created_at FROM comments WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $currentUserId]);
        $userComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching comments for user {$currentUserId}: " . $e->getMessage());
        $fetchDbError = "Nu s-au putut încărca datele. Vă rugăm încercați mai târziu.";
        // Potentially set a general error message for the user
    }
} else {
    $fetchDbError = "Database connection not available."; // Or handle as appropriate
}

?>

<!-- Start of your HTML content for the dashboard page -->
<!-- Assuming you have a header include here, e.g., <?php // include 'partials/header.php'; ?> -->
<!-- The main container for your dashboard content -->
<div class="container mt-4 mb-4">

    <?php if ($statusMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($statusMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($deleteCommentError): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($deleteCommentError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($addCommentError): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($addCommentError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h2 class="h5 mb-0 py-1">Adăugare Date</h2>
        </div>
        <div class="card-body">
            <form action="index.php?page=dashboard" method="POST">
                <input type="hidden" name="action" value="add_comment">
                <div class="mb-3">
                    <label for="comment_content" class="form-label">Datele dumneavoastră:</label>
                    <textarea class="form-control" id="comment_content" name="comment_content" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-plus-circle-fill me-1 align-text-bottom" viewBox="0 0 16 16">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z" />
                    </svg>
                    Adaugă date
                </button>
            </form>
        </div>
    </div>


    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h2 class="h5 mb-0 py-1">Datele Dumneavoastră Stocate</h2>
        </div>
        <div class="card-body">
            <?php if ($fetchDbError): ?>
                <div class="alert alert-warning" role="alert">
                    <?= htmlspecialchars($fetchDbError); ?>
                </div>
            <?php elseif (!empty($userComments)): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($userComments as $comment): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start py-3"  
                            id="comment-<?php echo htmlspecialchars($comment['id']); ?>">
                            <div class="ms-2 me-auto">
                                <div class="comment-content">
                                    <?php echo nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?></div>
                                <small class="text-muted">
                                    Adăugat: <?php echo date('d M Y, H:i', strtotime($comment['created_at'])); ?>
                                </small>
                            </div>
                            <form action="index.php?page=dashboard" method="POST" class="ms-3">
                                <input type="hidden" name="action" value="delete_comment">
                                <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Șterge comentariul"
                                    onclick="return confirm('Sunteți sigur că doriți să ștergeți aceste date?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-trash3-fill align-text-bottom" viewBox="0 0 16 16">
                                        <path
                                            d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                    </svg>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted fst-italic">Nu ați adăugat date încă.</p>
            <?php endif; ?>
        </div>
    </div>
</div> 
<?php // include 'partials/footer.php'; ?>