<?php
require('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $query = "DELETE FROM result_types WHERE id='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(array("status" => "success", "message" => "Result type deleted successfully"));
        exit;
    } else {
        echo json_encode(array("status" => "error", "message" => "Error deleting result type"));
        exit;
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
    exit;
}
?>