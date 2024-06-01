<?php
header('Content-Type: application/json');
require_once('conn.php');

$classId = $_GET['classId'];

$classInfoQuery = "SELECT session, branch FROM classes WHERE id = '$classId'";
$classInfoResult = $conn->query($classInfoQuery);

if ($classInfoResult->num_rows > 0) {
    $classInfo = $classInfoResult->fetch_assoc();
    $session = $classInfo['session'];
    $branch = $classInfo['branch'];

    $sql = "SELECT name, session, branch, semester, roll, email, created AS joinedon FROM users WHERE role = 'student' AND session = '$session' AND branch = '$branch'";
    $result = $conn->query($sql);

    $students = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }

    echo json_encode($students);
} else {
    echo json_encode(['error' => 'Class not found']);
}

$conn->close();
?>