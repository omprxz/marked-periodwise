<?php
date_default_timezone_set('Asia/Kolkata');
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "marked";
} else {
    $servername = "sql200.infinityfree.com";
    $username = "if0_36661768";
    $password = "Om015107";
    $dbname = "if0_36661768_marked_period";
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$timestamp = date('Y-m-d H:i:s');

require_once('checkLogin.php');
require_once('vars.php');
//require_once('cron.php');
?>