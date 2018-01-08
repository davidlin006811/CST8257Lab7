<?php 
session_start();
include 'Lab7Common/Header.php';
if (isset($_SESSION["loginStudent"]) ) {
    header("location:CourseSelection.php");
    exit();
}
?>
<div class = "welcome">
    <h1>Welcome to Algonquian College Online Course Registration</h1>
    <p>If you have never used this before, you have to <a href="NewUser.php">Sign up</a> first</p>
    <p>If you have already signed up, you can <a href="Login.php">Log in</a> now</p>
</div>

<?php include 'Lab7Common/Footer.php'; ?>
