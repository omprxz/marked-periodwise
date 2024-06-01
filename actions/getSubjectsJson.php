<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php';
$class_id = $_GET['id'];
$subjectsQuery = "SELECT id, code, type, name FROM subjects WHERE c_id = '$class_id'";
$result = mysqli_query($conn, $subjectsQuery);

$response = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = ['subjects' => [
            'id' => $row['id'],
            'code' => $row['code'],
            'type' => $row['type'],
            'name' => $row['name'],
            'f_id' => $row['f_id'],
            'c_id' => $row['c_id']],
            'icon' => 'success',
            'message' => 'Subjects loaded successfully'
        ];
    }
} else {
    $response[] = [
        'icon' => 'error',
        'message' => 'No subjects found'
    ];
}

echo json_encode($response);

mysqli_close($conn);
?>