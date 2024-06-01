<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userid'])) {
    require('conn.php');

    $day = mysqli_real_escape_string($conn, $_POST['day']);
    $fromTime = mysqli_real_escape_string($conn, $_POST['fromTime']);
    $toTime = mysqli_real_escape_string($conn, $_POST['toTime']);
    $subjectId = mysqli_real_escape_string($conn, $_POST['subjectId']);
    $classId = mysqli_real_escape_string($conn, $_POST['classId']);

    if (empty($day) || empty($fromTime) || empty($toTime) || empty($subjectId)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    $byid = $_SESSION['userid'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $duplicateCheckQuery = "SELECT * FROM routines WHERE day = '$day' AND fromTime = '$fromTime' AND toTime = '$toTime' AND sub_id = '$subjectId' AND c_id = '$classId'";
    $duplicateCheckResult = mysqli_query($conn, $duplicateCheckQuery);

    if (mysqli_num_rows($duplicateCheckResult) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This routine already exists.']);
        exit();
    }

    $hpwQuery = "SELECT hpw FROM subjects WHERE id = '$subjectId'";
    $hpwResult = mysqli_query($conn, $hpwQuery);

    if (mysqli_num_rows($hpwResult) > 0) {
        $hpwData = mysqli_fetch_assoc($hpwResult);
        $hpw = $hpwData['hpw'];

        $fromTimeInSeconds = strtotime($fromTime);
        $toTimeInSeconds = strtotime($toTime);
        $newRoutineDuration = abs(($toTimeInSeconds - $fromTimeInSeconds) / 3600);

        $routineDurationQuery = "SELECT fromTime, toTime FROM routines WHERE sub_id = '$subjectId'";
        $routineDurationResult = mysqli_query($conn, $routineDurationQuery);

        $totalRoutineDuration = 0;
        while ($routineRow = mysqli_fetch_assoc($routineDurationResult)) {
            $existingFromTimeInSeconds = strtotime($routineRow['fromTime']);
            $existingToTimeInSeconds = strtotime($routineRow['toTime']);
            $existingRoutineDuration = abs(($existingToTimeInSeconds - $existingFromTimeInSeconds) / 3600);
            $totalRoutineDuration += $existingRoutineDuration;
        }

        if ($totalRoutineDuration + $newRoutineDuration > $hpw) {
            echo json_encode(['status' => 'error', 'message' => 'Adding this routine exceeds the subject\'s hours per week limit.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Subject not found.']);
        exit();
    }

    $timeSlotCheckQuery = "SELECT * FROM routines WHERE day = '$day' AND c_id = '$classId' AND ((fromTime <= '$fromTime' AND toTime > '$fromTime') OR (fromTime < '$toTime' AND toTime >= '$toTime') OR (fromTime >= '$fromTime' AND toTime <= '$toTime'))";
    $timeSlotCheckResult = mysqli_query($conn, $timeSlotCheckQuery);

    if (mysqli_num_rows($timeSlotCheckResult) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'The selected time slot is already occupied.']);
        exit();
    }

    $insertQuery = "INSERT INTO routines (day, fromTime, toTime, sub_id, c_id, byid, ip) VALUES ('$day', '$fromTime', '$toTime', '$subjectId', '$classId', '$byid', '$ip')";

    if (mysqli_query($conn, $insertQuery)) {
        echo json_encode(['status' => 'success', 'message' => 'Routine added successfully.']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add routine. Please try again.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}
?>