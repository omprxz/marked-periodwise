<?php
require('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $resultType = $_POST['result_type'];

    $checkQuery = "SELECT COUNT(*) as count FROM result_types WHERE type = '$resultType' AND id != '$id'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['count'] > 0) {
        echo json_encode(array("status" => "error", "message" => "New result type already exists"));
        exit;
    }

    $query = "UPDATE result_types SET type='$resultType' WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        echo json_encode(array("status" => "success", "message" => "Result type updated successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error updating result type"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>