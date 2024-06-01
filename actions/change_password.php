<?php
session_start();
require('conn.php');

if (!isset($_SESSION["userid"])) {
    $response['status'] = 'failed';
    $response['result'] = 'User session not found.';
    echo(json_encode($response));
    exit();
}

$sUserId = $_SESSION["userid"];
$response = array();

if (isset($_POST['currentPass']) && isset($_POST['newPass'])) {
    $admin_currentPass = $_POST['currentPass'];
    $admin_newPass = $_POST['newPass'];

    $hashed_newPass = password_hash($admin_newPass, PASSWORD_DEFAULT);

    $checkPassQuery = "SELECT pass FROM users WHERE id = $sUserId LIMIT 1";
    $checkPassResult = mysqli_query($conn, $checkPassQuery);

    if ($checkPassResult) {
        if (mysqli_num_rows($checkPassResult) == 1) {
            $user = mysqli_fetch_assoc($checkPassResult);

            if (password_verify($admin_currentPass, $user['pass'])) {
                $updatePassQuery = "UPDATE users SET pass = '$hashed_newPass' WHERE id = $sUserId";
                $updatePassResult = mysqli_query($conn, $updatePassQuery);

                if ($updatePassResult) {
                    $response['status'] = 'success';
                    $response['result'] = 'Password changed.';
                    echo(json_encode($response));
                    exit();
                } else {
                    $response['status'] = 'failed';
                    $response['result'] = 'Something went wrong!';
                    echo(json_encode($response));
                    exit();
                }
            } else {
                $response['status'] = 'failed';
                $response['result'] = 'Wrong password!';
                echo(json_encode($response));
                exit();
            }
        } else {
            $response['status'] = 'failed';
            $response['result'] = 'User not found!';
            echo(json_encode($response));
            exit();
        }
    } else {
        $response['status'] = 'failed';
        $response['result'] = 'Something went wrong!';
        echo(json_encode($response));
        exit();
    }
} else {
    $response['status'] = 'failed';
    $response['result'] = 'Method not allowed!';
    echo(json_encode($response));
    exit();
}
?>