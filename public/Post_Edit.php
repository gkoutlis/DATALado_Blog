<?php
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/databaseFunctions.php';

requireLogin();

$postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
if ($postId <= 0) {
  setError('Invalid post.');
  redirectTo('/errorPage.php');
}

$post = dbSelectOne(
  "SELECT post_id, user_id, post_title, post_body, status, created_at, updated_at
   FROM posts
   WHERE post_id = ?
   LIMIT 1",
  [$postId],
  "i"
);

if (!$post) {
  setError('Post not found.');
  redirectTo('/errorPage.php');
}

// Only owner or admin can edit
$isOwner = ((int)$post['user_id'] === (int)currentUserId());
if (!$isOwner && !isUserAdmin()) {
  setError('You cannot edit this post.');
  redirectTo('/Dashboard_Posts.php');
}

$pageTitle = 'Edit Post • DATA Labo';
require __DIR__ . '/_header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h1 class="h3 mb-0">Edit post</h1>
    <div class="text-muted small">
      <i class="bi bi-clock me-1"></i>Created <?= htmlspecialchars((string)$post['created_at']) ?>
      <?php if (!empty($post['updated_at'])): ?>
        <span class="mx-2 opacity-50">•</span>
        Updated <?= htmlspecialchars((string)$post['updated_at']) ?>
      <?php endif; ?>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-soft" href="Dashboard_Posts.php" data-spinner="1"><i class="bi bi-arrow-left me-1"></i>Back</a>
    <a class="btn btn-soft" href="Post_Show.php?post_id=<?= (int)$postId ?>" data-spinner="1"><i class="bi bi-eye me-1"></i>Preview</a>
  </div>
</div>

<div class="card bl-card">
  <div class="card-body p-4 p-lg-5">
    <form method="POST" action="servers/post_update.php" class="vstack gap-3">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="post_id" value="<?= (int)$postId ?>">

      <div>
        <label class="form-label">Title</label>
        <input class="form-control" type="text" name="post_title" maxlength="200" required
               value="<?= htmlspecialchars((string)$post['post_title']) ?>">
      </div>

      <div>
        <label class="form-label">Body</label>
        <textarea class="form-control" name="post_body" rows="12" required><?= htmlspecialchars((string)$post['post_body']) ?></textarea>
      </div>

      <div>
        <label class="form-label">Status</label>
        <select class="form-select" name="status" required>
          <option value="draft" <?= ($post['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
          <option value="published" <?= ($post['status'] === 'published') ? 'selected' : '' ?>>Published</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-gradient" type="submit">
          <i class="bi bi-save me-1"></i>Save changes
        </button>
        <form method="POST" action="servers/post_delete.php" class="m-0">
          <!-- nested forms invalid; keep delete as separate form below -->
        </form>
      </div>
    </form>

    <hr class="my-4">

    <form method="POST" action="servers/post_delete.php" class="m-0">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="post_id" value="<?= (int)$postId ?>">
      <button class="btn btn-soft" type="submit" onclick="return confirm('Delete this post?');">
        <i class="bi bi-trash me-1"></i>Delete post
      </button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
