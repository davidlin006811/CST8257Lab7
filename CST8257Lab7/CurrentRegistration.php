<?php
    session_start();
    include 'Lab7Common/Header.php';
    include_once 'Lab7Common/Functions.php';
    if (!isset($_SESSION['loginStudent'])){
         $_SESSION['attemptAccessPage'] = 'CurrentRegistration.php';
        header("location:login.php");
        exit();
    } 
   
    $student = unserialize($_SESSION["loginStudent"]);
    $studentName = $student->getName();
    $studentId = $student->getId();
    extract($_POST);
    if (isset($btnDelete) && ($_SESSION['key'] == $hiddenKey)) {
        if (sizeof($selectRegistrations) > 0){
            for ($i = 0; $i < sizeof($selectRegistrations); $i++){
                $questionMarkIndex = strpos($selectRegistrations[$i], '?');
                $courseCode = substr($selectRegistrations[$i], $questionMarkIndex + 1);
                $semesterCode = substr($selectRegistrations[$i], 0, $questionMarkIndex);
                DeleteRegistrationRecord($studentId, $courseCode, $semesterCode);
            }
        }
    }
    // get register semesterCodes from Registration table
    $registrationSemesterCodes = GetRegistrationSemesterCodes($studentId);
    $_SESSION['key'] = mt_rand(0, 1000000); 
 ?>


<div class="current-registration">
    <h2 class="text-center">Current Registrations</h2>
    <p>Hello <span class="highlight"><?php echo "$studentName" ?></span>(not you? change user <a href="Login.php">here</a>), the followings are your current registrations</p>
    <form action="CurrentRegistration.php" role="form" method="post">
        <input type="hidden" name="hiddenKey" value="<?php echo $_SESSION['key'] ?>"/>
        <div class = "registration-table">
            <table class="table">
                <thead>
                    <th>Year</th>
                    <th>Term</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Hours</th>
                    <th>Select</th>
                </thead>
                <?php 
                    foreach ($registrationSemesterCodes as $semesterCode){
                        $registerCourses = GetRegistrationCoursesBySemester($studentId, $semesterCode);
                        $registerSemester = GetSemesterByCode($semesterCode);
                        $year = $registerSemester->getYear();
                        $term = $registerSemester->getTerm();
                        $weeklyHours = 0;
                        foreach ($registerCourses as $course){
                            $courseCode = $course->getCourseCode();
                            $courseTitle = $course->getCourseTitle();
                            $hours = $course->getCourseHours();
                            $weeklyHours += (int) $hours;
                            $selectId = $semesterCode.'?'.$courseCode;
                            echo "<tr>";
                            echo "<td>$year</td>";
                            echo "<td>$term</td>";
                            echo "<td>$courseCode</td>";
                            echo "<td>$courseTitle</td>";
                            echo "<td>$hours</td>";
                            echo "<td><input type='checkbox' name='selectRegistrations[]' value = '$selectId' /></td>";
                            echo "</tr>";
                        }
                        echo "<tr><td></td><td></td><td></td><td><span>Total Weekly Hours</span></td><td>$weeklyHours</td><td></td></tr>";
                    }
                ?>
            </table>
          </div>
        <div class = "col-sm-2 btn-deselect v-margin "><button type = "submit" name = "btnDelete"  class = "btn btn-primary btn-block" onclick="confirmDelete()">DeleteSelected</button></div>
          <div class = "col-sm-2 v-margin" ><button type = "submit" name = "btnClear" class = "btn btn-primary btn-block">Clear</button></div>
            
    </form>
   
</div>
<?php
include 'Lab7Common/Footer.php'; ?>
