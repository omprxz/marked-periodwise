<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userid'])) {
    require('conn.php');

    $routineId = mysqli_real_escape_string($conn, $_POST['routineId']);

    $query = "SELECT r.*, s.name, s.code FROM routines r INNER JOIN subjects s ON r.sub_id = s.id WHERE r.id = '$routineId'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $routineDetails = mysqli_fetch_assoc($result);
        
        $subjectDetails = [
            'id' => $routineDetails['sub_id'],
            'name' => $routineDetails['name'],
            'code' => $routineDetails['code']
        ];

        $combinedDetails = [
            'day' => $routineDetails['day'],
            'fromTime' => $routineDetails['fromTime'],
            'toTime' => $routineDetails['toTime'],
            'subject' => $subjectDetails
        ];

        echo json_encode(['status' => 'success', 'routine' => $combinedDetails]);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Routine not found.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}
?>