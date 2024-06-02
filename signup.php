<?php
session_start();
require 'actions/conn.php';
require_once('actions/vars.php');
if(isset($_SESSION['userid'])){
    if($userSql['role'] == 'student'){
  header('Location: markatt.php');
    }else{
        header('Location: classes.php');
    }
}
$msg = "";
$status = "";
if (isset($_POST['submit'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $role = $_POST['role'];
  $branch = isset($_POST['branch']) ? $_POST['branch'] : '';
  $semester = isset($_POST['semester']) ? $_POST['semester'] : '';
  $session = isset($_POST['session']) ? $_POST['session'] : '';
  $roll = isset($_POST['roll']) ? $_POST['roll'] : '';
  $secret = isset($_POST['secret']) ? $_POST['secret'] : '';
  $pass = $_POST['pass'];
  $pass = password_hash($pass, PASSWORD_DEFAULT);
  
  $sql = "SELECT * FROM users WHERE email='$email'";

  if ($role == "student") {
    if (empty($branch) || empty($semester) || empty($roll) || empty($session) || $secret == '') {
      $msg = "All fields are required!";
      $status = "danger";
    } else {
      $sql = "SELECT * FROM users WHERE email='$email' OR (role='student' AND branch='$branch' AND roll='$roll' AND session='$session')";
      $sql2 = "INSERT INTO users (name, email, role, branch, semester, session, roll, pass) VALUES ('$name', '$email', '$role', '$branch', '$semester', '$session', '$roll', '$pass')";
      $secretArray = $secretCodeStudent;
    }
  } else {
    $sql = "SELECT * FROM users WHERE email='$email'";
    $sql2 = "INSERT INTO users (name, email, role, pass) VALUES ('$name', '$email', '$role', '$pass')";
    $secretArray = $secretCodeFaculty;
  }
  if(in_array($secret, $secretArray)){
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $msg = "Email or User already exists!";
    $status = "danger";
  } else {
    if (!empty($sql2)) {
      if (mysqli_query($conn, $sql2)) {
        $msg = "Account Created! Proceed to <a href='login.php'>Login</a>";
        $status = "success";
      } else {
        $msg = "Error: " . $sql2 . "<br>" . mysqli_error($conn);
        $status = "danger";
      }
    }
  }
  }else{
    $msg = "Wrong secret code for $role registration.";
    $status = "danger";
  }
  
}

?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
<link rel="stylesheet" href="components/libs/font-awesome-pro/css/all.min.css" />
<style>
  .student-dets-div{
  display: none;
}
</style>

</head>

<body>
  <?php include 'nav.php'; ?>
<div class="container-fluid ">
<h1 class="text-center mt-3">Register</h1>
<!--
            name
            email
            branch
            semester
            roll
            pass
         -->
<form action="" method="post" class="m-4 ">
<?php if (!empty($msg)){ ?>
      <div class="alert alert-<?php echo $status; ?>">
        <?php echo $msg; ?>
      </div>
<?php } ?>
<div class="form-floating mb-3">
<input type="text" name="name" class="form-control" id="name" placeholder="" value="<?php echo $name; ?>" required>
<label for="name">Name</label>
</div>
<div class="form-floating mb-3">
<input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo $email; ?>" required>
<label for="email">Email address</label>
</div>
<div class="mb-3">
<select name="role" class="form-select" id="role" aria-label="Role" required>
<option selected value="" disabled>Select Role</option>
<option value="student">Student</option>
<option value="faculty">Faculty</option>
</select>
</div>
<div class="mb-3 student-dets-div ">
<select name="branch" class="form-select student-dets-input" id="branch" aria-label="Branch">
<option selected value="" disabled>Select Branch</option>
<option value="CSE">Computer Science and Engineering (CSE)</option>
<option value="CE">Civil Engineering (CE)</option>
<option value="ECE">Electronics and Communication Engineering (ECE)</option>
<option value="EE">Electrical Engineering (EE)</option>
<option value="ME">Mechanical Engineering (ME)</option>
<option value="AUTOM">Automobile Engineering (AUTOM)</option>
</select>
</div>
<div class="mb-3 student-dets-div ">
<select name="semester" class="form-select student-dets-input" id="semester" value="" aria-label="Semester">
<option selected value="" disabled>Select Semester</option>
<option value="1">1st Semester</option>
<option value="2">2nd Semester</option>
<option value="3">3rd Semester</option>
<option value="4">4th Semester</option>
<option value="5">5th Semester</option>
<option value="6">6th Semester</option>
</select>
</div>
<div class="mb-3 student-dets-div ">
<select name="session" class="form-select student-dets-input" id="session" value="" aria-label="Session">
<option selected value="" disabled>Select Session</option>
<option value="2018-21">2018-21</option>
<option value="2019-22">2019-22</option>
<option value="2020-23">2020-23</option>
<option value="2021-24">2021-24</option>
<option value="2022-25">2022-25</option>
<option value="2023-26">2023-26</option>
<option value="2024-27">2024-27</option>
</select>
</div>
<div class="form-floating mb-3 student-dets-div ">
<input type="number" name="roll" class="form-control student-dets-input" id="roll" placeholder="">
<label for="roll">Roll</label>
</div>

<div class="input-group mb-3">
<input type="password" name="pass" class="form-control" id="pass" placeholder="Password"
aria-label="Password" required>
<label for="pass" class="visually-hidden">Password</label>
<button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass">
<i class="far fa-eye"></i>
</button>
</div>

<div class="input-group mb-3 secret-div d-none">
  <input type="secret" name="secret" class="form-control" id="secret" placeholder="Secret code" aria-label="Secret" required>
</div>

<div class="mt-4 text-center">
<button name="submit" class="btn btn-outline-success register">Register</button>
</div>
<div class="text-center my-3">Already have an account? <a href="login.php" class="link-primary">Log in</a></div>
</form>
</div>

<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script>
  $(document).ready(function () {
    $(".toggle-password").click(function () {
        var targetId = $(this).data("target");
        var passwordField = $("#" + targetId);
        var fieldType = passwordField.attr("type") === "password" ? "text" : "password";
        passwordField.attr("type", fieldType);
        $(this).find("i").toggleClass("fa-eye fa-eye-slash");
    });
});

$("#role").on("change", function() {
   $('.secret-div').removeClass('d-none')
   $('#secret').attr('required', true)
    if ($(this).val() == "student") {
        $('.student-dets-div').css('display', 'block');
        $('.student-dets-input').attr('required', true)
        $('#secret').attr('placeholder', 'Secret code for student registration')
    } else {
        $('.student-dets-div').css('display', 'none');
        $('.student-dets-input').removeAttr('required')
        $('#secret').attr('placeholder', 'Secret code for faculty registration')
    }
});

</script>
</body>

</html>