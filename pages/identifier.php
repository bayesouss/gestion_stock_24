<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['user']))
header('location:../pages/login.php');
?>

<!-- if(!isset($_SESSION['user']))
header('location:../pages/login.php');
$userinfo = $_SESSION['user']; -->