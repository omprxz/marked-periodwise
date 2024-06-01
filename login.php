<?php
session_start();
require 'actions/conn.php';
if(isset($_SESSION['userid'])){
    if($userSql['role'] == 'student'){
  header('Location: markatt.php');
    }else{
        header('Location: allattendance.php');
    }
}
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";
$status = isset($_GET['status']) ? $_GET['status'] : "";

if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $pass = $_POST['pass'];

  $sql = "SELECT * FROM users WHERE email='$email'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    if (password_verify($pass, $user['pass'])) {
      $_SESSION['userid'] = $user['id'];
      $msg = "Login successful!";
      $status = "success";
      if($user['role']=='student'){
      header("Location: markatt.php");
    }else{
        header("Location: allattendance.php");
    }
    } else {
      $msg = "Invalid password!";
      $status = "danger";
    }
  } else {
    $msg = "User not found!";
    $status = "danger";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="components/login.css">
</head>
<body>
  <?php include 'nav.php'; ?>
<div class="container-fluid">
  <h1 class="text-center mt-3">Login</h1>
  <form action="" method="post" class="m-4">
    <?php if (!empty($msg)){ ?>
      <div class="alert alert-<?php echo $status; ?>">
        <?php echo $msg; ?>
      </div>
    <?php } ?>
    <div class="form-floating mb-3">
      <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo $email; ?>" required>
      <label for="email">Email address</label>
    </div>
    <div class="input-group mb-3">
      <input type="password" name="pass" class="form-control" id="pass" placeholder="Password" aria-label="Password" required>
      <label for="pass" class="visually-hidden">Password</label>
      <button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass">
        <i class="fas fa-eye"></i>
      </button>
    </div>
    <div class="text-center my-3">Forgot password? <a href="forgot.php" class="link-primary">Reset here</a></div>
    <div class="mt-4 text-center">
      <button name="submit" class="btn btn-outline-success login">Login</button>
    </div>
    <div class="text-center my-3">Don't have an account? <a href="signup.php" class="link-primary">Create One</a></div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script>
  $(document).ready(function() {
    $(".toggle-password").click(function() {
      var input = $("#" + $(this).data("target"));
      var icon = $(this).find("i");
      if (input.attr("type") === "password") {
        input.attr("type", "text");
        icon.removeClass("fa-eye").addClass("fa-eye-slash");
      } else {
        input.attr("type", "password");
        icon.removeClass("fa-eye-slash").addClass("fa-eye");
      }
    });
  });
</script>
</body>
</html>