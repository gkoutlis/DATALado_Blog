<?php
require_once __DIR__ . '/../functions/databaseFunctions.php';
require_once __DIR__ . '/../functions/genericFunctions.php';

$pageTitle = 'Posts • DATA Labo';
require __DIR__ . '/_header.php';

$perPage = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$q = trim((string)($_GET['q'] ?? ''));
$hasSearch = $q !== '';
$like = '%' . $q . '%';

$whereSql = "p.status = 'published'";
$params = [];
$types = '';

if ($hasSearch) {
  $whereSql .= " AND (p.post_title LIKE ? OR p.post_body LIKE ? OR u.user_name LIKE ?)";
  $params[] = $like; $params[] = $like; $params[] = $like;
  $types .= 'sss';
}

// Count published posts (same WHERE as select!)
$countRow = dbSelectOne(
  "SELECT COUNT(*) AS cnt
   FROM posts p
   JOIN users u ON u.user_id = p.user_id
   WHERE $whereSql",
  $params,
  $types
);
$total = (int)($countRow['cnt'] ?? 0);
$totalPages = max(1, (int)ceil($total / $perPage));

if ($page > $totalPages) {
  $page = $totalPages;
  $offset = ($page - 1) * $perPage;
}

// Fetch page (deterministic ordering avoids duplicates between pages)
$params2 = $params;
$types2 = $types . 'ii';
$params2[] = $perPage;
$params2[] = $offset;

$posts = dbSelectAll(
  "SELECT p.post_id, p.post_title, p.post_body, p.created_at, u.user_name AS author
   FROM posts p
   JOIN users u ON u.user_id = p.user_id
   WHERE $whereSql
   ORDER BY p.created_at DESC, p.post_id DESC
   LIMIT ? OFFSET ?",
  $params2,
  $types2
);

function excerpt(string $text, int $len = 160): string {
  $t = trim(strip_tags($text));
  if (mb_strlen($t) <= $len) return $t;
  return mb_substr($t, 0, $len - 1) . '…';
}
?>
<!-- Hero -->
<section class="bl-hero bl-glass">
  <div class="bl-hero-inner">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
      <div>
        <h1 class="display-6 mb-2">DATA Labo</h1>
        <p class="mb-0 opacity-75">
          Ένα τεχνολογικό blog για developers που θέλουν καθαρό κώδικα, σωστή αρχιτεκτονική και σύγχρονες πρακτικές. 
          Διάβασε, πειραματίσου, σχολίασε και εξελίξου μαζί μας.
        </p>
      </div>

      <div class="d-flex gap-2">
        <?php if (existsLoggedUser()): ?>
          <a class="btn btn-gradient" href="Dashboard_Posts.php" data-spinner="1"><i class="bi bi-grid-1x2 me-1"></i>Dashboard</a>
        <?php else: ?>
          <a class="btn btn-gradient" href="User_Login.php" data-spinner="1"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
        <?php endif; ?>
        <a class="btn btn-soft" href="#latest"><i class="bi bi-arrow-down me-1"></i>Browse</a>
      </div>
    </div>

    <!-- Search -->
    <form class="mt-4" method="GET" action="Posts_List.php">
      <div class="input-group bl-search">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input class="form-control" type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search posts, content, author…">
        <button class="btn btn-gradient" type="submit"><i class="bi bi-magic me-1"></i>Search</button>
        <?php if ($hasSearch): ?>
          <a class="btn btn-soft" href="Posts_List.php"><i class="bi bi-x-circle me-1"></i>Clear</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</section>

<div id="latest" class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 mb-0">Latest posts</h2>
  <div class="text-muted small">
    <?= $hasSearch ? ('Results for “' . htmlspecialchars($q) . '”') : ($total . ' posts') ?>
  </div>
</div>

<?php if (empty($posts)): ?>
  <div class="bl-glass p-4">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-inbox fs-4"></i>
      <div>
        <div class="fw-bold">No posts found</div>
        <div class="text-muted small">Try a different search.</div>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="row">
    <?php foreach ($posts as $p): ?>
      <div class="col-md-6 col-lg-4 mb-4" data-reveal="1">
        <div class="card bl-card h-100">
          <div class="card-body p-4 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <h3 class="card-title h5 mb-2"><?= htmlspecialchars($p['post_title']) ?></h3>
              <span class="badge text-bg-success"><i class="bi bi-broadcast-pin me-1"></i>Published</span>
            </div>

            <div class="bl-meta mb-3">
              <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($p['author']) ?>
              <span class="mx-2 opacity-50">•</span>
              <i class="bi bi-clock me-1"></i><?= htmlspecialchars((string)$p['created_at']) ?>
            </div>

            <p class="mb-4"><?= htmlspecialchars(excerpt((string)$p['post_body'])) ?></p>

            <div class="mt-auto d-flex gap-2">
              <a class="btn btn-gradient btn-sm" href="Post_Show.php?post_id=<?= (int)$p['post_id'] ?>" data-spinner="1">
                <i class="bi bi-book me-1"></i>Read
              </a>
              <a class="btn btn-soft btn-sm" href="Post_Show.php?post_id=<?= (int)$p['post_id'] ?>#comments" data-spinner="1">
                <i class="bi bi-chat-left-text me-1"></i>Comments
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <nav aria-label="Posts pagination">
      <ul class="pagination justify-content-center">
        <?php
          $queryBase = $hasSearch ? ('&q=' . urlencode($q)) : '';
          $prev = max(1, $page - 1);
          $next = min($totalPages, $page + 1);
        ?>
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="Posts_List.php?page=<?= $prev ?><?= $queryBase ?>" aria-label="Previous" data-spinner="1">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>

        <?php
          // show a compact range around current page
          $start = max(1, $page - 2);
          $end = min($totalPages, $page + 2);
          for ($i = $start; $i <= $end; $i++):
        ?>
          <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="Posts_List.php?page=<?= $i ?><?= $queryBase ?>" data-spinner="1"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="Posts_List.php?page=<?= $next ?><?= $queryBase ?>" aria-label="Next" data-spinner="1">
            <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php if (existsLoggedUser()): ?>
  <!-- Floating Action Button -->
  <a class="btn btn-gradient bl-fab" href="Post_Create.php" title="New Post" aria-label="New Post" data-spinner="1">
    <i class="bi bi-plus-lg fs-4"></i>
  </a>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
