<?php
// servers/Comment_Delete_Server.php
// POST: delete comment (admin-only).
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';

startSession();

if (!isRequestMethodPost()) {
  setError('Invalid request method.');
  redirectTo('/errorPage.php');
}

requireAdmin();
csrf_verify_or_die();

$commentId = (int)($_POST['comment_id'] ?? 0);
$postId = (int)($_POST['post_id'] ?? 0); // optional for redirect

if ($commentId <= 0) {
  setError('Invalid comment.');
  redirectTo('/errorPage.php');
}

// Fetch for redirect + existence
$comment = dbSelectOne(
  "SELECT comment_id, post_id FROM comments WHERE comment_id = ? LIMIT 1",
  [$commentId]
);

if (!$comment) {
  setError('Comment not found.');
  redirectTo('/errorPage.php');
}

dbExecute("DELETE FROM comments WHERE comment_id = ?", [$commentId]);

setSuccess('Comment deleted.');

$redirectPostId = $postId > 0 ? $postId : (int)$comment['post_id'];

// If you deleted from Admin_Comments, go back there. If you posted from a post page, you can pass post_id.
$from = $_POST['from'] ?? '';
if ($from === 'post') {
  redirectTo('/Post_Show.php?post_id=' . $redirectPostId);
}

redirectTo('/Admin_Comments.php');
