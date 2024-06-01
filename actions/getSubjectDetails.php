<?php
date_default_timezone_set('Asia/Kolkata');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userid'])) {
    require('conn.php');

    $subjectId = mysqli_real_escape_string($conn, $_POST['id']);

    $subjectQuery = "SELECT * FROM subjects WHERE id = '$subjectId'";
    $subjectResult = mysqli_query($conn, $subjectQuery);

    if ($subjectResult && mysqli_num_rows($subjectResult) > 0) {
        $subjectDetails = mysqli_fetch_assoc($subjectResult);
        $c_id = $subjectDetails['c_id'];
        $f_id = $subjectDetails['f_id'];
        $byid = $subjectDetails['byid'];

        $classQuery = "SELECT session, branch FROM classes WHERE id = '$c_id'";
        $classResult = mysqli_query($conn, $classQuery);
        $classDetails = mysqli_fetch_assoc($classResult);

        $teacherQuery = "SELECT name FROM users WHERE id = '$f_id'";
        $teacherResult = mysqli_query($conn, $teacherQuery);
        $teacherDetails = mysqli_fetch_assoc($teacherResult);
        
        $byQuery = "SELECT name FROM users WHERE id = '$byid'";
        $byResult = mysqli_query($conn, $byQuery);
        $byDetails = mysqli_fetch_assoc($byResult);

        $response = [
            'icon' => 'success',
            'subject' => array_merge(
                $subjectDetails,
                [
                    'session' => $classDetails['session'],
                    'branch' => $classDetails['branch'],
                    'teacher' => $teacherDetails['name'],
                    'byname' => $byDetails['name']
                ]
            ),
            'message' => 'Subject details fetched successfully'
        ];

        echo json_encode($response);
        exit();
    } else {
        echo json_encode(['icon' => 'error', 'message' => 'Subject not found.']);
        exit();
    }
} else {
    echo json_encode(['icon' => 'error', 'message' => 'Invalid request.']);
    exit();
}
?>