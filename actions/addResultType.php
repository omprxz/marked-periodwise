<?php
require('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resultType = $_POST['result_type'];

    $checkQuery = "SELECT COUNT(*) as count FROM result_types WHERE type = '$resultType'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] > 0) {
        echo json_encode(array("status" => "error", "message" => "Result type already exists"));
        exit;
    }

    $query = "INSERT INTO result_types (type, created) VALUES ('$resultType', '$timestamp')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(array("status" => "success", "message" => "Result type added successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error adding result type"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>