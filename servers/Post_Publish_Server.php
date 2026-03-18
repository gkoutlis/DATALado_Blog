<?php
// servers/Post_Publish_Server.php
// POST: publish a draft post (owner-only; admin can publish too).
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

$post = dbSelectOne(
  "SELECT post_id, user_id, status FROM posts WHERE post_id = ? LIMIT 1",
  [$postId]
);

if (!$post) {
  setError('Post not found.');
  redirectTo('/errorPage.php');
}

$isOwner = ((int)$post['user_id'] === (int)currentUserId());
if (!$isOwner && !isUserAdmin()) {
  setError('Owner-only: you cannot publish this post.');
  redirectTo('/errorPage.php');
}

if ($post['status'] === 'published') {
  setSuccess('Post is already published.');
  redirectTo('/Post_Edit.php?post_id=' . $postId);
}

dbExecute(
  "UPDATE posts
   SET status = 'published', published_at = NOW()
   WHERE post_id = ?",
  [$postId]
);

setSuccess('Post published.');
redirectTo('/Post_Edit.php?post_id=' . $postId);
