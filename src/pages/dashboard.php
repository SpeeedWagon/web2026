<?php
// We assume session is started and user is logged in already.
// This code block would typically be within a file like 'pages/my_comments.php'
// or a component included on a secure page.

// 1. Get the current user's ID from the session
//    (Add an extra check just in case, though the calling page should ensure this)
if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>Error: User ID not found in session.</p>";
    // Prevent further execution if ID is missing
    return; // Or exit, depending on context
}
$currentUserId = $_SESSION['user_id'];

// 2. Define the SQL Query using columns from your schema
$sql = "SELECT id, content, created_at
        FROM comments
        WHERE user_id = :user_id
        ORDER BY created_at DESC"; // Show newest comments first

// 3. Initialize variables
$userComments = [];
$dbError = null;

// 4. Prepare, execute, and fetch comments
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $currentUserId]);
    $userComments = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all matching rows

} catch (PDOException $e) {
    $dbError = "Could not retrieve your comments. Please try again later.";
    // Log the detailed error for debugging
    error_log("Database error fetching comments for user {$currentUserId}: " . $e->getMessage());
}

?>

<!-- HTML to display the comments -->
<div class="user-comments-section">
    <h2>My Comments</h2>

    <?php if ($dbError): // Display error if one occurred ?>
        <p class="error" style="color: red; border: 1px solid red; padding: 5px;">
            <?php echo htmlspecialchars($dbError); ?>
        </p>
    <?php endif; ?>


    <?php if (!$dbError && !empty($userComments)): // Display list if no error and comments exist ?>
        <ul class="comment-list">
            <?php foreach ($userComments as $comment): ?>
                <li class="comment-item" id="comment-<?php echo htmlspecialchars($comment['id']); ?>">
                    <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?>
                        <?php // nl2br converts newlines in the text to <br> tags for display ?>
                    </div>
                    <div class="comment-meta">
                        Posted on: <?php echo date('M d, Y \a\t H:i', strtotime($comment['created_at'])); ?>
                        <?php /* You could add links here, e.g., to the post the comment is on */ ?>
                        <?php /* Example: <a href="index.php?page=view_post&id=<?php echo htmlspecialchars($comment['post_id']); ?>">View Post</a> */ ?>
                        <?php /* Add Edit/Delete links if applicable */ ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif (!$dbError): // No error, but no comments found ?>
        <p>You haven't written any comments yet.</p>
    <?php endif; ?>

</div>

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