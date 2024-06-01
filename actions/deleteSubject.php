<?php
require('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $query = "DELETE FROM subjects WHERE id='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(array("status" => "success", "message" => "Subject deleted successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error deleting subject"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>