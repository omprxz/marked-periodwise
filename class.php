<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}
require('actions/conn.php');
 if(isset($_GET['id'])) {
   $issetId=true;
            $classId = $_GET['id'];
            $classQuery = "SELECT session, branch FROM classes WHERE id = '$classId'";
            $classResult = mysqli_query($conn, $classQuery);
            if(mysqli_num_rows($classResult) > 0) {
                $classData = mysqli_fetch_assoc($classResult);
                $session = $classData['session'];
                $branch = $classData['branch'];
            }
 }else{
   $issetId = false;
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>
    <?php
    echo ($issetId) ? ($session.' ('.$branch. ') - Manage Class') : 'Manage Class';
    ?>
  </title>
  <link href="components/libs/font-awesome-pro/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <style>
    *{
      font-family: 'Roboto';
    }
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
         #tables-container { font-size: 0.9rem; }
        .tableAtt td, .tableAtt th { white-space: nowrap; }
        .dataTables_scrollBody td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
        .nav-pills-cust li a{
          padding: 5px 10px;
        }
</style>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>


   <?php include 'nav.php'; ?>
   <?php 
    $roleQ = "SELECT role from users where id = '$sUserId' limit 1";
    $roleE = mysqli_fetch_assoc(mysqli_query($conn, $roleQ));
    $role = $roleE['role'];
    if($role == 'faculty'){
  ?>
<div class="container">
    <?php 
        if($issetId) {
           ?>
    <h1 class="text-center fw-bold my-3"><a href="classes.php" class="nav-link"><?php echo $session . " - " . $branch; ?></a></h1>
    <ul class="nav nav-pills nav-pills-cust justify-content-center mt-1 mb-3 border border-2 border-dark-subtle pt-2 pb-0 rounded">
      
        <li class="nav-item mb-2 border-end">
            <a class="nav-link active studentsPill" data-bs-toggle="pill" href="#students">Students</a>
        </li>
        <li class="nav-item mb-2 border-end">
            <a class="nav-link routinesPill" data-bs-toggle="pill" href="#routines">Routines</a>
        </li>
        <li class="nav-item mb-2 border-end">
            <a class="nav-link subjectsPill" data-bs-toggle="pill" href="#subjects">Subjects</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link attendancePill" data-bs-toggle="pill" href="#attendance">Attendance</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link pendAttendancesPill" data-bs-toggle="pill" href="#pendAttendances">Pending Attendances</a>
        </li>
        
    </ul>

    <div class="tab-content">
      
        <div class="tab-pane fade show active" id="students">
          <?php include 'students_class.php'; ?>
        </div>
        
        <div class="tab-pane fade" id="routines" data-loaded='' >
          <?php include 'routines_class.php'; ?>
        </div>

        <div class="tab-pane fade" id="subjects" data-loaded='' >
          <?php include 'subjects_class.php'; ?>
        </div>
        
        <div class="tab-pane fade" id="attendance" data-loaded='' >
          <?php include 'attendance_class.php'; ?>
        </div>
       
        <div class="tab-pane fade" id="pendAttendances" data-loaded='' >
          <?php include 'pendingAttendances_class.php'; ?>
        </div>

        </div>
    <?php 
        }else {
    ?>
    <div class="alert alert-warning mt-4 mx-2" role="alert">
        Empty class ID. <br />
        Choose one from below.
    </div>
    <ul class="list-group mt-4 mx-2 classes-list">
      <?php
        $classQuery = "SELECT id, session, branch FROM classes";
        $classResult = mysqli_query($conn, $classQuery);
        while($row = mysqli_fetch_assoc($classResult)) {
          echo "<li class='list-group-item d-flex justify-content-between align-items-center' data-id='".$row['id']."'><a style='cursor: pointer;' href='/class.php?id=".$row['id']."' class='nav-link'>".$row['session']." - ".$row['branch']."</a><a style='cursor: pointer;' href='/class.php?id=".$row['id']."'><i class='fad fa-arrow-right'></i></a></li>";
        }
      ?>
    </ul>
    <?php } ?>
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

<script src="/eruda.js"></script>


</body>
</html>