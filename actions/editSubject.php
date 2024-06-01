<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userid'])) {
    require('conn.php');

    $subjectId = mysqli_real_escape_string($conn, $_POST['id']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $classId = mysqli_real_escape_string($conn, $_POST['class_id']);
    $teacherId = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $hpw = mysqli_real_escape_string($conn, $_POST['hpw']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    $checkQuery = "SELECT * FROM subjects WHERE id != '$subjectId' AND (code = '$code' AND name = '$name' AND c_id = '$classId' AND f_id = '$teacherId' AND type = '$type')";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['icon' => 'error', 'message' => 'Subjevt details already exists for another subject.']);
        exit();
    }

    $updateQuery = "UPDATE subjects SET code = '$code', name = '$name', c_id = '$classId', f_id = '$teacherId', hpw = '$hpw', type = '$type' WHERE id = '$subjectId'";

    if (mysqli_query($conn, $updateQuery)) {
        echo json_encode(['message' => 'Subject updated successfully.', 'icon' => 'success']);
        exit();
    } else {
        echo json_encode(['icon' => 'error', 'message' => 'Failed to update subject. Please try again.']);
        exit();
    }
} else {
    echo json_encode(['icon' => 'error', 'message' => 'Invalid request.']);
    exit();
}
?>