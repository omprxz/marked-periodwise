<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Add Marks</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <?php 
  $roleQ = "SELECT role from users where id = '$sUserId' limit 1";
  $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
  $role = $roleE['role'];
  if($role == 'faculty'){
  ?>
  <div class="container">
    <h2 class="text-center my-3">Add Marks</h2>
    <form action="actions/addMarksAction.php" method="post" class="my-3 mx-2" id="marksForm">
      <div class="input-group mb-3">
        <span class="input-group-text">Attendance Range</span>
        <input name="minAtt" type="number" class="form-control min-att" aria-label="Mininum attendance" placeholder="Mininum">
        <input name="maxAtt" type="number" class="form-control max-att" aria-label="Maximum attendance" placeholder="Maximum">
      </div>
      <div class="form-floating mb-4">
        <select name="session[]" multiple class="form-select session" aria-label="Session">
          <?php
            $sessionQuery = "SELECT DISTINCT session FROM users where role = 'student';";
            $sessionResult = mysqli_query($conn, $sessionQuery);
            while($row = mysqli_fetch_assoc($sessionResult)) {
              echo "<option value='".$row['session']."'>".$row['session']."</option>";
            }
          ?>
        </select>
        <label for="session">Session</label>
      </div>
      <div class="form-floating mb-4">
        <select name="branch[]" multiple class="form-select branch" aria-label="Branch">
          <?php
            $sessionQuery = "SELECT DISTINCT branch FROM users where role = 'student';";
            $sessionResult = mysqli_query($conn, $sessionQuery);
            while($row = mysqli_fetch_assoc($sessionResult)) {
              echo "<option value='".$row['branch']."'>".$row['branch']."</option>";
            }
          ?>
        </select>
        <label for="branch">Branch</label>
      </div>
      <div class="form-floating mb-4">
        <select name="semester[]" multiple class="form-select semester" aria-label="Semester">
          <?php
            $sessionQuery = "SELECT DISTINCT semester FROM users where role = 'student';";
            $sessionResult = mysqli_query($conn, $sessionQuery);
            while($row = mysqli_fetch_assoc($sessionResult)) {
              echo "<option value='".$row['semester']."'>".$row['semester']."</option>";
            }
          ?>
        </select>
        <label for="semester">Semester</label>
      </div>
      <div class="form-floating mb-3">
        <input name="marks" type="number" class="form-control marks" id="floatingMarks" placeholder="Marks">
        <label for="floatingMarks">Marks</label>
      </div>
      <div class="form-floating mb-3">
        <select name="subjects[]" class="form-select subjects" aria-label="Subjects" multiple>
          <?php
            $subjectQuery = "SELECT id, subject FROM subjects";
            $subjectResult = mysqli_query($conn, $subjectQuery);
            while($row = mysqli_fetch_assoc($subjectResult)) {
              echo "<option value='".$row['id']."'>".$row['subject']."</option>";
            }
          ?>
        </select>
        <label for="subject">Subjects</label>
      </div>
      <div class="form-floating mb-4">
        <select name="resType" class="form-select res-type" aria-label="Result Type">
          <option value="" selected disabled>Select Result Type</option>
          <?php
            $resultTypeQuery = "SELECT id, type FROM result_types";
            $resultTypeResult = mysqli_query($conn, $resultTypeQuery);
            while($row = mysqli_fetch_assoc($resultTypeResult)) {
              echo "<option value='".$row['id']."'>".$row['type']."</option>";
            }
          ?>
        </select>
        <label for="resType">Result Type</label>
      </div>
      <div class="text-center">
        <button type="button" class="btn btn-outline-primary add">Add Marks</button>
      </div>
      
    </form>
  </div>
  <?php
}else{
?>
<div class="alert alert-danger m-4">
  This page is only for faculties.
</div>
<?php
}
?>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter',
      Swal.stopTimer)
    toast.addEventListener('mouseleave',
      Swal.resumeTimer)
  }
})

$('.add').click(function () {
  const minAtt = $('.min-att').val();
  const maxAtt = $('.max-att').val();
  const session = $('.session').val();
  const marks = $('.marks').val();
  const subjects = $('.subjects').val();
  const resType = $('.res-type').val();

  if (minAtt === '') {
    Toast.fire({
      icon: 'error',
      title: 'Minimum attendance is required'
    });
    return;
  }

  if (maxAtt === '') {
    Toast.fire({
      icon: 'error',
      title: 'Maximum attendance is required'
    });
    return;
  }
  
  if (session === null || session.length === 0) {
    Toast.fire({
      icon: 'error',
      title: 'At least one session is required'
    });
    return;
  }

  if (marks === '') {
    Toast.fire({
      icon: 'error',
      title: 'Marks is required'
    });
    return;
  }

  if (subjects === null || subjects.length === 0) {
    Toast.fire({
      icon: 'error',
      title: 'At least one subject must be selected'
    });
    return;
  }

  if (resType === null) {
    Toast.fire({
      icon: 'error',
      title: 'Result type is required'
    });
    return;
  }

  $.ajax({
    type: "POST",
    url: "actions/addMarksAction.php",
    dataType: "json",
    data: $("#marksForm").serialize(),
    beforeSend: function() {
      $('.add').prop('disabled', true);
      $('.add').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding Marks...');
    },
    success: function(response) {
      console.log(response);
      $('.add').prop('disabled', false);
      $('.add').text('Add Marks');
      Toast.fire({
        icon: response['status'],
        title: response.message
      });
      if(response['status']){
        $('#marksForm')[0].reset()
      }
    },
    error: function(xhr, status, error) {
      console.error(xhr.responseText);
      $('.add').prop('disabled', false);
      $('.add').text('Add Marks');
      Toast.fire({
        icon: 'error',
        title: 'An error occurred. Please try again.'
      });
    }
  });
});

</script>
<script src="eruda.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>