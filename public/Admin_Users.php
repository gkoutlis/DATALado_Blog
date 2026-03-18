<?php
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

requireAdmin();

$pageTitle = 'Admin Users • DATA Labo';
require __DIR__ . '/_header.php';

$users = dbSelectAll(
  "SELECT user_id, user_name, email, role, created_at
   FROM users
   ORDER BY created_at DESC, user_id DESC"
);
?>

<div class="bl-shell">
  <aside class="bl-sidebar bl-glass">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-shield-lock fs-5"></i>
      <div>
        <div class="fw-bold">Admin</div>
        <div class="text-muted small">Users</div>
      </div>
    </div>

    <div class="vstack gap-1">
      <a class="bl-side-link" href="Dashboard_Posts.php" data-spinner="1"><i class="bi bi-grid-1x2"></i>Dashboard</a>
      <a class="bl-side-link" href="Admin_Comments.php" data-spinner="1"><i class="bi bi-chat-left-dots"></i>Comments</a>
      <a class="bl-side-link" href="Admin_Users.php"><i class="bi bi-people"></i>Users</a>
      <a class="bl-side-link" href="Posts_List.php" data-spinner="1"><i class="bi bi-globe2"></i>Public</a>
    </div>

    <hr class="my-3 opacity-25">

    <div class="small text-muted">
      Tip: χρησιμοποίησε “password” μόνο για demo. Σε πραγματικό app, απαραίτητο δυνατό password.
    </div>
  </aside>

  <section>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h1 class="h3 mb-0">Manage users</h1>
        <div class="text-muted small">Create demo users and assign roles.</div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-5">
        <div class="card bl-card">
          <div class="card-body p-4">
            <h2 class="h5 mb-3"><i class="bi bi-person-plus me-2"></i>Create user</h2>

            <form method="POST" action="servers/user_create.php" class="vstack gap-3">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

              <div>
                <label class="form-label">Username</label>
                <input class="form-control" name="user_name" required maxlength="50" placeholder="user2">
              </div>

              <div>
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" required maxlength="120" placeholder="user2@example.com">
              </div>

              <div>
                <label class="form-label">Phone (optional)</label>
                <input class="form-control" name="phone" maxlength="30" placeholder="+30…">
              </div>

              <div>
                <label class="form-label">Role</label>
                <select class="form-select" name="role" required>
                  <option value="user" selected>User</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <div>
                <label class="form-label">Password</label>
                <input class="form-control" name="password" type="text" required maxlength="72" placeholder="password">
                <div class="form-text">Demo: βάζεις απλό password για να τεστάρεις γρήγορα.</div>
              </div>

              <button class="btn btn-gradient" type="submit">
                <i class="bi bi-check2-circle me-1"></i>Create
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-7">
        <div class="card bl-card">
          <div class="card-body p-0">
            <div class="p-4 pb-0">
              <h2 class="h5 mb-0"><i class="bi bi-people me-2"></i>Users</h2>
            </div>

            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>User</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th>Role</th>
                    <th class="d-none d-lg-table-cell">Created</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $u): ?>
                    <tr>
                      <td class="fw-semibold">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars((string)$u['user_name']) ?>
                      </td>
                      <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars((string)$u['email']) ?></td>
                      <td>
                        <?php if ($u['role'] === 'admin'): ?>
                          <span class="bl-badge bl-badge-admin"><i class="bi bi-shield-check me-1"></i>admin</span>
                        <?php else: ?>
                          <span class="bl-badge bl-badge-user"><i class="bi bi-person-check me-1"></i>user</span>
                        <?php endif; ?>
                      </td>
                      <td class="d-none d-lg-table-cell text-muted small"><?= htmlspecialchars((string)$u['created_at']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
