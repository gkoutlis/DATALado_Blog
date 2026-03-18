<?php
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

requireAdmin();

$pageTitle = 'Admin Comments • DATA Labo';
require __DIR__ . '/_header.php';

$comments = dbSelectAll(
  "SELECT c.comment_id, c.post_id, c.author_name, c.author_email, c.comment_body, c.created_at,
          p.post_title
   FROM comments c
   JOIN posts p ON p.post_id = c.post_id
   ORDER BY c.created_at DESC, c.comment_id DESC
   LIMIT 100"
);
?>

<div class="bl-shell">
  <aside class="bl-sidebar bl-glass">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-shield-lock fs-5"></i>
      <div>
        <div class="fw-bold">Admin</div>
        <div class="text-muted small">Comments</div>
      </div>
    </div>

    <div class="vstack gap-1">
      <a class="bl-side-link" href="Dashboard_Posts.php" data-spinner="1"><i class="bi bi-grid-1x2"></i>Dashboard</a>
      <a class="bl-side-link" href="Admin_Comments.php"><i class="bi bi-chat-left-dots"></i>Comments</a>
      <a class="bl-side-link" href="Admin_Users.php" data-spinner="1"><i class="bi bi-people"></i>Users</a>
      <a class="bl-side-link" href="Posts_List.php" data-spinner="1"><i class="bi bi-globe2"></i>Public</a>
    </div>

    <hr class="my-3 opacity-25">

    <div class="small text-muted">
      Showing latest 100 comments.
    </div>
  </aside>

  <section>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h1 class="h3 mb-0">Manage comments</h1>
        <div class="text-muted small">Delete abusive or test comments.</div>
      </div>
    </div>

    <?php if (empty($comments)): ?>
      <div class="bl-glass p-4">
        <i class="bi bi-inbox me-2"></i>No comments.
      </div>
    <?php else: ?>
      <div class="card bl-card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Comment</th>
                <th class="d-none d-lg-table-cell">Post</th>
                <th class="d-none d-md-table-cell">When</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($comments as $c): ?>
                <tr>
                  <td style="min-width: 320px;">
                    <div class="fw-semibold">
                      <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars((string)$c['author_name']) ?>
                      <?php if (!empty($c['author_email'])): ?>
                        <span class="text-muted small">(<?= htmlspecialchars((string)$c['author_email']) ?>)</span>
                      <?php endif; ?>
                    </div>
                    <div class="text-muted small" style="white-space: nowrap; overflow:hidden; text-overflow: ellipsis; max-width: 520px;">
                      <?= htmlspecialchars((string)$c['comment_body']) ?>
                    </div>
                  </td>
                  <td class="d-none d-lg-table-cell">
                    <a href="Post_Show.php?post_id=<?= (int)$c['post_id'] ?>" data-spinner="1">
                      <?= htmlspecialchars((string)$c['post_title']) ?>
                    </a>
                  </td>
                  <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars((string)$c['created_at']) ?></td>
                  <td class="text-end">
                    <form method="POST" action="servers/comment_delete.php" class="m-0">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="comment_id" value="<?= (int)$c['comment_id'] ?>">
                      <input type="hidden" name="post_id" value="<?= (int)$c['post_id'] ?>">
                      <input type="hidden" name="from" value="admin">
                      <button class="btn btn-soft btn-sm" type="submit" onclick="return confirm('Delete this comment?');">
                        <i class="bi bi-trash me-1"></i>Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
