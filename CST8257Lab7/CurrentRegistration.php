<?php
    session_start();
    include 'Lab7Common/Header.php';
    include_once 'Lab7Common/Student.php';
    include_once 'Lab7Common/Course.php';
    include_once 'Lab7Common/Functions.php';
    if (!isset($_SESSION['loginStudent'])){
    header("location:login.php");
    exit();
    } 
    $student = unserialize($_SESSION["loginStudent"]);
    $studentName = $student->getName();
 ?>

<script>
    $('#login').hide();
    $('#logout').show();
</script>
<div class="current-registration">
    <h1>Current Registration</h1>
    <p>Hello <span class="highlight"><?php echo "$studentName" ?></span>(not you? change user <a href="Login.php">here</a>), the followings are your current registrations</p>
</div>
<?php
include 'Lab7Common/Footer.php'; ?>
