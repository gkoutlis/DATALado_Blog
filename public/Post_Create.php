<?php
require_once __DIR__ . '/../functions/userFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';

requireLogin();

$pageTitle = 'Create Post • DATA Labo';
require __DIR__ . '/_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h3 mb-0">Create post</h1>
    <div class="text-muted small">Write, save as draft, then publish from Dashboard.</div>
  </div>
  <a class="btn btn-soft" href="Dashboard_Posts.php" data-spinner="1"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card bl-card">
  <div class="card-body p-4 p-lg-5">
    <form method="POST" action="servers/post_create.php" class="vstack gap-3">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

      <div>
        <label class="form-label">Title</label>
        <input class="form-control" type="text" name="post_title" maxlength="200" required placeholder="A clear, specific title…">
      </div>

      <div>
        <label class="form-label">Body</label>
        <textarea class="form-control" name="post_body" rows="10" required placeholder="Write something useful…"></textarea>
      </div>

      <div>
        <label class="form-label">Status</label>
        <select class="form-select" name="status" required>
          <option value="draft" selected>Draft</option>
          <option value="published">Published</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-gradient" type="submit">
          <i class="bi bi-check2-circle me-1"></i>Create
        </button>
        <a class="btn btn-soft" href="Posts_List.php" data-spinner="1"><i class="bi bi-globe2 me-1"></i>View public</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
