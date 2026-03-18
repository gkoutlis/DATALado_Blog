<?php
// servers/Post_Update_Server.php
// POST: update existing post (owner-only; admin can update too).
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
$title  = trim($_POST['post_title'] ?? '');
$body   = trim($_POST['post_body'] ?? '');

if ($postId <= 0) {
  setError('Invalid post.');
  redirectTo('/errorPage.php');
}

if ($title === '' || $body === '') {
  setError('Title and content are required.');
  redirectTo('/Post_Edit.php?post_id=' . $postId);
}

if (mb_strlen($title) > 254) {
  setError('Title is too long (max 254).');
  redirectTo('/Post_Edit.php?post_id=' . $postId);
}

// Fetch post to enforce owner/admin
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
  setError('Owner-only: you cannot edit this post.');
  redirectTo('/errorPage.php');
}

// Update
$sql = "UPDATE posts
        SET post_title = ?, post_body = ?
        WHERE post_id = ?";

$affected = dbExecute($sql, [$title, $body, $postId]);

setSuccess('Post updated.');
redirectTo('/Post_Edit.php?post_id=' . $postId);
