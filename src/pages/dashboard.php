<?php
// src/pages/my_comments.php

// Start session if not already started (best practice)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Authentication Check ---
// Must happen before any processing or output
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Please log in to manage your comments.';
    // header('Location: index.php?page=login');
    exit;
}

// We need the $pdo object. Assume index.php makes it available,
// or require your connection file here.
// require_once __DIR__ . '/../db_connection.php'; // If needed

// --- Initialize variables for feedback ---
$addCommentError = '';
$deleteCommentError = '';
$statusMessage = '';

// --- Check for Status Messages from Redirects ---
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'added') {
        $statusMessage = "Comment added successfully!";
    } elseif ($_GET['status'] === 'deleted') {
        $statusMessage = "Comment deleted successfully!";
    } elseif ($_GET['status'] === 'delete_error') {
        $deleteCommentError = "Could not delete comment. Please try again.";
    } elseif ($_GET['status'] === 'auth_error') {
        $deleteCommentError = "You are not authorized to delete that comment.";
    }
     elseif ($_GET['status'] === 'add_error') {
        $addCommentError = "Could not add comment. Please try again.";
    }
}


// --- Process POST Requests (Add/Delete) ---
// This MUST happen before fetching comments for display
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id']; // Get user ID again for processing

    // --- Handle ADD Comment ---
    if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
        $content = trim($_POST['comment_content'] ?? '');

        if (empty($content)) {
            $addCommentError = "Comment content cannot be empty.";
        } else {
            // Validation passed, proceed with insert
            try {
                $sql = "INSERT INTO comments (user_id, content) VALUES (:user_id, :content)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $currentUserId,
                    ':content' => $content
                ]);

                // Redirect using PRG pattern on success
                echo "<script>
                        setTimeout(function() {
             window.location.href = 'index.php?page=dashboard'; // Reload specific URL
         }, 1); // Reload after 1.5 seconds (1500 milliseconds)
       </script>";
                

            } catch (PDOException $e) {
                $addCommentError = "Database error adding comment.";
                error_log("Error adding comment for user {$currentUserId}: " . $e->getMessage());
                // Let the script continue to display the page with the error
                 // Optional: Redirect with error status
                // header('Location: index.php?page=my_comments');
                // exit;
            }
        }
    }

    // --- Handle DELETE Comment ---
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
        $commentIdToDelete = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!$commentIdToDelete || $commentIdToDelete <= 0) {
            $deleteCommentError = "Invalid comment ID provided.";
        } else {
            try {
                // *** AUTHORIZATION CHECK: Verify comment belongs to the user ***
                $authSql = "SELECT user_id FROM comments WHERE id = :comment_id";
                $authStmt = $pdo->prepare($authSql);
                $authStmt->execute([':comment_id' => $commentIdToDelete]);
                $commentOwner = $authStmt->fetchColumn(); // Fetch just the user_id column

                if ($commentOwner === false) {
                    // Comment doesn't exist
                     $deleteCommentError = "Comment not found.";
                     // Optional Redirect: header('Location: index.php?page=my_comments&status=not_found'); exit;
                } elseif ($commentOwner != $currentUserId) {
                    // Authorization failed - comment belongs to someone else!
                    error_log("Authorization failed: User {$currentUserId} tried to delete comment {$commentIdToDelete} owned by {$commentOwner}");
                    // Redirect with generic error (don't reveal specific auth failure details)
                    // header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    // --- Authorization OK - Proceed with Deletion ---
                    $deleteSql = "DELETE FROM comments WHERE id = :comment_id AND user_id = :user_id"; // Double check user_id
                    $deleteStmt = $pdo->prepare($deleteSql);
                    $deleteStmt->execute([
                        ':comment_id' => $commentIdToDelete,
                        ':user_id'    => $currentUserId
                    ]);

                    // Check if deletion was successful (optional but good)
                    if ($deleteStmt->rowCount() > 0) {
                         // Redirect using PRG pattern on success
                         echo "<script>
                         setTimeout(function() {
              window.location.href = 'index.php?page=dashboard'; // Reload specific URL
          }, 1); // Reload after 1.5 seconds (1500 milliseconds)
        </script>";
                        exit;
                    } else {
                        // Row count was 0 - maybe already deleted or race condition?
                        $deleteCommentError = "Could not delete comment (it might already be gone).";
                         // Optional Redirect: header('Location: index.php?page=my_comments&status=delete_failed'); exit;
                    }
                }

            } catch (PDOException $e) {
                $deleteCommentError = "Database error deleting comment.";
                error_log("Error deleting comment {$commentIdToDelete} for user {$currentUserId}: " . $e->getMessage());
                // Redirect with generic error status
                //  header('Location: index.php?page=dashboard&status=delete_error');
                 exit;
            }
        }
    }
} // --- End of POST processing ---


