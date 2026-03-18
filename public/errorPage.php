<?php
require_once __DIR__ . '/../functions/genericFunctions.php';
startSession();

$pageTitle = 'Error • DATA Labo';
require __DIR__ . '/_header.php';
?>

<div class="card p-4">
  <h1 class="h4">Something went wrong</h1>
  <p class="text-muted mb-0">
    Please go back and try again.
  </p>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
