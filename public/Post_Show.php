<?php
require_once __DIR__ . '/../functions/databaseFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';

$postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
if ($postId <= 0) {
  setError('Invalid post.');
  redirectTo('/errorPage.php');
}

$post = dbSelectOne(
  "SELECT p.post_id, p.user_id, p.post_title, p.post_body, p.status, p.created_at, p.updated_at, u.user_name AS author
   FROM posts p
   JOIN users u ON u.user_id = p.user_id
   WHERE p.post_id = ?
   LIMIT 1",
  [$postId]
);

if (!$post) {
  setError('Post not found.');
  redirectTo('/errorPage.php');
}

// Draft is visible only to owner/admin
if ($post['status'] !== 'published') {
  requireLogin();
  $isOwner = ((int)$post['user_id'] === (int)currentUserId());
  if (!$isOwner && !isUserAdmin()) {
    setError('Post not available.');
    redirectTo('/Posts_List.php');
  }
}

$comments = dbSelectAll(
  "SELECT comment_id, author_name, author_email, comment_body, created_at
   FROM comments
   WHERE post_id = ?
   ORDER BY created_at DESC, comment_id DESC",
  [$postId],
  "i"
);

$pageTitle = htmlspecialchars((string)$post['post_title']) . ' • DATA Labo';
require __DIR__ . '/_header.php';

$isPublished = ($post['status'] === 'published');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <a class="btn btn-soft btn-sm" href="Posts_List.php" data-spinner="1">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>

  <div class="d-flex gap-2 align-items-center">
    <?php if ($isPublished): ?>
      <span class="badge text-bg-success"><i class="bi bi-broadcast-pin me-1"></i>Published</span>
    <?php else: ?>
      <span class="badge text-bg-secondary"><i class="bi bi-eye-slash me-1"></i>Draft</span>
    <?php endif; ?>

    <span class="text-muted small d-none d-md-inline">
      <i class="bi bi-clock me-1"></i><?= htmlspecialchars((string)$post['created_at']) ?>
    </span>
  </div>
</div>

<div class="row g-4">
  <div class="col-12 col-lg-7">
    <article class="card bl-card">
      <div class="card-body p-4 p-lg-5">
        <h1 class="h2 fw-black mb-2"><?= htmlspecialchars((string)$post['post_title']) ?></h1>

        <div class="bl-meta mb-4">
          <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars((string)$post['author']) ?>
          <?php if (!empty($post['updated_at'])): ?>
            <span class="mx-2 opacity-50">•</span>
            <i class="bi bi-arrow-repeat me-1"></i>Updated <?= htmlspecialchars((string)$post['updated_at']) ?>
          <?php endif; ?>
        </div>

        <div style="white-space: pre-wrap; line-height: 1.75;">
          <?= htmlspecialchars((string)$post['post_body']) ?>
        </div>
      </div>
    </article>

    <section id="comments" class="mt-4">
      <div class="card bl-card">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0"><i class="bi bi-chat-left-text me-2"></i>Comments</h2>
            <span class="text-muted small"><?= count($comments) ?> total</span>
          </div>

          <?php if (empty($comments)): ?>
            <div class="text-muted">No comments yet.</div>
          <?php else: ?>
            <div class="vstack gap-3">
              <?php foreach ($comments as $c): ?>
                <div class="bl-comment" data-reveal="1">
                  <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                      <strong><?= htmlspecialchars((string)$c['author_name']) ?></strong>
                      <?php if (!empty($c['author_email'])): ?>
                        <span class="text-muted small">(<?= htmlspecialchars((string)$c['author_email']) ?>)</span>
                      <?php endif; ?>
                    </div>
                    <div class="text-muted small"><i class="bi bi-clock-history me-1"></i><?= htmlspecialchars((string)$c['created_at']) ?></div>
                  </div>

                  <div style="white-space: pre-wrap;"><?= htmlspecialchars((string)$c['comment_body']) ?></div>

                  <?php if (isUserAdmin()): ?>
                    <form method="POST" action="servers/comment_delete.php" class="mt-3">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="comment_id" value="<?= (int)$c['comment_id'] ?>">
                      <input type="hidden" name="post_id" value="<?= (int)$postId ?>">
                      <input type="hidden" name="from" value="post">
                      <button class="btn btn-soft btn-sm" type="submit"
                              onclick="return confirm('Delete this comment?');">
                        <i class="bi bi-trash me-1"></i>Delete (admin)
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </div>

  <div class="col-12 col-lg-5">
    <div class="card bl-card">
      <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="bi bi-pen me-2"></i>Leave a comment</h2>

        <?php if (!$isPublished): ?>
          <div class="bl-glass p-3">
            <i class="bi bi-lock me-1"></i>Comments are disabled for draft posts.
          </div>
        <?php else: ?>
          <form method="POST" action="servers/comment_create.php" class="vstack gap-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="post_id" value="<?= (int)$postId ?>">

            <div>
              <label class="form-label">Name</label>
              <input class="form-control" name="author_name" maxlength="80" required placeholder="Your name">
            </div>

            <div>
              <label class="form-label">Email (optional)</label>
              <input class="form-control" name="author_email" maxlength="120" placeholder="you@example.com">
            </div>

            <div>
              <label class="form-label">Comment</label>
              <textarea class="form-control" name="comment_body" rows="5" maxlength="1000" required placeholder="Write something useful…"></textarea>
              <div class="form-text">No login required. Be respectful.</div>
            </div>

            <button class="btn btn-gradient" type="submit">
              <i class="bi bi-send me-1"></i>Post comment
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <?php if (existsLoggedUser()): ?>
      <div class="mt-4 bl-glass p-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-lightning-charge"></i>
          <div>
            <div class="fw-bold">Quick actions</div>
            <div class="text-muted small">Go to dashboard or create a new post.</div>
          </div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <a class="btn btn-soft btn-sm" href="Dashboard_Posts.php" data-spinner="1"><i class="bi bi-grid-1x2 me-1"></i>Dashboard</a>
          <a class="btn btn-gradient btn-sm" href="Post_Create.php" data-spinner="1"><i class="bi bi-plus-lg me-1"></i>New Post</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
