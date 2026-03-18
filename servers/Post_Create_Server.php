<?php
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

startSession();
requireLogin();
csrf_verify_or_die();

$title = trim($_POST['post_title'] ?? '');
$body  = trim($_POST['post_body'] ?? '');

if ($title === '' || $body === '') {
  setError('Title and content are required.');
  redirectTo('/Post_Create.php');
}

$sql = "INSERT INTO posts (user_id, post_title, post_body, status)
        VALUES (?, ?, ?, 'draft')";

dbExecute($sql, [currentUserId(), $title, $body]);

setSuccess('Post created as draft.');
redirectTo('/Dashboard_Posts.php');
