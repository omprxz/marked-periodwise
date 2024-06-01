<?php
session_start();
require_once('conn.php');
if(isset($_SESSION['userid'])){
 $sUserId = $_SESSION['userid'];
 $sql = "select * from users where id = $sUserId";
 $userSql=mysqli_query($conn, $sql);
 if(mysqli_num_rows($userSql)==0){
   header('Location: /logout.php');
 }else{
     $userSql=mysqli_fetch_assoc($userSql);
 }
}

?>