<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$sUserId = $_SESSION["userid"];

require "vars.php";
require_once "conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $minAtt = isset($_POST["minAtt"]) && $_POST["minAtt"] !== '' ? (float)$_POST["minAtt"] : 0;
    $maxAtt = isset($_POST["maxAtt"]) && $_POST["maxAtt"] !== '' ? (float)$_POST["maxAtt"] : 100;
    $session = $_POST["session"];
    $branch = $_POST["branch"];
    $marks = (int)$_POST["marks"];
    $subject = $_POST["subject"];
    $resType = $_POST["resType"];
    $ip = $_SERVER['REMOTE_ADDR'];

    if (empty($maxAtt) || empty($session) || empty($branch) || empty($marks) || empty($subject) || empty($resType)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    if ($minAtt > $maxAtt) {
        echo json_encode(['status' => 'error', 'message' => 'Minimum attendance cannot be greater than maximum attendance.']);
        exit();
    }

    $getStudents = "
        SELECT u.id, u.name, u.semester, u.roll
        FROM users u
        INNER JOIN classes c ON c.session = u.session AND c.branch = u.branch
        WHERE u.session = '$session' AND u.branch = '$branch' AND u.role = 'student';
    ";
    $studentResult = mysqli_query($conn, $getStudents);

    if ($studentResult && mysqli_num_rows($studentResult) > 0) {
        $studentIds = [];
        while ($studentData = mysqli_fetch_assoc($studentResult)) {
            $studentId = $studentData["id"];
            $totalPresent = 0;

            $totalPeriods = 0;
            $getRoutines = "SELECT * FROM routines WHERE sub_id = '$subject' AND c_id = (SELECT id FROM classes WHERE session = '$session' AND branch = '$branch') ORDER BY created DESC";
            $routineResult = mysqli_query($conn, $getRoutines);

            if ($routineResult && mysqli_num_rows($routineResult) > 0) {
                $routineData = mysqli_fetch_assoc($routineResult);
                $startDate = new DateTime($routineData['created']);
                $currentDate = new DateTime();
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($startDate, $interval, $currentDate);

                foreach ($period as $date) {
                    if ($date->format('N') != 7) {
                        mysqli_data_seek($routineResult, 0);
                        while ($routineRow = mysqli_fetch_assoc($routineResult)) {
                            if ($date->format('N') == $routineRow['day'] && $routineRow['sub_id'] == $subject) {
                                $totalPeriods++;
                            }
                        }
                    }
                }
            }

            $getAttendance = "SELECT COUNT(*) AS totalPresent FROM attendance a INNER JOIN routines r ON a.r_id = r.id WHERE a.s_id = '$studentId' AND r.sub_id = '$subject' AND r.c_id = (SELECT id FROM classes WHERE session = '$session' AND branch = '$branch')";
            $attendanceResult = mysqli_query($conn, $getAttendance);
            if ($attendanceResult) {
                $attendanceData = mysqli_fetch_assoc($attendanceResult);
                $totalPresent = $attendanceData['totalPresent'];
            }

            $attendancePercentage = ($totalPeriods > 0) ? ($totalPresent / $totalPeriods) * 100 : 0;

            if ($attendancePercentage >= $minAtt && $attendancePercentage <= $maxAtt) {
                $checkMarks = "
                    SELECT * FROM marks 
                    WHERE s_id = '$studentId' AND sub_id = '$subject' AND resT_id = '$resType'
                ";
                $marksResult = mysqli_query($conn, $checkMarks);

                if (mysqli_num_rows($marksResult) > 0) {
                    $updateMarks = "
                        UPDATE marks 
                        SET marks = '$marks', ip = '$ip', f_id = '$sUserId' 
                        WHERE s_id = '$studentId' AND sub_id = '$subject' AND resT_id = '$resType'
                    ";
                    mysqli_query($conn, $updateMarks);
                } else {
                    $addMarks = "
                        INSERT INTO marks (s_id, sub_id, marks, resT_id, ip, f_id)
                        VALUES ('$studentId', '$subject', '$marks', '$resType', '$ip', '$sUserId')
                    ";
                    mysqli_query($conn, $addMarks);
                }

                $studentIds[] = $studentId;
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Marks added/updated successfully.', 'id' => $studentIds]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No students with these details.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>