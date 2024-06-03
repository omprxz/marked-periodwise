<?php
require('conn.php');
$resultTypeQuery = "SELECT id, type FROM result_types";
        $resultTypeResult = mysqli_query($conn, $resultTypeQuery);
        if(mysqli_num_rows($resultTypeResult) > 0){
        while($row = mysqli_fetch_assoc($resultTypeResult)) {
          echo "<li class='list-group-item d-flex justify-content-between align-items-center' data-id='".$row['id']."'>".$row['type']."
          <p class='mb-0 d-flex gap-4'>
            <i class='fad fa-pencil edit' data-id='".$row['id']."'></i>
            <i class='fad fa-trash-alt delete' data-id='".$row['id']."'></i>
          </p>
          </li>";
        }
        }else{
          echo '<div class="alert alert-warning m-4">No result types available.</div>';
        }
?>
