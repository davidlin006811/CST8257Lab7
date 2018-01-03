<?php

Class Course {
    private $code;
    private $title;
    private $hours;
    private $year;
    private $term;
    
    public function __construct($code, $title, $hours, $year, $term) {
        $this->code = $code;
        $this->title = $title;
        $this->hours = $hours;
        $this->year = $year;
        $this->term = $term;
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
    public function getCourseYear() {
        return $this->year;
    }
    public function getCourseTerm() {
        return $this->term;
    }
}

