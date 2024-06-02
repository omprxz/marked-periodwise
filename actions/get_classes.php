<?php
require_once 'conn.php';

$classQuery = "SELECT id, session, branch FROM classes";
$classResult = mysqli_query($conn, $classQuery);

if (mysqli_num_rows($classResult) > 0) {
    while ($row = mysqli_fetch_assoc($classResult)) {
       echo "<li class='list-group-item d-flex justify-content-between align-items-center link-primary' data-id='".$row['id']."'><a href='/class.php?id=".$row['id']."' class='nav-link'>".$row['session']." - ".$row['branch']."</a><span class='text-danger delete-class' data-id='".$row['id']."' style='cursor: pointer;'><i class='fad fa-trash-alt'></i></span></li>";
    }
} else {
    echo "<p class='text-center mt-2'>No classes.</p>";
}
?>