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

$searchTerm = $_GET['search_term'] ?? '';
$errorEditCommentId = null;

// Handle status messages from URL (set by index.php after POST actions)
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'admin_comment_deleted_success':
            $statusMessage = "Comentariul a fost șters cu succes.";
            break;
        case 'admin_delete_error_db':
            $errorMessage = "A apărut o eroare la baza de date în timpul ștergerii comentariului. Verificați log-urile serverului.";
            break;
        case 'admin_delete_invalid_id':
            $errorMessage = "ID-ul comentariului pentru ștergere este invalid.";
            break;
        case 'admin_comment_not_found_or_delete_failed':
            $errorMessage = "Comentariul nu a fost găsit sau ștergerea a eșuat (posibil deja șters).";
            break;
        case 'admin_comment_updated_success':
            $statusMessage = "Comentariul a fost actualizat cu succes.";
            break;
        case 'admin_comment_update_no_change':
            $statusMessage = "Comentariul nu a fost modificat (posibil conținut identic sau comentariul nu a fost găsit).";
            break;
        case 'admin_update_invalid_id':
            $errorMessage = "ID-ul comentariului pentru actualizare este invalid.";
            break;
        case 'admin_update_empty_content':
            $errorMessage = "Conținutul comentariului nu poate fi gol.";
            if (isset($_GET['comment_id_error'])) {
                $errorEditCommentId = (int)$_GET['comment_id_error'];
            }
            break;
        case 'admin_comment_not_found_for_update':
            $errorMessage = "Comentariul specificat pentru actualizare nu a fost găsit.";
            break;
        case 'admin_update_error_db':
            $errorMessage = "A apărut o eroare la baza de date în timpul actualizării comentariului.";
            break;
    }
}


try {
    $sql = "SELECT
                c.id AS comment_id,
                c.content,
                c.created_at AS comment_created_at,
                c.updated_at AS comment_updated_at, -- Added for display
                u.id AS user_id,
                u.user_name,
                u.profile_image_path -- Selected avatar path
            FROM
                comments c
            LEFT JOIN
                users u ON c.user_id = u.id";

    $params = [];
    if (!empty($searchTerm)) {
        $sql .= " WHERE c.content LIKE :searchTerm";
        $params[':searchTerm'] = '%' . $searchTerm . '%';
    }

    $sql .= " ORDER BY c.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $commentsWithUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Admin page: Error fetching comments: " . $e->getMessage());
    $fetchError = "Nu s-au putut prelua comentariile din baza de date. Vă rugăm verificați log-urile serverului.";
}

