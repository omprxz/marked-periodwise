<h3 class="text-center fw-bold my-2">Pending Attendances</h3>
<div class="pend-students">
<?php
require_once "actions/conn.php";
function distance($lat1, $lon1, $lat2, $lon2, $unit)
{
  if ($lat1 == $lat2 && $lon1 == $lon2) {
    return 0;
  } else {
    $theta = $lon1 - $lon2;
    $dist =
      sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return $miles * 1.609344;
    } elseif ($unit == "N") {
      return $miles * 0.8684;
    } else {
      return $miles;
    }
  }
}

$classId = $_GET["id"];

$class_query = "
    SELECT session, branch
    FROM classes
    WHERE id = $classId;
";

$class_result = $conn->query($class_query);

if ($class_result->num_rows > 0) {
  $class_row = $class_result->fetch_assoc();
  $session = $class_row["session"];
  $branch = $class_row["branch"];

  $distinct_dates_query = "
        SELECT DISTINCT p.date
        FROM pendingAttendances p
        INNER JOIN routines r ON p.r_id = r.id
        INNER JOIN subjects s ON r.sub_id = s.id
        INNER JOIN users u ON p.s_id = u.id
        WHERE p.disapproved = 0 AND u.session = '$session' AND u.branch = '$branch'
        ORDER BY p.date DESC;
    ";

  $distinct_dates_result = $conn->query($distinct_dates_query);

  if ($distinct_dates_result->num_rows > 0) {
    while ($date_row = $distinct_dates_result->fetch_assoc()) {
      $date = $date_row["date"];

      $distinct_subjects_query = "
                SELECT DISTINCT r.sub_id, s.name AS sub_name
                FROM pendingAttendances p
                INNER JOIN routines r ON p.r_id = r.id
                INNER JOIN subjects s ON r.sub_id = s.id
                INNER JOIN users u ON p.s_id = u.id
                WHERE p.disapproved = 0 AND u.session = '$session' AND u.branch = '$branch' AND p.date = '$date';
            ";

      $distinct_subjects_result = $conn->query($distinct_subjects_query);

      if ($distinct_subjects_result->num_rows > 0) {
        echo '<div class="pend-date-div text-center">';
        echo '<div class="sticky-top bg-dark rounded-bottom text-white mb-2 py-1">';
        echo '<p class="text-center mb-0">' .
          date("d F Y", strtotime($date)) .
          "</p>";
        echo "</div>";

        while ($subject_row = $distinct_subjects_result->fetch_assoc()) {
          $sub_id = $subject_row["sub_id"];
          $sub_name = $subject_row["sub_name"];

          echo '<div class="pend-subject-div text-center mb-3 d-flex flex-column align-items-center">';
          echo '<h6 class="text-center fw-bold">' . $sub_name . "</h6>";

        $pending_attendance_query = "
    SELECT pa.*, u.name, u.roll
    FROM pendingAttendances pa
    INNER JOIN users u ON pa.s_id = u.id
    INNER JOIN routines r ON pa.r_id = r.id
    WHERE pa.disapproved = 0 
    AND pa.date = '$date'
    AND u.session = '$session' 
    AND u.branch = '$branch'
    AND r.sub_id = '$sub_id';
";

          $pending_attendance_result = $conn->query($pending_attendance_query);

          if ($pending_attendance_result->num_rows > 0) {
            while ($pa_row = $pending_attendance_result->fetch_assoc()) {
              echo '<div class="card pend-card mb-3 text-start card-' . $pa_row["id"] . '" style="width:17rem;">';
              echo '<div class="card-body">';
              echo '<ul class="list-group">';
              echo '<li class="list-group-item">Name: ' .
                $pa_row["name"] .
                "</li>";
              echo '<li class="list-group-item">Roll: ' .
                $pa_row["roll"] .
                "</li>";
              echo '<li class="list-group-item">Routine: ' .
                date("H:i", strtotime($pa_row["date"])) .
                "</li>";
              echo '<li class="list-group-item">Subject: ' .
                $sub_name .
                "</li>";
              echo '<li class="list-group-item">Distance: ' . round(distance($pa_row["s_lat"], $pa_row["s_long"], $pa_row["clg_lat"], $pa_row["clg_long"], 'K') * 1000) .
                " meters</li>";
              echo "</ul>";
              echo "</div>";
              echo '<div class="card-footer">';
              echo '<div class="pend-actions d-flex justify-content-center gap-4">';
              echo '<button class="btn btn-danger reject" data-id="'.$pa_row["id"].'"> <i class="fal fa-times"></i> Reject</button>';
              echo '<button class="btn btn-success approve" data-id="'.$pa_row["id"].'"> <i class="fal fa-check"></i> Approve</button>';
              echo "</div>";
              echo "</div>";
              echo "</div>";
            }
          } else {
            echo "<p>No pending attendance for this subject and date.</p>";
          }
          echo "</div>";
        }
        echo "</div>";
      }
    }
  } else {
    echo '<div class="alert alert-warning text-center">No pending attendance.</div>';
  }
} else {
  echo '<div class="alert alert-warning text-center">Class not found.</div>';
}

$conn->close();

?>
</div>

<script>
  $(document).ready(function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    $(document).on('click', '.reject', function() {
        var id = $(this).data('id');
        console.log(id)
        Swal.fire({
            title: 'Reject Attendance',
            text: 'Are you sure you want to reject this attendance?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reject it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'GET',
                    url: 'actions/pendingAttendance.php',
                    data: { id: id, action: 'reject' },
                    dataType: 'json',
                    success: function(response) {
                        Toast.fire({
                            icon: response.icon,
                            title: response.message
                        });
                        if(response.icon == 'success'){
                $('.card-'+id).remove()
                
                let subDivs = $('.pend-subject-div')
                subDivs.each(function() {
                    if ($(this).find('.pend-card').length == 0) {
                        $(this).remove();
                    }
                });
                
                let dateDivs = $('.pend-date-div')
                dateDivs.each(function() {
                    if ($(this).find('.pend-card').length == 0) {
                        $(this).remove();
                    }
                });
                  
                if($('.pend-students').find('.pend-date-div').length == 0){
                  $('.pend-students').html('<div class="alert alert-warning text-center">No pending attendance left.</div>')
                }
                
                        }
                    },
                    error: function(xhr, status, error) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error rejecting attendance: ' + error
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.approve', function() {
    var id = $(this).data('id');
    console.log(id)
    $.ajax({
        type: 'GET',
        url: 'actions/pendingAttendance.php',
        data: { id: id, action: 'approve' },
        dataType: 'json',
        success: function(response) {
            Toast.fire({
                icon: response.icon,
                title: response.message
            });
            if(response.icon == 'success'){
                 $('.card-'+id).remove()
                
                let subDivs = $('.pend-subject-div')
                subDivs.each(function() {
                    if ($(this).find('.pend-card').length == 0) {
                        $(this).remove();
                    }
                });
                
                let dateDivs = $('.pend-date-div')
                dateDivs.each(function() {
                    if ($(this).find('.pend-card').length == 0) {
                        $(this).remove();
                    }
                });
                  
                if($('.pend-students').find('.pend-date-div').length == 0){
                  $('.pend-students').html('<div class="alert alert-warning text-center">No pending attendance left.</div>')
                }
               
            }
        },
        error: function(xhr, status, error) {
            Toast.fire({
                icon: 'error',
                title: 'Error approving attendance: ' + error
            });
        }
    });
});
});
</script>