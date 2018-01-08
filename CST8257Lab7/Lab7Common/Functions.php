<?php

include_once 'Entities.php';
function ValidateStudentId($id){
    return $id != NULL? TRUE:FALSE;
}

function ValidateStudentName($name){
   return $name !=NULL? TRUE:FALSE;
}

function ValidatePhone($phone) {

    $phoneRegExp = "/^[2-9][0-9][0-9]-[2-9][0-9][0-9]-[0-9]{4}$/";
    if (preg_match($phoneRegExp, $phone)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function ValidatePassword($password){
    $passwordError = "";
    if ($password == NULL) {
        $passwordError = "blank error";
        return $passwordError;
    }
    if(strlen($password) < 6){
        $passwordError = "length error";
        return $passwordError;
    }
    $containsUpperCase = preg_match('/[A-Z]/', $password);
    $containsLowerCase = preg_match('/[a-z]/', $password);
    $containDigitNumber = preg_match('/\d/', $password);
    if (!$containsUpperCase || !$containsLowerCase || !$containDigitNumber){
        $passwordError = "format error";
    }
    return $passwordError;
}
function ValidateCourseNumber($courseNumber){
    return $courseNumber > 0? TRUE:FALSE;
}

function ValidatePasswordMatch($password1, $password2){
    return $password1 == $password2 ? TRUE:FALSE;
}
function ConnectSQLServer(){
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    return $myPdo;
}

function SaveStudentRecord($studentId, $studentName, $phoneNumber, $Password){
    
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Student WHERE StudentId= :studentId';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['studentId'=>$studentId]);
    foreach ($pStmt as $row) {
        if ($row['StudentId'] != NULL) {
            return FALSE;
        }
    }
    $insertStudent = "INSERT INTO Student VALUES(:id, :name, :phoneNumber, :password)";
    $pSignUp = $myPdo->prepare($insertStudent);
    $pSignUp->execute(['id'=>$studentId, 'name'=>$studentName, 'phoneNumber'=>$phoneNumber, 'password'=>$Password]);
    return TRUE;
}

function StudentLogin($studentId, $loginPassword){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Student WHERE StudentId= :studentId AND Password= :password';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['studentId'=>$studentId, 'password'=>$loginPassword]);
    foreach ($pStmt as $row){
        
        if ($row['StudentId'] != NULL) {
            $student = new Student($row['StudentId'], $row['Name'], $row['Phone']);
            return $student;
        }
    }
    return NULL;
}

//For courseSelect page:

// get course by course code
function GetCourseByCourseCode($courseCode){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Course WHERE CourseCode = :courseCode';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['courseCode'=>$courseCode]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if ($row){
        $course = new Course($row['CourseCode'], $row['Title'], $row['WeeklyHours']);
        return $course;
    }
    else{
        return NULL;
    }
}
//get all the courses by semester
function GetCourseBySemeter($semesterCode)
{
       $courseOffers = array();
       $courses = array();
       $myPdo = ConnectSQLServer();
        $sql = 'SELECT * FROM CourseOffer WHERE SemesterCode = :semesterCode';
        
        $pStmt = $myPdo->prepare($sql);

        $pStmt->execute( [ 'semesterCode' => $semesterCode ] );
        
        foreach ($pStmt as $row)
        {
             $courseOffer = new CourseOffer($row['CourseCode'], $row['SemesterCode']);
             array_push($courseOffers, $courseOffer);
        }
        if (sizeof($courseOffers) > 0){
            foreach ($courseOffers as $courseOffer) {
                $courseCode = $courseOffer->getCourseCode();
                $course = GetCourseByCourseCode($courseCode);
                if ($course != NULL){
                    array_push($courses, $course);
                }
            }
            if (sizeof($courses) > 0){
                return $courses;
            }
            else {
                return NULL;
            }
        }
        else {
            return NULL;
        }
    }
    
    //get all the courses registered by a student in a semester
function GetRegistrationCoursesBySemester($studentId, $semesterCode)
{
        $courses = array();
        $courseCodes = array();
        $myPdo = ConnectSQLServer();
        
        $sql = 'SELECT CourseCode From Registration WHERE StudentId = :studentId && SemesterCode = :semesterCode ORDER BY CourseCode ASC';
        
        $pStmt = $myPdo->prepare($sql);

        $pStmt->execute( ['studentId'=>$studentId, 'semesterCode'=>$semesterCode] );
        
        foreach ($pStmt as $row)
        {
            if ($row) {
                array_push($courseCodes, $row['CourseCode']);
            }
            
        }
        if (sizeof($courseCodes) > 0) {
            foreach ($courseCodes as $courseCode){
                
                $course = GetCourseByCourseCode($courseCode);
                if ($course != NULL) {
                    array_push($courses, $course);
                }
            }
            if (sizeof($courses) > 0) {
                return $courses;
            }
            else {
                return NULL;
            }
        }
        else {
            return NULL;
        }
        

    }
    //get all the semester code
function GetSemesters(){
    $semesters = array();
    $myPdo = ConnectSQLServer();
    $sql = "SELECT * FROM Semester";
    $pStmt = $myPdo->prepare($sql);
    $pStmt->execute();
    foreach ($pStmt as $row)
    {
        $semester = new Semester( $row['SemesterCode'], $row['Term'], $row['Year'] );
        array_push($semesters, $semester);
    }
    if (sizeof($semesters) > 0) {
        return $semesters;
    }
    else {
        return NULL;
    }
}

// get available courses
function GetAvailableCourse($semesterCourses, $registerCourses){
     $availableCourses = array();
     if ($registerCourses == NULL){
         return $semesterCourses;
     }
     for($i = 0; $i < sizeof($semesterCourses); $i++){
        if (!in_array($semesterCourses[$i], $registerCourses)){
            array_push($availableCourses, $semesterCourses[$i]);
        }
    }
    if (sizeof($availableCourses) > 0) {
        return $availableCourses;
    }
    else {
        return NULL;
    }
}
// get register courses hours
function GetRegisterCourseHours($registerCourses){
    $totalHours = 0;
    if ($registerCourses == NULL){
        return 0;
    }
    foreach ($registerCourses as $course){
        $hour = $course->getCourseHours();
        $totalHours += (int) $hour;
    }
    return $totalHours;
}
//register a course
function RegisterCourse($studentId, $courseCode, $semesterCode){
    
       $myPdo = ConnectSQLServer();  
       $sql = "INSERT INTO Registration VALUES(:studentId, :courseCode, :semesterCode)";
       $pStmt = $myPdo->prepare($sql);
       $pStmt->execute(['studentId'=>$studentId, 'courseCode'=>$courseCode, 'semesterCode'=>$semesterCode]);
}

// get registration records
function GetRegistrationSemesterCodes($studentId){
    $registrationSemesterCodes = array();
    $myPdo = ConnectSQLServer(); 
    $sqlStatement = 'SELECT DISTINCT(SemesterCode) FROM Registration WHERE StudentId = :studentId ORDER BY SemesterCode ASC';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['studentId'=>$studentId]);
    foreach ($pStmt as $row){
        if ($row){
             array_push($registrationSemesterCodes, $row['SemesterCode']);
        }
       
    }
    if (sizeof($registrationSemesterCodes) > 0){
        return $registrationSemesterCodes;
    }
    else {
        return NULL;
    }
}

// get semester by semester code
function GetSemesterByCode($semesterCode){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'SELECT * FROM Semester WHERE SemesterCode = :semesterCode';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['semesterCode'=>$semesterCode]);
    $row = $pStmt->fetch(PDO::FETCH_ASSOC);
    if ($row){
        $semester = new Semester($row['SemesterCode'], $row['Term'], $row['Year']);
        return $semester;
    }
    else {
        return NULL;
    }
}

// delete registration record
function DeleteRegistrationRecord($studentId, $courseCode, $semesterCode){
    $myPdo = ConnectSQLServer();
    $sqlStatement = 'DELETE FROM Registration WHERE StudentId = :studentId && CourseCode = :courseCode && SemesterCode = :semesterCode';
    $pStmt = $myPdo->prepare($sqlStatement);
    $pStmt->execute(['studentId'=>$studentId, 'courseCode'=>$courseCode, 'semesterCode'=>$semesterCode]);
}
