<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php';
$class_id = $_GET['id'];
$subjectsQuery = "SELECT id, code, type, name FROM subjects where c_id = '$class_id'";
$result = mysqli_query($conn, $subjectsQuery);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="' . $row['id'] . '"><span class="view-subject" data-id="' . $row['id'] . '" style="cursor: pointer;">' . $row['name'] . ' (' . $row['type'] . ') </span>
            <span class="d-flex gap-3">
                <i class="fad fa-pencil-alt edit-subject text-primary" data-id="' . $row['id'] . '" style="cursor: pointer;"></i>
                <i class="fad fa-trash-alt delete-subject text-danger" data-id="' . $row['id'] . '" style="cursor: pointer;"></i>
            </span>
        </li>';
    }
} else {
    echo '<div class="alert alert-warning" role="alert">No subjects found.</div>';
}

mysqli_close($conn);
?>