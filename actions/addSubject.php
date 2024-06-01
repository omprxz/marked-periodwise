<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['code']) && isset($_POST['name']) && isset($_POST['class_id']) && isset($_POST['teacher_id']) && isset($_POST['hpw']) && isset($_POST['type'])) {
        require('conn.php');

        $code = mysqli_real_escape_string($conn, $_POST['code']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);
        $teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id']);
        $hpw = mysqli_real_escape_string($conn, $_POST['hpw']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);

        // Check if code, name, class_id, teacher_id, type doesn't match
        $checkQuery = "SELECT * FROM subjects WHERE code = '$code' AND name = '$name' AND c_id = '$class_id' AND f_id = '$teacher_id' AND type = '$type'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $response = array('message' => 'Subject with same details already exists.', 'icon' => 'error');
            echo json_encode($response);
            exit();
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $byid = $_SESSION['userid'];

        $insertQuery = "INSERT INTO subjects (code, name, c_id, f_id, hpw, type, ip, byid) VALUES ('$code', '$name', '$class_id', '$teacher_id', '$hpw', '$type', '$ip', '$byid')";

        if (mysqli_query($conn, $insertQuery)) {
            $response = array('message' => 'Subject added successfully.', 'icon' => 'success');
            echo json_encode($response);
            exit();
        } else {
            $response = array('message' => 'Failed to add subject. Please try again.', 'icon' => 'error');
            echo json_encode($response);
            exit();
        }
    } else {
        $response = array('message' => 'All fields are required.', 'icon' => 'error');
        echo json_encode($response);
        exit();
    }
} else {
    $response = array('message' => 'Invalid request method.', 'icon' => 'error');
    echo json_encode($response);
    exit();
}
?>