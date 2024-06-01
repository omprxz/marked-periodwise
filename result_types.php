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
  <title>Manage Result Types</title>
  <link href="/components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
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
      <button type="button" id="addResultTypeBtn" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Add Result Type</button>
    </div>
    <ul class="list-group mt-4 mx-2 result-types-list">
      <?php
        $resultTypeQuery = "SELECT id, type FROM result_types";
        $resultTypeResult = mysqli_query($conn, $resultTypeQuery);
        while($row = mysqli_fetch_assoc($resultTypeResult)) {
          echo "<li class='list-group-item d-flex justify-content-between align-items-center' data-id='".$row['id']."'>".$row['type']."
          <p class='mb-0 d-flex gap-4'>
            <i class='fas fa-pencil edit' data-id='".$row['id']."'></i>
            <i class='fas fa-trash-alt delete' data-id='".$row['id']."'></i>
          </p>
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
    function loadResultTypes() {
        $.ajax({
            url: 'actions/getResultTypes.php',
            type: 'GET',
            success: function(response) {
                $('.result-types-list').html(response);
            }
        });
    }

    $('#addResultTypeBtn').click(function() {
        Swal.fire({
            title: 'Add Result Type',
            input: 'text',
            inputLabel: 'Result Type Name',
            showCancelButton: true,
            confirmButtonText: 'Add',
            preConfirm: (resultTypeName) => {
                if (!resultTypeName) {
                    Swal.showValidationMessage('Result type name is required');
                }
                return resultTypeName;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'actions/addResultType.php',
                    type: 'POST',
            dataType:'json',
                    data: { result_type: result.value },
                    success: function(response) {
                       Toast.fire({title:response['message'], icon:response['status']});
                        loadResultTypes();
                    }
                });
            }
        });
    });

    $(document).on('click', '.edit', function() {
        const resultTypeId = $(this).data('id');
        const resultTypeName = $(this).closest('li').text().trim();
        Swal.fire({
            title: 'Edit Result Type',
            input: 'text',
            inputValue: resultTypeName,
            showCancelButton: true,
            confirmButtonText: 'Update',
            preConfirm: (newResultTypeName) => {
                if (!newResultTypeName) {
                    Swal.showValidationMessage('Result type name is required');
                }
                return newResultTypeName;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'actions/updateResultType.php',
                    type: 'POST',
            dataType:'json',
                    data: { id: resultTypeId, result_type: result.value },
                    success: function(response) {
                        Toast.fire({title:response['message'], icon:response['status']});
                        loadResultTypes();
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete', function() {
        const resultTypeId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'actions/deleteResultType.php',
                    type: 'POST',
                    dataType:'json',
                    data: { id: resultTypeId },
                    success: function(response) {
                        Toast.fire({title:response['message'], icon:response['status']});
                        loadResultTypes();
                    }
                });
            }
        });
    });

    loadResultTypes();
});
</script>
</body>
</html>