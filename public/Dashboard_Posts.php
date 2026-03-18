<?php
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

requireLogin();

$pageTitle = 'Dashboard • DATA Labo';
require __DIR__ . '/_header.php';

$myPosts = dbSelectAll(
  "SELECT post_id, post_title, status, created_at, updated_at
   FROM posts
   WHERE user_id = ?
   ORDER BY created_at DESC, post_id DESC",
  [currentUserId()],
  "i"
);

$stats = dbSelectOne(
  "SELECT
      SUM(status='published') AS published_cnt,
      SUM(status='draft') AS draft_cnt,
      COUNT(*) AS total_cnt
   FROM posts
   WHERE user_id = ?",
  [currentUserId()],
  "i"
);

$publishedCnt = (int)($stats['published_cnt'] ?? 0);
$draftCnt = (int)($stats['draft_cnt'] ?? 0);
$totalCnt = (int)($stats['total_cnt'] ?? 0);
?>

<div class="bl-shell">
  <aside class="bl-sidebar bl-glass">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-grid-1x2 fs-5"></i>
      <div>
        <div class="fw-bold">Dashboard</div>
        <div class="text-muted small"><?= htmlspecialchars((string)currentUserName()) ?></div>
      </div>
    </div>

    <div class="vstack gap-1">
      <a class="bl-side-link" href="Dashboard_Posts.php"><i class="bi bi-journal-text"></i>My Posts</a>
      <a class="bl-side-link" href="Post_Create.php" data-spinner="1"><i class="bi bi-plus-circle"></i>New Post</a>
      <a class="bl-side-link" href="Posts_List.php" data-spinner="1"><i class="bi bi-globe2"></i>Public Posts</a>

      <?php if (isUserAdmin()): ?>
        <div class="mt-2 text-muted small">Admin tools</div>
        <a class="bl-side-link" href="Admin_Comments.php" data-spinner="1"><i class="bi bi-chat-left-dots"></i>Manage Comments</a>
        <a class="bl-side-link" href="Admin_Users.php" data-spinner="1"><i class="bi bi-people"></i>Manage Users</a>
      <?php endif; ?>
    </div>

    <hr class="my-3 opacity-25">

    <div class="small">
      <div class="d-flex justify-content-between">
        <span class="text-muted">Published</span><span class="fw-bold"><?= $publishedCnt ?></span>
      </div>
      <div class="d-flex justify-content-between">
        <span class="text-muted">Drafts</span><span class="fw-bold"><?= $draftCnt ?></span>
      </div>
      <div class="d-flex justify-content-between">
        <span class="text-muted">Total</span><span class="fw-bold"><?= $totalCnt ?></span>
      </div>
    </div>
  </aside>

  <section>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h1 class="h3 mb-0">My posts</h1>
        <div class="text-muted small">Create, edit, delete, and publish posts.</div>
      </div>
      <a class="btn btn-gradient" href="Post_Create.php" data-spinner="1"><i class="bi bi-plus-lg me-1"></i>Create post</a>
    </div>

    <?php if (empty($myPosts)): ?>
      <div class="bl-glass p-4">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-inbox fs-4"></i>
          <div>
            <div class="fw-bold">No posts yet</div>
            <div class="text-muted small">Create your first post to get started.</div>
          </div>
        </div>
        <div class="mt-3">
          <a class="btn btn-gradient" href="Post_Create.php" data-spinner="1"><i class="bi bi-plus-lg me-1"></i>New Post</a>
        </div>
      </div>
    <?php else: ?>
      <div class="card bl-card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Title</th>
                <th>Status</th>
                <th class="d-none d-md-table-cell">Created</th>
                <th class="d-none d-md-table-cell">Updated</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($myPosts as $p): ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars((string)$p['post_title']) ?></td>
                  <td>
                    <?php if ($p['status'] === 'published'): ?>
                      <span class="badge text-bg-success"><i class="bi bi-broadcast-pin me-1"></i>Published</span>
                    <?php else: ?>
                      <span class="badge text-bg-secondary"><i class="bi bi-eye-slash me-1"></i>Draft</span>
                    <?php endif; ?>
                  </td>
                  <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars((string)$p['created_at']) ?></td>
                  <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars((string)$p['updated_at']) ?></td>
                  <td class="text-end">
                    <div class="d-inline-flex gap-2">
                      <a class="btn btn-soft btn-sm" href="Post_Edit.php?post_id=<?= (int)$p['post_id'] ?>" data-spinner="1">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                      </a>

                      <?php if ($p['status'] !== 'published'): ?>
                        <form method="POST" action="servers/post_publish.php" class="m-0">
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                          <input type="hidden" name="post_id" value="<?= (int)$p['post_id'] ?>">
                          <button class="btn btn-gradient btn-sm" type="submit">
                            <i class="bi bi-upload me-1"></i>Publish
                          </button>
                        </form>
                      <?php endif; ?>

                      <form method="POST" action="servers/post_delete.php" class="m-0">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                        <input type="hidden" name="post_id" value="<?= (int)$p['post_id'] ?>">
                        <button class="btn btn-soft btn-sm" type="submit" onclick="return confirm('Delete this post?');">
                          <i class="bi bi-trash me-1"></i>Delete
                        </button>
                      </form>
                    </div>
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
