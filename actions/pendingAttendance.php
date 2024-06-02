<?php
require_once 'conn.php';

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
if($_GET['action'] == 'reject'){
    $delete_query = "UPDATE pendingAttendances SET disapproved = 1 WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        $response = array(
            "icon" => "success",
            "message" => "Attendance rejected."
        );
    } else {
        $response = array(
            "icon" => "error",
            "message" => "Error rejecting: " . mysqli_error($conn)
        );
    }
}
elseif($_GET['action'] == 'approve'){

    $select_query = "SELECT * FROM pendingAttendances WHERE id = '$id'";
    $result = mysqli_query($conn, $select_query);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $s_id = $row['s_id'];
        $r_id = $row['r_id'];
        $s_lat = $row['s_lat'];
        $s_long = $row['s_long'];
        $clg_lat = $row['clg_lat'];
        $clg_long = $row['clg_long'];
        $date = $row['date'];
        $ip = $row['ip'];

        $insert_query = "INSERT INTO attendance (s_id, r_id, s_lat, s_long, clg_lat, clg_long, date, ip) 
                         VALUES ('$s_id', '$r_id', '$s_lat', '$s_long', '$clg_lat', '$clg_long', '$date', '$ip')";

        if(mysqli_query($conn, $insert_query)) {
            $delete_query = "DELETE FROM pendingAttendances WHERE id = '$id'";
            if(mysqli_query($conn, $delete_query)) {
                $response = array(
                    "icon" => "success",
                    "message" => "Attendance approved."
                );
            } else {
                $response = array(
                    "icon" => "success",
                    "message" => "Approved"
                );
            }
        } else {
            $response = array(
                "icon" => "error",
                "message" => "Error approving attendance: " . mysqli_error($conn)
            );
        }
    } else {
        $response = array(
            "icon" => "error",
            "message" => "Pending attendance not found with provided ID."
        );
    }
} else {
    $response = array(
        "icon" => "error",
        "message" => "ID not provided."
    );
}
}
else {
    $response = array(
        "icon" => "error",
        "message" => "ID not provided."
    );
}

mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode($response);
?>