<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else {
    $sUserId = $_SESSION['userid'];
}

require('vars.php');
require_once 'conn.php';

$subjects = $_POST['subjects'] ?? [];

$response = [];

if (empty($subjects)) {
    $response['warning'] = 'No records.';
    exit(json_encode($response, JSON_PRETTY_PRINT));
} elseif (in_array('all', $subjects)) {
    $subjectsList = 'all';
} else {
    $subjectsList = implode(',', array_map('intval', $subjects));
}

$totalPeriods = 0;
$totalAttendanceCount = 0;

$userQuery = "SELECT session, branch FROM users WHERE id = '$sUserId'";
$userResult = mysqli_query($conn, $userQuery);

if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userData = mysqli_fetch_assoc($userResult);
    $userSession = $userData['session'];
    $userBranch = $userData['branch'];

    $classQuery = "SELECT id FROM classes WHERE session = '$userSession' AND branch = '$userBranch'";
    $classResult = mysqli_query($conn, $classQuery);

    if ($classResult && mysqli_num_rows($classResult) > 0) {
        while ($classData = mysqli_fetch_assoc($classResult)) {
            $classId = $classData['id'];

            $subjectCondition = $subjectsList === 'all' ? '' : "AND sub_id IN ($subjectsList)";
            $routineQuery = "SELECT id FROM routines WHERE c_id = '$classId' $subjectCondition";
            $routineResult = mysqli_query($conn, $routineQuery);

            if ($routineResult && mysqli_num_rows($routineResult) > 0) {
                $routineIds = [];
                while ($routineData = mysqli_fetch_assoc($routineResult)) {
                    $routineIds[] = $routineData['id'];
                }

                $totalPeriods += count($routineIds);

                $routineIdsString = implode(',', $routineIds);
                $attendanceQuery = "SELECT COUNT(*) AS attendance_count FROM attendance WHERE r_id IN ($routineIdsString)";
                $attendanceResult = mysqli_query($conn, $attendanceQuery);
                $attendanceData = mysqli_fetch_assoc($attendanceResult);
                $totalAttendanceCount += $attendanceData['attendance_count'];
            }
        }

        if ($totalPeriods > 0) {
            $overallPercentage = ($totalAttendanceCount / $totalPeriods) * 100;
            $totalAbsentDays = $totalPeriods - $totalAttendanceCount;

            $response['totalPeriods'] = $totalPeriods;
            $response['totalAttendanceCount'] = $totalAttendanceCount;
            $response['overallPercentage'] = number_format($overallPercentage, 2);
            $response['totalAbsentDays'] = $totalAbsentDays;
        } else {
            $response['warning'] = 'No attendance data available.';
            exit(json_encode($response, JSON_PRETTY_PRINT)); // Output JSON and exit
        }
    } else {
        $response['warning'] = 'No classes found for the user\'s session and branch.';
        exit(json_encode($response, JSON_PRETTY_PRINT)); // Output JSON and exit
    }
} else {
    $response['warning'] = 'User session or branch not found.';
    exit(json_encode($response, JSON_PRETTY_PRINT)); // Output JSON and exit
}

$subjectCondition = $subjectsList === 'all' ? '' : "AND r.sub_id IN ($subjectsList)";
$attendanceQuery = "SELECT DATE(a.date) AS date, TIME(a.date) AS time, s.name AS subject_name
                    FROM attendance a
                    INNER JOIN routines r ON a.r_id = r.id
                    INNER JOIN subjects s ON r.sub_id = s.id
                    WHERE a.s_id = '$sUserId' $subjectCondition";

$result = mysqli_query($conn, $attendanceQuery);

if ($result && mysqli_num_rows($result) > 0) {
    $attendanceRecords = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $attendanceRecords[] = $row;
    }
    $response['attendanceRecords'] = $attendanceRecords;
} else {
    $response['warning'] = 'No attendance records found.';
}

echo json_encode($response, JSON_PRETTY_PRINT); // Output JSON
?>