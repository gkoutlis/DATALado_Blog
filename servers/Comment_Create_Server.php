<?php
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

startSession();
csrf_verify_or_die();

$postId = (int)($_POST['post_id'] ?? 0);

$authorName  = trim($_POST['author_name'] ?? '');
$authorEmail = trim($_POST['author_email'] ?? '');
$commentBody = trim($_POST['comment_body'] ?? '');

if ($postId <= 0) {
  setError('Invalid post.');
  redirectTo('/errorPage.php');
}

if ($authorName === '' || $commentBody === '') {
  setError('Name and comment are required.');
  redirectTo('/Post_Show.php?post_id=' . $postId);
}

if (mb_strlen($authorName) > 80) {
  setError('Name is too long (max 80).');
  redirectTo('/Post_Show.php?post_id=' . $postId);
}

if ($authorEmail !== '' && !filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
  setError('Invalid email.');
  redirectTo('/Post_Show.php?post_id=' . $postId);
}

if (mb_strlen($commentBody) > 2000) {
  setError('Comment is too long (max 2000 chars).');
  redirectTo('/Post_Show.php?post_id=' . $postId);
}

// (προαιρετικό αλλά καλό) έλεγξε ότι το post υπάρχει και είναι published
$post = dbSelectOne("SELECT post_id, status FROM posts WHERE post_id = ? LIMIT 1", [$postId]);
if (!$post || $post['status'] !== 'published') {
  setError('Post not found.');
  redirectTo('/errorPage.php');
}

// Insert
$sql = "INSERT INTO comments (post_id, author_name, author_email, comment_body)
        VALUES (?, ?, ?, ?)";

$emailOrNull = ($authorEmail === '') ? null : $authorEmail;

dbExecute($sql, [$postId, $authorName, $emailOrNull, $commentBody]);

setSuccess('Comment posted.');
redirectTo('/Post_Show.php?post_id=' . $postId);