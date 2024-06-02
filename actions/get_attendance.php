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

if (empty($subjects)) {
    echo '<div class="alert alert-warning" role="alert">No records.</div>';
    exit();
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
            $routineQuery = "SELECT id, day FROM routines WHERE c_id = '$classId' $subjectCondition";
            $routineResult = mysqli_query($conn, $routineQuery);

            if ($routineResult && mysqli_num_rows($routineResult) > 0) {
                while ($routineData = mysqli_fetch_assoc($routineResult)) {
                    $routineId = $routineData['id'];
                    $routineDay = $routineData['day'];
                    if ($routineDay != 0) {
                        $routineEndDate = date('Y-m-d');
                        $routineStartDate = date('Y-m-d', strtotime("-6 days"));

                        $attendanceQuery = "SELECT COUNT(*) AS attendance_count FROM attendance WHERE r_id = '$routineId'  AND DATE(date) BETWEEN '$routineStartDate' AND '$routineEndDate'";
                        $attendanceResult = mysqli_query($conn, $attendanceQuery);
                        $attendanceData = mysqli_fetch_assoc($attendanceResult);
                        $totalAttendanceCount += $attendanceData['attendance_count'];
                        $totalPeriods++;
                    }
                }
            }
        }

        if ($totalPeriods > 0) {
            $overallPercentage = ($totalAttendanceCount / $totalPeriods) * 100;
            $totalAbsentDays = $totalPeriods - $totalAttendanceCount;

            echo '<div class="text-center mb-2">';
            echo '<p class="badge bg-dark p-2">Total Periods: ' . $totalPeriods . '</p>';
            echo '<div class="d-flex justify-content-around gap-3">';
            echo '<div class="flex-fill badge bg-primary p-2">Present: ' . $totalAttendanceCount . '</div>';
            echo '<div class="flex-fill badge bg-success p-2">Percentage: ' . number_format($overallPercentage, 2) . '%</div>';
            echo '<div class="flex-fill badge bg-danger p-2">Absent: ' . $totalAbsentDays . '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning" role="alert">No attendance data available.</div>';
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">No classes found for the user\'s session and branch.</div>';
    }
} else {
    echo '<div class="alert alert-warning" role="alert">User session or branch not found.</div>';
}

$subjectCondition = $subjectsList === 'all' ? '' : "AND r.sub_id IN ($subjectsList)";
$attendanceQuery = "SELECT DATE(a.date) AS date, TIME(a.date) AS time, s.name AS subject_name
                    FROM attendance a
                    INNER JOIN routines r ON a.r_id = r.id
                    INNER JOIN subjects s ON r.sub_id = s.id
                    WHERE a.s_id = '$sUserId' $subjectCondition";

$result = mysqli_query($conn, $attendanceQuery);

if ($result && mysqli_num_rows($result) > 0) {
    echo '<table class="table table-hover text-center">';
    echo '<thead><tr><th>Date</th><th>Time</th><th>Subject Name</th></thead>';
    echo '<tbody>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['date'] . '</td>';
        echo '<td>' . $row['time'] . '</td>';
        echo '<td>' . $row['subject_name'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-warning" role="alert">No attendance records found.</div>';
}
?>