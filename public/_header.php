<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'DATA Labo') ?></title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Font (system-friendly; optional) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <!-- App theme -->
  <link rel="stylesheet" href="assets/css/app.css">

  <style>
    body{ font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
  </style>
</head>

<body>

<?php
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';

$flash = getFlash();
$flashType = $flash['type'] ?? null;
$flashMessage = $flash['message'] ?? null;

$toastHeader = 'Notice';
$toastIcon = 'bi-info-circle';
$toastBg = 'bg-primary';

if ($flashType) {
  if ($flashType === 'success') { $toastHeader='Success'; $toastIcon='bi-check-circle'; $toastBg='bg-success'; }
  elseif ($flashType === 'error') { $toastHeader='Error'; $toastIcon='bi-exclamation-triangle'; $toastBg='bg-danger'; }
  elseif ($flashType === 'warning') { $toastHeader='Warning'; $toastIcon='bi-exclamation-circle'; $toastBg='bg-warning'; }
  else { $toastHeader='Info'; $toastIcon='bi-info-circle'; $toastBg='bg-primary'; }
}
?>

<!-- Loading Spinner Overlay -->
<div id="blSpinnerOverlay" aria-hidden="true">
  <div class="text-center">
    <div class="spinner-border text-light" role="status" aria-label="Loading"></div>
    <div class="text-light mt-3 small">Loading…</div>
  </div>
</div>

<nav class="navbar navbar-expand-lg bl-navbar">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="Posts_List.php">
      <img src="/DATALaboBlog/public/assets/images/tux.png" alt="Linux" width="24" height="24" style="object-fit:contain;">
      <span class="fw-bold">DATALabo</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="Posts_List.php"><i class="bi bi-journal-text me-1"></i>Posts</a></li>

        <?php if (existsLoggedUser()): ?>
          <li class="nav-item"><a class="nav-link" href="Dashboard_Posts.php"><i class="bi bi-grid-1x2 me-1"></i>Dashboard</a></li>
          <?php if (isUserAdmin()): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-shield-lock me-1"></i>Admin
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="Admin_Comments.php"><i class="bi bi-chat-left-dots me-2"></i>Comments</a></li>
                <li><a class="dropdown-item" href="Admin_Users.php"><i class="bi bi-people me-2"></i>Users</a></li>
              </ul>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>

      <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-soft btn-sm" id="blThemeToggle" type="button" title="Toggle theme" aria-label="Toggle theme">
          <i class="bi bi-moon-stars"></i>
        </button>

        <?php if (existsLoggedUser()): ?>
          <div class="d-flex align-items-center gap-2">
            <span class="small text-muted d-none d-md-inline">Signed in as <strong><?= htmlspecialchars((string)currentUserName()) ?></strong></span>
            <?php $role = (string)(currentUserRole() ?? 'user'); ?>
            <span class="bl-badge <?= $role === 'admin' ? 'bl-badge-admin' : 'bl-badge-user' ?>">
              <i class="bi <?= $role === 'admin' ? 'bi-shield-check' : 'bi-person-check' ?> me-1"></i><?= htmlspecialchars($role) ?>
            </span>
          </div>

          <form method="POST" action="servers/user_logout.php" class="m-0">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button class="btn btn-soft btn-sm" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
          </form>
        <?php else: ?>
          <a class="btn btn-gradient btn-sm" href="User_Login.php" data-spinner="1"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Toast notifications (flash) -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2100;">
  <?php if (!empty($flashMessage)): ?>
    <div id="blFlashToast" class="toast align-items-center text-bg-dark border-0 bl-glass" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge <?= htmlspecialchars($toastBg) ?>"><i class="bi <?= htmlspecialchars($toastIcon) ?> me-1"></i><?= htmlspecialchars($toastHeader) ?></span>
            <span class="small opacity-75">Just now</span>
          </div>
          <div><?= htmlspecialchars((string)$flashMessage) ?></div>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>
</div>

<main class="container my-4">
