<?php
header('Content-Type: application/json');
require_once('conn.php');

$sql = "SELECT name, session, branch, semester, roll, email, created AS joinedon FROM users where role = 'student'";
$result = $conn->query($sql);

$students = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

echo json_encode($students);
$conn->close();
?>