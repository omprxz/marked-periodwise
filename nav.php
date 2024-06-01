<?php
  require_once 'actions/conn.php';
  session_start();
  if(isset($_SESSION['userid'])){
  $sUserId=$_SESSION['userid'];
  }
?>

<head>
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
          type="text/css" media="all"/>
</head>

<nav class="navbar navbar-expand-lg navbar-light bg-light opacity-75">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/">MarkEd</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(!isset($_SESSION['userid'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/signup.php">Signup</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/about.php">About</a>
          </li>
        <?php else: ?>
          <?php
            $query = "SELECT role FROM users WHERE id = $sUserId";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $role = $row['role'];
          ?>
          <?php if($role == 'student'): ?>
            <li class="nav-item">
              <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/markatt.php">Mark Attendance</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/viewatt.php">View Attendance</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/viewmarks.php">View Marks</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/profile.php">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/logout.php">Logout</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/about.php">About</a>
            </li>
          <?php elseif($role == 'faculty'): ?>
            <li class="nav-item">
              <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/settings.php">Settings</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/profile.php">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/logout.php">Logout</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/about.php">About</a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" type="text/javascript" charset="utf-8"></script>