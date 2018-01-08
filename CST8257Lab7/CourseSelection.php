<?php
session_start();

include 'Lab7Common/Header.php';
include_once 'Lab7Common/Functions.php';
if (!isset($_SESSION['loginStudent'])) {
    $_SESSION['attemptAccessPage'] = 'CourseSelection.php';
    header("location:login.php");
    exit();
}

$student = unserialize($_SESSION["loginStudent"]);
$studentName = $student->getName();
$studentId = $student->getId();
$semesters = GetSemesters();
$error = "";
// get selected semester code
$selectedSemesterCode;
if (urldecode($_GET['selectedSemesterCode']) != NULL){
    $selectedSemesterCode = urldecode($_GET['selectedSemesterCode']);
    $_SESSION['selectedSemesterCode'] = $selectedSemesterCode;
}
else if($_SESSION['selectedSemesterCode'] != NULL) {
    $selectedSemesterCode = $_SESSION['selectedSemesterCode'];
}
else {
    $selectedSemesterCode = $semesters[0]->getCode();
}
//get semester course by selected semester code
$semesterCourses = GetCourseBySemeter($selectedSemesterCode);

//get registered course of a student in a selected semester
$registerCourses = GetRegistrationCoursesBySemester($studentId, $selectedSemesterCode);
// get registered courses hours
$registerCourseHours = GetRegisterCourseHours($registerCourses);
// get avaliable register course hours
$availableCourseHours = 16 - $registerCourseHours;

extract($_POST);

if (isset($btnSubmit) && ($_SESSION['key'] == $hiddenKey)){
    $validateSuccess = True;
    $selectedCourseNumer = sizeof($selectedCourseCodes);
    $validateNumberSuccess = ValidateCourseNumber($selectedCourseNumer);
    if (!$validateNumberSuccess){
        $error = "You need to select at least one course!";
        $validateSuccess = FALSE;
    }
    else {
        $selectedCourseHours = 0;
        foreach ($selectedCourseCodes as $courseCode){
            $course = GetCourseByCourseCode($courseCode);
            $courseHours = $course->getCourseHours();
            $selectedCourseHours += $courseHours;
        }
        if ($selectedCourseHours > $availableCourseHours){
            $error = "Your selection exceed the max weekly hours";
            $validateSuccess = FALSE;
        }
    }
    if ($validateSuccess) {
        foreach ($selectedCourseCodes as $selectedCourseCode){
            RegisterCourse($studentId, $selectedCourseCode, $selectedSemesterCode);
        }
        header('location:CourseSelection.php');
    }
}
// get available semester courses
$availableCourses = GetAvailableCourse($semesterCourses, $registerCourses);
$_SESSION['key'] = mt_rand(0, 1000000);
?>
<div class = "course-selection">
    <h2 class="text-center">Course Selection</h2>
    <p>Welcome <span><?php echo "$studentName" ?></span>(not your? change user <a href="Login.php">here</a>)</p>
    <p>You have registered <span class="highlight"><?php echo "$registerCourseHours" ?></span> hours for the selected semester</p>
    <p>You can register <span class="highlight"><?php echo "$availableCourseHours" ?></span> more hours of course(s) for the semester</p>
    <p>Please note that the courses you have registered will not be displayed in the list</p>
    <br/>
    <div class = "course-list">
        <form action = "CourseSelection.php" role = "form" method="post">
             <input type="hidden" name="hiddenKey" value="<?php echo $_SESSION['key'] ?>"/>
            
            <div class = "col-sm-3 semester-list">
                <select class = "form-control" id="selectedSemester" name = "selectSemester" onchange="ChangeSemester()" >
                    <?php
                       foreach ($semesters as $semester){
                           $semesterName = $semester->getName();
                           $semesterCode = $semester->getCode();
                           if ($semesterCode == $selectedSemesterCode) {
                               echo "<option value = '$semesterCode' selected = 'selected'>$semesterName</option>";
                           }
                           else {
                               echo "<option value = '$semesterCode'>$semesterName</option>";
                           }
                       }
                    ?>
                </select>
            </div>
             
              <div class = "error"><?php echo "$error" ?></div>         
            <div class = "course-table">
               
                <table class = "table">
                    <thead>
                        <th>Code</th>
                        <th>Course Title</th>
                        <th>Hours</th>
                        <th>Select</th>
                    </thead>
                    <?php
                         foreach ($availableCourses as $course){
                            $courseCode = $course->getCourseCode();
                            $courseTitle = $course->getCourseTitle();
                            $courseHours = $course->getCourseHours();
                            echo "<tr>";
                            echo "<td>$courseCode</td>";
                            echo "<td>$courseTitle</td>";
                            echo "<td>$courseHours</td>";
                            echo "<td><input type = 'checkbox' name = 'selectedCourseCodes[]' value='$courseCode'></td>";
                            echo "</tr>";
                            
                        }
                    ?>
                </table>
            </div>
            
             <div class = "col-sm-1 btn-submit v-margin "><button type = "submit" name = "btnSubmit"  class = "btn btn-success btn-block">Submit</button></div>
             <div class = "col-sm-1 v-margin" ><button type = "submit" name = "btnClear" class = "btn btn-warning btn-block">Clear</button></div>
            
        </form>
    </div>
</div>



<?php include 'Lab7Common/Footer.php'; ?>


