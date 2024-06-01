<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            overflow-x: auto;
            font-size: 0.9rem;
        }
        .dataTables_wrapper .dataTables_scroll {
            width: 100% !important;
        }
        .dataTables_length{
          margin-top: 10px;
          margin-bottom: 5px;
          text-align: center;
        }
        .dataTables_filter{
          margin: 10px auto;
        }
    </style>
</head>
<body>
<?php include('nav.php'); ?>
<?php 
  $roleQ = "SELECT role FROM users WHERE id = '$sUserId' LIMIT 1";
  $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
  $role = $roleE['role'];
  if($role != 'faculty'){ ?>
    <div class="alert alert-danger m-4">
      This page is only for faculties.
    </div>
  <?php } else { ?>
<div class="container">
    <h2 class="mt-3 mb-2 fw-bold text-center">Student Details</h2>
    <div class="table-responsive mb-2">
        <table id="studentsTable" class="table table-hover table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Session</th>
                    <th>Branch</th>
                    <th>Semester</th>
                    <th>Roll</th>
                    <th>Email</th>
                    <th>Joined On</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $.ajax({
        url: "actions/fetch_students.php",
        type: "GET",
        dataType: "json",
        success: function(data) {
            $('#studentsTable').DataTable({
                data: data,
                columns: [
                    { "data": "name" },
                    { "data": "session" },
                    { "data": "branch" },
                    { "data": "semester" },
                    { "data": "roll" },
                    { "data": "email" },
                    { "data": "joinedon" }
                ],
                scrollX: true
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error fetching data: ", textStatus, errorThrown);
        }
    });
});
</script>
</body>
</html>