// --- Fetch User's Comments for Display ---
// This runs *after* potential adds/deletes and redirects
$currentUserId = $_SESSION['user_id']; // Re-affirm user ID
$userComments = [];
$fetchDbError = null; // Use a different variable name for fetch errors

try {
    $sql = "SELECT id, content, created_at
            FROM comments
            WHERE user_id = :user_id
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $currentUserId]);
    $userComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fetchDbError = "Could not retrieve your comments. Please try again later.";
    error_log("Database error fetching comments for user {$currentUserId}: " . $e->getMessage());
}

?>

<!-- Page Content Starts (No <html>, <head>, <body>) -->
<h1>Manage My Comments</h1>

<!-- Display Status/Feedback Messages -->
<?php if ($statusMessage): ?>
    <p class="status-message" style="color: green; background-color: #e6ffe6; border: 1px solid green; padding: 10px; border-radius: 4px;">
        <?php echo htmlspecialchars($statusMessage); ?>
    </p>
<?php endif; ?>
<?php if ($deleteCommentError): // Display general delete errors here ?>
    <p class="error-message" style="color: red; background-color: #ffe6e6; border: 1px solid red; padding: 10px; border-radius: 4px;">
        <?php echo htmlspecialchars($deleteCommentError); ?>
    </p>
<?php endif; ?>


<!-- Add Comment Form -->
<div class="add-comment-form">
    <h2>Add a New Comment</h2>
    <?php if ($addCommentError): ?>
        <p class="error-message" style="color: red;">
            <?php echo htmlspecialchars($addCommentError); ?>
        </p>
    <?php endif; ?>
    <form action="index.php?page=dashboard" method="POST">
        <input type="hidden" name="action" value="add_comment">
        <div>
            <label for="comment_content">Your Comment:</label><br>
            <textarea id="comment_content" name="comment_content" rows="4" cols="50" required></textarea>
        </div>
        <div>
            <button type="submit">Add Comment</button>
        </div>
    </form>
</div>

<hr style="margin: 20px 0;">

<!-- Display Existing Comments -->
<div class="user-comments-section">
    <h2>My Existing Comments</h2>

    <?php if ($fetchDbError): // Display error if fetching failed ?>
        <p class="error-message" style="color: red;">
            <?php echo htmlspecialchars($fetchDbError); ?>
        </p>
    <?php elseif (!empty($userComments)): // Display list if comments exist ?>
        <ul class="comment-list" style="list-style: none; padding: 0;">
            <?php foreach ($userComments as $comment): ?>
                <li class="comment-item" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9;" id="comment-<?php echo htmlspecialchars($comment['id']); ?>">
                    <div class="comment-content" style="margin-bottom: 5px;">
                        <?php echo nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                    <div class="comment-meta" style="font-size: 0.85em; color: #555;">
                        Posted: <?php echo date('M d, Y H:i', strtotime($comment['created_at'])); ?>

                        <!-- Delete Button Form -->
                        <form action="index.php?page=dashboard" method="POST" style="display: inline; margin-left: 15px;">
                            <input type="hidden" name="action" value="delete_comment">
                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                            <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this comment?');" style="color: red; background: none; border: none; padding: 0; font-size: 0.85em; cursor: pointer;">
                                Delete
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: // No error, but no comments found ?>
        <p>You haven't written any comments yet.</p>
    <?php endif; ?>

</div>
<!-- End Page Content -->

<!-- Optional basic styling (better in a separate CSS file) -->
<style>
    .user-comments-section { margin-top: 20px; }
    .comment-list { list-style: none; padding: 0; }
    .comment-item {
        border: 1px solid #ddd;
        padding: 10px 15px;
        margin-bottom: 10px;
        background-color: #f9f9f9;
        border-radius: 4px;
    }
    .comment-content { margin-bottom: 5px; }
    .comment-meta { font-size: 0.85em; color: #555; }
    .error { color: #D8000C; background-color: #FFD2D2; padding: 10px; border: 1px solid #D8000C; border-radius: 3px; margin-bottom: 15px; }
</style>