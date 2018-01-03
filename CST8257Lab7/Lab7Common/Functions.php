<?php
include_once 'Student.php';
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

function ValidatePasswordMatch($password1, $password2){
    return $password1 == $password2 ? TRUE:FALSE;
}

function SaveStudentRecord($studentId, $studentName, $phoneNumber, $Password){
    
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
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
    $dbConnection = parse_ini_file("db_connection.ini");
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
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

