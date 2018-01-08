<?php

Class Course {
    private $code;
    private $title;
    private $hours;
   
    public function __construct($code, $title, $hours) {
        $this->code = $code;
        $this->title = $title;
        $this->hours = $hours;
    
    }
    public function getCourseCode() {
        return $this->code;
    }
    public function getCourseTitle() {
        return $this->title;
    }
    public function getCourseHours() {
        return $this->hours;
    }

}

class Student{
    private $id;
    private $name;
    private $phone;
    
    public function __construct($id, $name, $phone){
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
       
    }
 
    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getPhone() {
        return $this->phone;
    }
}

Class Semester {
    private $code;
    private $term;
    private $year;
    
    public function __construct($code, $term, $year) {
        $this->code = $code;
        $this->term = $term;
        $this->year = $year;
    }
    
    public function getCode() {
        return $this->code;
    }
    public function getTerm() {
        return $this->term;
    }
    public function getYear() {
        return $this->year;
    }
    public function getName(){
        return $this->year." ".$this->term;
    }
}

Class CourseOffer {
    private $courseCode;
    private $semesterCode;
    
    public function __construct($courseCode, $semesterCode) {
        $this->courseCode = $courseCode;
        $this->semesterCode = $semesterCode;
    }
    public function getCourseCode(){
        return $this->courseCode;
    }
    public function getSemesterCode(){
        return $this->semesterCode;
    }
}

Class Registration {
    private $studentId;
    private $courseCode;
    private $semesterCode;
    
    public function __construct($studentId, $courseCode, $semesterCode) {
        $this->studentId = $studentId;
        $this->courseCode = $courseCode;
        $this->semesterCode = $semesterCode;
    }
    
    public function getStudentId(){
        return $this->studentId;
    }
    public function getCourseCode(){
        return $this->courseCode;
    }
    public function getSemesterCode(){
        return$this->semesterCode;
    }
}
