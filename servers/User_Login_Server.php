<?php
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';

startSession();

if (!isRequestMethodPost()) {
  setError('Invalid request method.');
  redirectTo('/errorPage.php');
}

csrf_verify_or_die();

$userName = trim($_POST['user_name'] ?? '');
$password = $_POST['password'] ?? '';

if ($userName === '' || $password === '') {
  setError('Please enter username and password.');
  redirectTo('/User_Login.php');
}

// Fetch user by username
$user = dbSelectOne(
  "SELECT user_id, user_name, role, password_hash
   FROM users
   WHERE user_name = ?
   LIMIT 1",
  [$userName]
);

// Do NOT leak whether user exists
if (!$user || !password_verify($password, (string)$user['password_hash'])) {
  setError('Invalid username or password.');
  redirectTo('/User_Login.php');
}

logUserIn((int)$user['user_id'], (string)$user['user_name'], (string)$user['role']);

setSuccess('Welcome back!');
redirectTo('/Dashboard_Posts.php');
