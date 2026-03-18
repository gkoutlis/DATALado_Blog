<?php
// servers/Post_Delete_Server.php
// POST: delete existing post (owner-only; admin can delete too).
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';

startSession();

if (!isRequestMethodPost()) {
  setError('Invalid request method.');
  redirectTo('/errorPage.php');
}

requireLogin();
csrf_verify_or_die();

$postId = (int)($_POST['post_id'] ?? 0);
if ($postId <= 0) {
  setError('Invalid post.');
  redirectTo('/errorPage.php');
}

// Fetch post to enforce owner/admin
$post = dbSelectOne(
  "SELECT post_id, user_id FROM posts WHERE post_id = ? LIMIT 1",
  [$postId]
);

if (!$post) {
  setError('Post not found.');
  redirectTo('/errorPage.php');
}

$isOwner = ((int)$post['user_id'] === (int)currentUserId());
if (!$isOwner && !isUserAdmin()) {
  setError('Owner-only: you cannot delete this post.');
  redirectTo('/errorPage.php');
}

dbExecute("DELETE FROM posts WHERE post_id = ?", [$postId]);

setSuccess('Post deleted.');
redirectTo('/Dashboard_Posts.php');
