<?php
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

startSession();
requireAdmin();
csrf_verify_or_die();

$userName = trim($_POST['user_name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$role     = trim($_POST['role'] ?? 'user');
$password = (string)($_POST['password'] ?? '');

// Basic validation
if ($userName === '' || $password === '') {
  setError('Username and password are required.');
  redirectTo('/Admin_Users.php');
}

if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $userName)) {
  setError('Username must be 3–50 chars: letters, numbers, underscore.');
  redirectTo('/Admin_Users.php');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  setError('Invalid email.');
  redirectTo('/Admin_Users.php');
}

if (!in_array($role, ['user', 'admin'], true)) {
  setError('Invalid role.');
  redirectTo('/Admin_Users.php');
}

// Password policy (minimal, adjust if you want)
if (strlen($password) < 8) {
  setError('Password must be at least 8 characters.');
  redirectTo('/Admin_Users.php');
}

// Hash password (NEVER store plain)
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$sql = "INSERT INTO users (user_name, email, role, password_hash)
        VALUES (?, ?, ?, ?)";

try {
  // email μπορεί να είναι '' από τη φόρμα → κάν' το NULL για να μην μπλέκει με UNIQUE
  $emailOrNull = ($email === '') ? null : $email;

  dbExecute($sql, [$userName, $emailOrNull, $role, $hash]);
} catch (Throwable $e) {
  // Συνήθως εδώ θα πέσεις σε duplicate username/email
  setError('Could not create user. Username/email may already exist.');
  redirectTo('/Admin_Users.php');
}

setSuccess('User created.');
redirectTo('/Admin_Users.php');