<?php

function restriction_init() {

    $user = new user;

    if ($user->isFaculty()) {
        restrictFacultyUsers();
    } 
    else if ($user->isTeacher()) {
        restrictTeachers();
    }
    else if ($user->isStudent()) {
        restrictStudent();
    }

    # restriction for all
    if ($user->isFaculty() || $user->isTeacher() || $user->isStudent()) {
        restrictionForAll();
    }
}

function restrictionForAll() {
    $user = new user;

    if (!$user->isAccountActive()) {
        setRestriction('Your account has been deactivated.');
    }
}

function restrictFacultyUsers() {

}

function restrictTeachers() {
    $teacher = new teacher;

    if (!$teacher->isHandlingASection()) {
        setRestriction('You are not handling a section yet.');
    }

    if ($teacher->isHandlingASection() && !$teacher->isSectionActive()) {
        setRestriction('Your section is currently deactivated.');
    }
}

function restrictStudent() {
    $student = new student;
    if ($student->studentSectionID == null) {
        setRestriction('You are not enrolled in a section.');
    }
}

function setRestriction($restriction) {
    $_SESSION['restriction'] = $restriction;
}

restriction_init();

function isRestricted() {
    if (isset($_SESSION['restriction'])) {
        return 1;
    } else {
        return 0;
    }
}

?>