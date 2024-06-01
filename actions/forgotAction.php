<?php
session_start();
require 'conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../libs/PHPMailer/src/Exception.php';
require '../libs/PHPMailer/src/PHPMailer.php';
require '../libs/PHPMailer/src/SMTP.php';

function generateOTP() {
    $otp = rand(1000, 9999);
    return $otp;
}

function sendMail($email, $otp) {
    $mail = new PHPMailer(true);

    $user = ['fittrackdesk@gmail.com'];
    $pass = ['ercx otwp mnvy whtd'];
    $subject = 'MarkEd Password Reset OTP';

    $message = '<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>MarkEd OTP Email</title>
      </head>
      <body>
        <p>Your OTP of MarkEd Reset Password Request is ' . $otp . '</p>
        <p>This OTP will expire shortly. Please use it for verification purposes.</p>
        <p>If you didn\'t request this OTP, please disregard this message.</p>
      </body>
    </html>';

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Username = $user[0];
        $mail->Password = $pass[0];

        $mail->setFrom('marked@gmail.com', 'MarkEd');
        $mail->addAddress($email, "MarkEd");

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}


if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];

   if ($action == 'send') {
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = $_POST['email'];

        $checkEmailQuery = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmailQuery) > 0) {
            $otp = generateOTP();
            $_SESSION['otp'] = $otp;

            $sendOtp = sendMail($email, $otp);
            if ($sendOtp) {
                echo json_encode(array('status' => 'success', 'message' => 'OTP Sent!'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Error sending OTP.'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Email does not exist.'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Email is required.'));
    }
} elseif($action == 'verify') {
        if(isset($_POST['email']) && !empty($_POST['email']) &&
           isset($_POST['otp']) && !empty($_POST['otp']) &&
           isset($_POST['pass']) && !empty($_POST['pass'])) {
            $email = $_POST['email'];
            $otp = $_POST['otp'];
            $password = $_POST['pass'];

            if(isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {
              $pass = password_hash($password, PASSWORD_DEFAULT);
                $sql = "update users set pass = '$pass' where email = '$email'";
                $result = mysqli_query($conn, $sql);
                if($result){
                unset($_SESSION['otp']);
                echo json_encode(array('status' => 'success', 'message' => 'Password has been reset.'));
                }else{
                  echo json_encode(array('status' => 'error', 'message' => 'Error while changing password.'));
                }
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Invalid OTP.'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Email, OTP, and Password are required.'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid action.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'No action specified.'));
}
?>