// Define a path for a default avatar image if you have one
$defaultAvatarPath = 'uploads/avatars/default-avatar.png';

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

    <!-- Search Form -->
    <form method="GET" action="index.php" class="mb-3">
        <input type="hidden" name="page" value="admin">
        <div class="row g-2">
            <div class="col-sm-9 col-md-8 col-lg-6">
                <input type="text" name="search_term" class="form-control" placeholder="Căutare în conținutul comentariilor..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            </div>
            <div class="col-sm-3 col-md-2 col-lg-2">
                <button type="submit" class="btn btn-primary w-100">Caută</button>
            </div>
            <?php if (!empty($searchTerm)): ?>
            <div class="col-sm-12 col-md-2 col-lg-2 mt-2 mt-sm-0">
                <a href="index.php?page=admin" class="btn btn-secondary w-100">Resetează</a>
            </div>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($commentsWithUsers) && !$fetchError): ?>
        <div class="alert alert-info">
            <?php echo !empty($searchTerm) ? 'Niciun comentariu găsit care să corespundă termenului de căutare.' : 'Niciun comentariu găsit în sistem.'; ?>
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
                        <th>Ultima Modificare</th>
                        <th style="min-width: 180px;">Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commentsWithUsers as $item):
                        $isEditingThisComment = ($errorEditCommentId === (int)$item['comment_id']);
                    ?>
                        <tr id="comment-row-<?php echo htmlspecialchars($item['comment_id']); ?>">
                            <td><?php echo htmlspecialchars($item['comment_id']); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php
                                    $avatarSrc = null;
                                    if (!empty($item['profile_image_path']) && file_exists($item['profile_image_path'])) {
                                        $avatarSrc = $item['profile_image_path'];
                                    } elseif (file_exists($defaultAvatarPath)) {
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
                            <td id="content-cell-<?php echo $item['comment_id']; ?>">
                                <div class="comment-display-<?php echo $item['comment_id']; ?>" style="<?php echo $isEditingThisComment ? 'display:none;' : 'display:block;'; ?>">
                                    <?php echo nl2br(htmlspecialchars($item['content'])); ?>
                                </div>
                                <div class="comment-edit-form-<?php echo $item['comment_id']; ?>" style="<?php echo $isEditingThisComment ? 'display:block;' : 'display:none;'; ?>">
                                    <form action="index.php?page=admin<?php echo !empty($searchTerm) ? '&search_term=' . urlencode($searchTerm) : ''; ?>" method="POST" class="mt-2">
                                        <input type="hidden" name="action" value="update_comment_admin">
                                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($item['comment_id']); ?>">
                                        <textarea name="comment_content" class="form-control mb-2" rows="3"><?php echo htmlspecialchars($item['content']); ?></textarea>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg align-text-bottom" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/></svg>
                                            Salvează
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleEditMode(<?php echo $item['comment_id']; ?>, false)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg align-text-bottom" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
                                            Anulează
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($item['comment_created_at']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($item['comment_updated_at']))); ?></td>
                            <td>
                                <div class="actions-container-<?php echo $item['comment_id']; ?>" style="<?php echo $isEditingThisComment ? 'display:none;' : 'display:inline-block;'; ?>">
                                    <button type="button" class="btn btn-sm btn-warning mb-1" onclick="toggleEditMode(<?php echo $item['comment_id']; ?>, true)" title="Editează comentariul">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill align-text-bottom" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.5-.5V9h-.5a.5.5 0 0 1-.5-.5V8.5H.5a.5.5 0 0 1-.5-.5V8s.5-1 1.5-1 1.5 1 1.5 1L6.5 2.5l8 8-2.146 2.146a.5.5 0 0 1-.708-.708l.646-.647-.708-.707L10.5 11.793 9.75 12.5l.354.354 1.5 1.5a.5.5 0 0 1 0 .708l-2.146 2.146zm.354-3.542a.5.5 0 0 0-.708 0L6.5 11.793l.707.707 1.647-1.646a.5.5 0 0 0 0-.708zM16 4.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 .5.5"/>
                                        </svg>
                                        Editează
                                    </button>
                                    <form action="index.php?page=admin<?php echo !empty($searchTerm) ? '&search_term=' . urlencode($searchTerm) : ''; ?>" method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_comment_admin">
                                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($item['comment_id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger mb-1" title="Șterge comentariul"
                                                onclick="return confirm('Sunteți sigur că doriți să ștergeți acest comentariu? Această acțiune este ireversibilă.');">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-trash3-fill align-text-bottom" viewBox="0 0 16 16">
                                                <path
                                                    d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                            </svg>
                                            Șterge
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleEditMode(commentId, isEditing) {
    const displayDiv = document.querySelector('.comment-display-' + commentId);
    const editFormDiv = document.querySelector('.comment-edit-form-' + commentId);
    const actionsContainer = document.querySelector('.actions-container-' + commentId);

    if (isEditing) {
        // Hide all other open edit forms first for a cleaner UI
        document.querySelectorAll('.comment-edit-form').forEach(form => {
            if (form !== editFormDiv) {
                const otherId = form.className.match(/comment-edit-form-(\d+)/)[1];
                if (otherId) {
                    toggleEditMode(otherId, false); // Close other forms
                }
            }
        });

        if(displayDiv) displayDiv.style.display = 'none';
        if(editFormDiv) editFormDiv.style.display = 'block';
        if(actionsContainer) actionsContainer.style.display = 'none';
        if(editFormDiv) {
            const textarea = editFormDiv.querySelector('textarea');
            if (textarea) textarea.focus();
        }
    } else {
        if(displayDiv) displayDiv.style.display = 'block';
        if(editFormDiv) editFormDiv.style.display = 'none';
        if(actionsContainer) actionsContainer.style.display = 'inline-block';
    }
}
</script>