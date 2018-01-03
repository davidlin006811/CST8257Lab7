<?php
session_start();

include 'Lab7Common/Header.php';
include_once 'Lab7Common/Student.php';
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
<div class="course-selection">
    <h1>Course Selection</h1>
    <p>Welcome <span class="highlight"><?php echo "$studentName" ?></span>! (not you? change user <a href="Login.php">here</a>)</p>
</div>
<?php
include 'Lab7Common/Footer.php';?>


