<?php
session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="home.php">MySite</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item me-2">
            <span class="nav-link text-white fw-bold">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-light" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item me-2">
            <a class="btn btn-outline-light" href="login.php">Sign In</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-light text-primary fw-bold" href="signup.php">Sign Up</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
