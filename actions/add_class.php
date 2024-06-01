<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['userid'])) {
    $response = array("status" => "error", "message" => "Session expired. Please log in again.", "icon" => "error");
} else {
    $sUserId = $_SESSION['userid'];
    $session = $_POST['session'];
    $branch = $_POST['branch'];
    $checkQuery = "SELECT * FROM classes WHERE session = '$session' AND branch = '$branch'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        $response = array("status" => "error", "message" => "Class already exists.", "icon" => "error");
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $byid = $sUserId;
        
        $insertQuery = "INSERT INTO classes (session, branch, byid, ip) VALUES ('$session', '$branch', '$byid', '$ip')";
        if (mysqli_query($conn, $insertQuery)) {
            $response = array("status" => "success", "message" => "Class added successfully.", "icon" => "success");
        } else {
            $response = array("status" => "error", "message" => "Failed to add class.", "icon" => "error");
        }
    }
}

echo json_encode($response);
?>