<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userid'])) {
    require('conn.php');

    $routineId = mysqli_real_escape_string($conn, $_POST['routineId']);
    $day = mysqli_real_escape_string($conn, $_POST['day']);
    $fromTime = mysqli_real_escape_string($conn, $_POST['fromTime']);
    $toTime = mysqli_real_escape_string($conn, $_POST['toTime']);
    $subjectId = mysqli_real_escape_string($conn, $_POST['subjectId']);
    $classId = mysqli_real_escape_string($conn, $_POST['classId']);

    if (empty($routineId) || empty($day) || empty($fromTime) || empty($toTime) || empty($subjectId)) {
        echo json_encode(['icon' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    $byid = $_SESSION['userid'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $hpwQuery = "SELECT hpw FROM subjects WHERE id = '$subjectId'";
    $hpwResult = mysqli_query($conn, $hpwQuery);

    if (mysqli_num_rows($hpwResult) > 0) {
        $hpwData = mysqli_fetch_assoc($hpwResult);
        $hpw = $hpwData['hpw'];

        $fromTimeInSeconds = strtotime($fromTime);
        $toTimeInSeconds = strtotime($toTime);
        $newRoutineDuration = abs(($toTimeInSeconds - $fromTimeInSeconds) / 3600);

$routineDurationQuery = "SELECT fromTime, toTime FROM routines WHERE id != '$routineId' AND sub_id = '$subjectId'";
        $routineDurationResult = mysqli_query($conn, $routineDurationQuery);

        $totalRoutineDuration = 0;
        if(mysqli_num_rows($routineDurationResult)>0){
        while ($routineRow = mysqli_fetch_assoc($routineDurationResult)) {
            $existingFromTimeInSeconds = strtotime($routineRow['fromTime']);
            $existingToTimeInSeconds = strtotime($routineRow['toTime']);
            $existingRoutineDuration = abs(($existingToTimeInSeconds - $existingFromTimeInSeconds) / 3600);
            $totalRoutineDuration += $existingRoutineDuration;
        }
}
        if ($totalRoutineDuration + $newRoutineDuration > $hpw) {
            echo json_encode(['icon' => 'error', 'message' => 'Adding this routine exceeds the subject\'s hours per week limit.']);
            exit();
        }
    } else {
        echo json_encode(['icon' => 'error', 'message' => 'Subject not found.']);
        exit();
    }

$timeSlotCheckQuery = "SELECT * FROM routines WHERE id != '$routineId' AND day = '$day' AND c_id = '$classId' AND ((fromTime <= '$fromTime' AND toTime > '$fromTime') OR (fromTime < '$toTime' AND toTime >= '$toTime') OR (fromTime >= '$fromTime' AND toTime <= '$toTime'))";
    $timeSlotCheckResult = mysqli_query($conn, $timeSlotCheckQuery);

    if (mysqli_num_rows($timeSlotCheckResult) > 0) {
        echo json_encode(['icon' => 'error', 'message' => 'The selected time slot is already occupied.']);
        exit();
    }

    $updateQuery = "UPDATE routines SET day = '$day', fromTime = '$fromTime', toTime = '$toTime', sub_id = '$subjectId', c_id = '$classId', byid = '$byid', ip = '$ip', updated = '$timestamp' WHERE id = '$routineId'";

    if (mysqli_query($conn, $updateQuery)) {
        echo json_encode(['icon' => 'success', 'message' => 'Routine updated successfully.']);
        exit();
    } else {
        echo json_encode(['icon' => 'error', 'message' => 'Failed to update routine. Please try again.']);
        exit();
    }
} else {
    echo json_encode(['icon' => 'error', 'message' => 'Invalid request.']);
    exit();
}
?>