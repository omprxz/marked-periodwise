<?php
session_start();
require 'conn.php';
require('vars.php');

function distance($lat1, $lon1, $lat2, $lon2, $unit) {
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['userid'];

$currentDate = date('Y-m-d');

$userQuery = "SELECT session, branch FROM users WHERE id = '$userId' LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);

if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userData = mysqli_fetch_assoc($userResult);
    $userSession = $userData['session'];
    $userBranch = $userData['branch'];

    $classQuery = "SELECT id FROM classes WHERE session = '$userSession' AND branch = '$userBranch' LIMIT 1";
    $classResult = mysqli_query($conn, $classQuery);

    if ($classResult && mysqli_num_rows($classResult) > 0) {
        $classData = mysqli_fetch_assoc($classResult);
        $classId = $classData['id'];

      $routineQuery = "SELECT r.id, r.fromTime, r.toTime, s.name 
                 FROM routines r 
                 JOIN subjects s ON r.sub_id = s.id 
                 WHERE r.c_id = '$classId' 
                 AND r.day = (DAYOFWEEK(NOW()) - 1)
                 AND TIME(NOW()) BETWEEN r.fromTime AND r.toTime";
        $routineResult = mysqli_query($conn, $routineQuery);

        if ($routineResult && mysqli_num_rows($routineResult) > 0) {
            $routineData = mysqli_fetch_assoc($routineResult);
            $routineId = $routineData['id'];

            if (!isset($_POST['location']) || !isset($_POST['location']['latitude']) || !isset($_POST['location']['longitude'])) {
                echo json_encode(['status' => 'error', 'message' => 'User location not provided']);
                exit();
            }

            $userLat = $_POST['location']['latitude'];
            $userLon = $_POST['location']['longitude'];

            $collegeLat = 25.6305705;
            $collegeLon = 85.1033881;

            $distance = distance($userLat, $userLon, $collegeLat, $collegeLon, 'K') * 1000;

            if ($distance > $attendenceLocationRange) {
                echo json_encode(['status' => 'error', 'message' => 'You are not in the college', 'distance' => $distance/1000]);
                exit();
            }

            $userIP = $_SERVER['REMOTE_ADDR'];

            date_default_timezone_set('Asia/Kolkata');
            $date = date('Y-m-d H:i:s');

            $sql = "INSERT INTO attendance (s_id, r_id, s_lat, s_long, clg_lat, clg_long, ip, date) 
                    VALUES ('$userId', '$routineId', '$userLat', '$userLon', '$collegeLat', '$collegeLon', '$userIP', '$date')";

            if (mysqli_query($conn, $sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Attendance marked']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to mark attendance']);
            }

        } else {
            echo json_encode(['status' => 'error', 'message' => 'No active routine found for current time']);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching class found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User data not found']);
}

mysqli_close($conn);
?>