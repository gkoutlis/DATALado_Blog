<?php
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';

$pageTitle = 'Login • DATA Labo';
require __DIR__ . '/_header.php';

// If already logged in, go dashboard
if (existsLoggedUser()) {
  redirectTo('/Dashboard_Posts.php');
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-5">
    <div class="card bl-card">
      <div class="card-body p-4 p-lg-5">
        <h1 class="h4 mb-2"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h1>
        <p class="text-muted small mb-4">Use your demo credentials to access the dashboard.</p>

        <form method="POST" action="servers/user_login.php" class="vstack gap-3">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

          <div>
            <label class="form-label">Username</label>
            <input class="form-control" name="user_name" required placeholder="admin / user / user2">
          </div>

          <div>
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required placeholder="Admin123! / User123! / password">
          </div>

          <button class="btn btn-gradient" type="submit">
            <i class="bi bi-shield-check me-1"></i>Sign in
          </button>

          <a class="btn btn-soft" href="Posts_List.php" data-spinner="1">
            <i class="bi bi-globe2 me-1"></i>Back to posts
          </a>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
