<?php
// app/Views/partials/navbar.php
// TODO: show links based on auth state.
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="Posts_List.php">
      <img src="/DATALaboBlog/public/assets/images/tux.png" alt="Linux" width="24" height="24">
      <strong>DATALabo</strong>
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="Posts_List.php">Posts</a></li>
        <li class="nav-item"><a class="nav-link" href="User_Login.php">Login</a></li>
        <!-- TODO: dashboard/admin links when logged in -->
      </ul>
    </div>
  </div>
</nav>
