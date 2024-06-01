<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}
require('actions/conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Manage Classes</title>
  <link href="components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
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
    <div class="text-center mt-4">
      <button type="button" id="addClassBtn" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Add Class</button>
    </div>
    <ul class="list-group mt-4 mx-2 classes-list">
      <?php
        $classQuery = "SELECT id, session, branch FROM classes";
        $classResult = mysqli_query($conn, $classQuery);
        while($row = mysqli_fetch_assoc($classResult)) {
          echo "<li class='list-group-item d-flex justify-content-between align-items-center' data-id='".$row['id']."'>".$row['session']." - ".$row['branch']."
          <span class='text-danger delete-class' data-id='".$row['id']."' style='cursor: pointer;'><i class='fas fa-trash-alt'></i></span>
          </li>";
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

<script src="eruda.js" type="text/javascript" charset="utf-8"></script>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
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

   $('#addClassBtn').click(function() {
    Swal.fire({
        title: 'Add Class',
        html:
            '<select id="session" class="form-select" required>' +
            '<option selected value="" disabled>Select Session</option>' +
            '<option value="2018-21">2018-21</option>' +
            '<option value="2019-22">2019-22</option>' +
            '<option value="2020-23">2020-23</option>' +
            '<option value="2021-24">2021-24</option>' +
            '<option value="2022-25">2022-25</option>' +
            '<option value="2023-26">2023-26</option>' +
            '<option value="2024-27">2024-27</option>' +
            '</select>' +
            '<select id="branch" class="form-select mt-2" required>' +
            '<option selected value="" disabled>Select Branch</option>' +
            '<option value="CSE">Computer Science and Engineering (CSE)</option>' +
            '<option value="CE">Civil Engineering (CE)</option>' +
            '<option value="ECE">Electronics and Communication Engineering (ECE)</option>' +
            '<option value="EE">Electrical Engineering (EE)</option>' +
            '<option value="ME">Mechanical Engineering (ME)</option>' +
            '<option value="AUTOM">Automobile Engineering (AUTOM)</option>' +
            '</select>',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: () => {
            const session = Swal.getPopup().querySelector('#session').value
            const branch = Swal.getPopup().querySelector('#branch').value
            if (!session || !branch) {
                Swal.showValidationMessage(`Please select session and branch`)
            }
            return { session: session, branch: branch }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'actions/add_class.php',
                data: { session: result.value.session, branch: result.value.branch },
                dataType:'json',
                success: function(response) {
                    Toast.fire({title:response['message'], icon:response['icon']});
                    loadClasses();
                }
            });
        }
    });
});

    $(document).on('click', '.delete-class', function() {
        const classId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'actions/delete_class.php',
                    dataType:'json',
                    data: { id: classId },
                    success: function(response) {
                        Toast.fire({title:response['message'], icon:response['status']});
                        loadClasses();
                    }
                });
            }
        });
    });

    function loadClasses() {
        $.ajax({
            url: 'actions/get_classes.php',
            type: 'GET',
            success: function(response) {
                $('.classes-list').html(response);
            }
        });
    }

    loadClasses();
});
</script>
</body>
</html>