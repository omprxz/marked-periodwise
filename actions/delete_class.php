<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['userid'])) {
    $response = array("status" => "error", "message" => "Session expired. Please log in again.");
} else {
    $classId = $_POST['id'];
    
    $deleteQuery = "DELETE FROM classes WHERE id = '$classId'";
    if (mysqli_query($conn, $deleteQuery)) {
        $response = array("status" => "success", "message" => "Class deleted successfully.", "icon" => "success");
    } else {
        $response = array("status" => "error", "message" => "Failed to delete class.", "icon" => "error");
    }
}

echo json_encode($response);
?>