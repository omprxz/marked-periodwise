<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php';

$class_id = $_GET['id'];

$daysMap = [
    0 => 'Sunday',
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
    7 => 'Sunday'
];

$daysQuery = "SELECT DISTINCT day FROM routines WHERE c_id = '$class_id' ORDER BY day";
$daysResult = mysqli_query($conn, $daysQuery);

if (mysqli_num_rows($daysResult) > 0) {
    while ($dayRow = mysqli_fetch_assoc($daysResult)) {
        $dayInt = $dayRow['day'];
        $dayName = isset($daysMap[$dayInt]) ? $daysMap[$dayInt] : 'Unknown Day';

        echo '<h4 class="fw-semibold mt-3 mb-0">' . $dayName . '</h4>';
        echo '<table class="table table-hover">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">From</th>';
        echo '<th scope="col">To</th>';
        echo '<th scope="col">Subject</th>';
        echo '<th scope="col">Teacher</th>';
        echo '<th scope="col">Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $routineQuery = "SELECT id, fromTime, toTime, sub_id FROM routines WHERE c_id = '$class_id' AND day = '$dayInt' ORDER BY fromTime";
        $routineResult = mysqli_query($conn, $routineQuery);

        if (mysqli_num_rows($routineResult) > 0) {
            while ($row = mysqli_fetch_assoc($routineResult)) {
                $fromTime = date('H:i', strtotime($row['fromTime']));
                $toTime = date('H:i', strtotime($row['toTime']));
                $sub_id = $row['sub_id'];

                $subjectQuery = "SELECT name FROM subjects WHERE id = '$sub_id'";
                $subjectResult = mysqli_query($conn, $subjectQuery);
                $subjectName = "";
                if (mysqli_num_rows($subjectResult) > 0) {
                    $subjectData = mysqli_fetch_assoc($subjectResult);
                    $subjectName = $subjectData['name'];
                }

                $teacherQuery = "SELECT name FROM users WHERE id IN (SELECT f_id FROM subjects WHERE id = '$sub_id')";
                $teacherResult = mysqli_query($conn, $teacherQuery);
                $teacherName = "";
                if (mysqli_num_rows($teacherResult) > 0) {
                    $teacherData = mysqli_fetch_assoc($teacherResult);
                    $teacherName = $teacherData['name'];
                }

                echo '<tr>';
                echo '<td>' . $fromTime . '</td>';
                echo '<td>' . $toTime . '</td>';
                echo '<td>' . $subjectName . '</td>';
                echo '<td>' . $teacherName . '</td>';
                echo '<td>' .  '<span class="d-flex gap-3 justify-content-center">
                <i class="fad fa-pencil-alt edit-routine text-primary" data-id="' . $row['id'] . '" style="cursor: pointer;"></i>
                <i class="fad fa-trash-alt delete-routine text-danger" data-id="' . $row['id'] . '" style="cursor: pointer;"></i>
            </span>' . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">No routines found for this day.</td></tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
} else {
    echo '<div class="alert alert-warning" role="alert">No routines found.</div>';
}

mysqli_close($conn);
?>