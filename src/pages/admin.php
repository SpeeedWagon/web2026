<?php
// This file is intended to be included by index.php,
// where session_start() is already called and $pdo is initialized.

// Double-check admin privileges (defense in depth)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "<p class='alert alert-danger'>Access Denied. You must be an administrator.</p>";
    return; // Stop further processing of this file
}

$commentsWithUsers = [];
$fetchError = null;
$statusMessage = ''; // For success messages
$errorMessage = '';  // For error messages

// Handle status messages from URL (set by index.php after POST actions)
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'admin_comment_deleted_success':
            $statusMessage = "Comentariul a fost șters cu succes.";
            break;
        // ... (other status cases) ...
        case 'admin_delete_error_db':
            $errorMessage = "A apărut o eroare la baza de date în timpul ștergerii comentariului. Verificați log-urile serverului.";
            break;
    }
}


try {
    $sql = "SELECT
                c.id AS comment_id,
                c.content,
                c.created_at AS comment_created_at,
                u.id AS user_id,
                u.user_name,
                u.profile_image_path -- Selected avatar path
            FROM
                comments c
            LEFT JOIN
                users u ON c.user_id = u.id
            ORDER BY
                c.created_at DESC";

    $stmt = $pdo->query($sql);
    $commentsWithUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Admin page: Error fetching comments: " . $e->getMessage());
    $fetchError = "Nu s-au putut prelua comentariile din baza de date. Vă rugăm verificați log-urile serverului.";
}

// Define a path for a default avatar image if you have one
$defaultAvatarPath = 'uploads/avatars/default-avatar.png'; //  <<-- IMPORTANT: Update this path to your actual default avatar if you have one

?>

<div class="container mt-4 mb-4">
    <h2>Admin Panel - Toate Comentariile Utilizatorilor</h2>

    <?php if ($statusMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($statusMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($fetchError): ?>
        <div class="alert alert-warning">
            <?php echo htmlspecialchars($fetchError); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($commentsWithUsers) && !$fetchError): ?>
        <div class="alert alert-info">
            Niciun comentariu găsit în sistem.
        </div>
    <?php elseif (!empty($commentsWithUsers)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover align-middle"> 
                <thead class="thead-dark">
                    <tr>
                        <th>ID Com.</th>
                        <th style="min-width: 200px;">Utilizator</th> 
                        <th>Conținut Comentariu</th>
                        <th>Data Postării</th>
                        <th style="width: 100px;">Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commentsWithUsers as $item): ?>
                        <tr id="comment-row-<?php echo htmlspecialchars($item['comment_id']); ?>">
                            <td><?php echo htmlspecialchars($item['comment_id']); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php
                                    $avatarSrc = null;
                                    if (!empty($item['profile_image_path']) && file_exists($item['profile_image_path'])) {
                                        $avatarSrc = $item['profile_image_path'];
                                    } elseif (file_exists($defaultAvatarPath)) { // Check if default avatar exists
                                        $avatarSrc = $defaultAvatarPath;
                                    }
                                    ?>
                                    <?php if ($avatarSrc): ?>
                                        <img src="<?php echo htmlspecialchars($avatarSrc); ?>"
                                             alt="<?php echo htmlspecialchars($item['user_name'] ?? 'User'); ?>'s avatar"
                                             class="rounded-circle me-2 flex-shrink-0"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle me-2 bg-secondary flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex-grow-1">
                                        <?php if ($item['user_name']): ?>
                                            <strong><?php echo htmlspecialchars($item['user_name']); ?></strong><br>
                                            <small class="text-muted">ID: <?php echo htmlspecialchars($item['user_id']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Utilizator șters sau necunoscut</span><br>
                                            <small class="text-muted">ID: <?php echo htmlspecialchars($item['user_id']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($item['content'])); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($item['comment_created_at']))); ?></td>
                            <td>
                                <form action="index.php?page=admin" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_comment_admin">
                                    <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($item['comment_id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Șterge comentariul"
                                            onclick="return confirm('Sunteți sigur că doriți să ștergeți acest comentariu? Această acțiune este ireversibilă.');">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-trash3-fill align-text-bottom" viewBox="0 0 16 16">
                                            <path
                                                d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                        </svg>
                                        Șterge
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>