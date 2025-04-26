<?php
// Ensure session is started (redundant if done in main index.php, but safe)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Authentication Check --- (Should ideally be done *before* including the file in index.php)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Please log in to manage your comments.';
    // Consider redirecting from the main index.php logic instead of here
    // header('Location: index.php?page=login');
    echo '<div class="alert alert-danger">Access denied. Please log in.</div>'; // Fallback message
    exit;
}

// Assume $pdo is available

// --- Initialize variables ---
$addCommentError = '';
$deleteCommentError = '';
$statusMessage = '';
$fetchDbError = null; // For fetch errors
$userComments = [];
$currentUserId = $_SESSION['user_id'];

// --- Status Messages from Redirects ---
if (isset($_GET['status'])) {
    // Simplified status handling
    switch ($_GET['status']) {
        case 'added': $statusMessage = "Comment added successfully!"; break;
        case 'deleted': $statusMessage = "Comment deleted successfully!"; break;
        case 'delete_error': $deleteCommentError = "Could not delete comment. Please try again."; break;
        case 'auth_error': $deleteCommentError = "You are not authorized to delete that comment."; break;
        case 'add_error': $addCommentError = "Could not add comment. Please try again."; break;
        case 'not_found': $deleteCommentError = "Comment not found."; break;
        case 'delete_failed': $deleteCommentError = "Could not delete comment (it might already be gone)."; break;

    }
}


// --- Process POST Requests (Add/Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Handle ADD Comment ---
    if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
        $content = trim($_POST['comment_content'] ?? '');
        if (empty($content)) {
            $addCommentError = "Comment content cannot be empty.";
        } else {
            try {
                $sql = "INSERT INTO comments (user_id, content) VALUES (:user_id, :content)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':user_id' => $currentUserId, ':content' => $content]);
                // Use header redirect for PRG pattern - JS redirect can be less reliable
                // header('Location: index.php?page=dashboard&status=added');
                echo "<script>
                         setTimeout(function() {
                window.location.href = 'index.php?page=dashboard'; // Reload specific URL
                }, 1); // Reload after 1.5 seconds (1500 milliseconds)
                 </script>";
                exit;
            } catch (PDOException $e) {
                $addCommentError = "Database error adding comment."; // Show error on current page
                error_log("Error adding comment for user {$currentUserId}: " . $e->getMessage());
                // header('Location: index.php?page=dashboard&status=add_error'); exit; // Alt: Redirect with error
            }
        }
    }

    // --- Handle DELETE Comment ---
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
        $commentIdToDelete = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!$commentIdToDelete || $commentIdToDelete <= 0) {
            $deleteCommentError = "Invalid comment ID provided.";
             // header('Location: index.php?page=dashboard&status=delete_error'); exit; // Alt: Redirect
        } else {
            try {
                $authSql = "SELECT user_id FROM comments WHERE id = :comment_id";
                $authStmt = $pdo->prepare($authSql);
                $authStmt->execute([':comment_id' => $commentIdToDelete]);
                $commentOwner = $authStmt->fetchColumn();

                if ($commentOwner === false) {
                    //  header('Location: index.php?page=dashboard&status=not_found'); exit;
                    echo "<script>
                    setTimeout(function() {
           window.location.href = 'index.php?page=dashboard'; // Reload specific URL
           }, 1); // Reload after 1.5 seconds (1500 milliseconds)
            </script>";
                } elseif ($commentOwner != $currentUserId) {
                     error_log("Auth failed: User {$currentUserId} deleting comment {$commentIdToDelete} owned by {$commentOwner}");
                    //  header('Location: index.php?page=dashboard&status=auth_error'); // Redirect on auth failure
                    echo "<script>
                    setTimeout(function() {
           window.location.href = 'index.php?page=dashboard'; // Reload specific URL
           }, 1); // Reload after 1.5 seconds (1500 milliseconds)
            </script>";
                     exit;
                } else {
                    $deleteSql = "DELETE FROM comments WHERE id = :comment_id AND user_id = :user_id";
                    $deleteStmt = $pdo->prepare($deleteSql);
                    $deleteStmt->execute([':comment_id' => $commentIdToDelete,':user_id' => $currentUserId]);

                    if ($deleteStmt->rowCount() > 0) {
                        //  header('Location: index.php?page=dashboard&status=deleted');
                        echo "<script>
                        setTimeout(function() {
               window.location.href = 'index.php?page=dashboard'; // Reload specific URL
               }, 1); // Reload after 1.5 seconds (1500 milliseconds)
                </script>";
                         exit;
                    } else {
                         header('Location: index.php?page=dashboard&status=delete_failed');
                         exit;
                    }
                }
            } catch (PDOException $e) {
                error_log("Error deleting comment {$commentIdToDelete} for user {$currentUserId}: " . $e->getMessage());
                // header('Location: index.php?page=dashboard&status=delete_error');
                echo "<script>
                setTimeout(function() {
       window.location.href = 'index.php?page=dashboard'; // Reload specific URL
       }, 1); // Reload after 1.5 seconds (1500 milliseconds)
        </script>";
                exit;
            }
        }
    }
} // --- End of POST processing ---


// --- Fetch User's Comments for Display ---
try {
    $sql = "SELECT id, content, created_at FROM comments WHERE user_id = :user_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $currentUserId]);
    $userComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fetchDbError = "Could not retrieve your comments. Please try again later.";
    error_log("DB error fetching comments for user {$currentUserId}: " . $e->getMessage());
}

?>

<!-- Page Content Starts -->
<h1 class="text-primary mb-4">Manage My Comments</h1>

<!-- Display Status/Feedback Messages -->
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


<!-- Add Comment Form Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
      <h2 class="h5 mb-0">Add a New Comment</h2>
    </div>
    <div class="card-body">
        <?php if ($addCommentError): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($addCommentError); ?>
            </div>
        <?php endif; ?>
        <form action="index.php?page=dashboard" method="POST">
            <input type="hidden" name="action" value="add_comment">
            <div class="mb-3">
                <label for="comment_content" class="form-label">Your Comment:</label>
                <textarea class="form-control" id="comment_content" name="comment_content" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-1" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                </svg>
                Add Comment
            </button>
        </form>
    </div>
</div>


<!-- Display Existing Comments Card -->
<div class="card shadow-sm">
     <div class="card-header">
       <h2 class="h5 mb-0">My Existing Comments</h2>
     </div>
    <div class="card-body">
        <?php if ($fetchDbError): ?>
            <div class="alert alert-warning" role="alert">
                <?= htmlspecialchars($fetchDbError); ?>
            </div>
        <?php elseif (!empty($userComments)): ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($userComments as $comment): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start" id="comment-<?php echo htmlspecialchars($comment['id']); ?>">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold"><?php echo nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?></div>
                            <small class="text-muted">
                                Posted: <?php echo date('M d, Y H:i', strtotime($comment['created_at'])); ?>
                             </small>
                        </div>
                        <!-- Delete Button Form -->
                        <form action="index.php?page=dashboard" method="POST" class="ms-2">
                            <input type="hidden" name="action" value="delete_comment">
                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete comment" onclick="return confirm('Are you sure you want to delete this comment?');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg>
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">You haven't written any comments yet.</p>
        <?php endif; ?>
    </div> <!-- /card-body -->
</div> <!-- /card -->
<!-- End Page Content -->