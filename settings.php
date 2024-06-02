<?php
session_start();
if (!isset($_SESSION["userid"])) {
  header("Location: login.php");
  exit();
} else {
  $sUserId = $_SESSION["userid"];
}
require_once('actions/conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Settings</title>
</head>
<body>
  <?php include "nav.php"; ?>
  <?php
  $roleQ = "SELECT role FROM users WHERE id = '$sUserId' LIMIT 1";
  $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
  $role = $roleE["role"];

  if ($role == "faculty") {
    $pages = [
      ["path" => "/classes.php", "title" => "Manage Classes"],
      ["path" => "/result_types.php", "title" => "Manage Result Types"],
      ["path" => "/allstudents.php", "title" => "All Students Details"],
      ["path" => "/allmarks.php", "title" => "View Marks"],
      ["path" => "/addmarks.php", "title" => "Add Marks"],
      ["path" => "/result_types.php", "title" => "Manage Result Types"]
    ];
    ?>
    <div class="container">
      <h1 class="text-center fw-bold my-2">Settings</h1>
      <ul class="list-group">
        <?php
        foreach ($pages as $page) {
          echo '<li class="list-group-item list-group-item-action"><a href="' . $page['path'] . '" class="nav-link">' . $page['title'] . '</a></li>';
        }
        ?>
      </ul>
    </div>
    <?php
  } else {
    ?>
    <div class="alert alert-danger m-4">
      This page is only for faculties.
    </div>
    <?php
  }
  ?>

  <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>