<?php 

function validateInput($input) {
    $con = connect();
    $invalidCharacters = array(
        '$',
        '\'',
        '"',
        '<',
        '>',
        '\\',
        '/'
    );
    return str_replace($invalidCharacters, '' ,$input);
}

function titleCase($string) {
    return ucwords(strtolower($string));
}

function upperCase($string) {
    return strtoupper($string);
}

#
# ADD STUDENT INPUT VALIDATION
#

function addStudentInputAreValid() {

    $isValid = true;

    $addStudentForm = array(
        'firstname',
        // 'middlename', == OPTIONAL
        'lastname',
        'gender',
        'birthday',
        'religion',
        'country',
        'region',
        'address',
        'contact',
        'mothername',
        'motherwork',
        'mothercontact',
        'fathername',
        'fatherwork',
        'fathercontact',
        'guardian',
        'relationship',
        'guardiancontact',
        'lrn',
        'section'
    );

    for ($i = 0; $i < count($addStudentForm); $i++) { 
        
        if (empty($_POST[$addStudentForm[$i]])) {
            $isValid = false;
        }

        // echo " %name = %_POST['".$addStudentForm[$i]."']";

    }

    return $isValid;

}

#
# ADD STUDENT PERSONNEl VALIDATION
#

function addPersonnelInputAreValid() {

    $isValid = true;

    $addPersonnelForm = array(
        'firstname',
        // 'middlename', == OPTIONAL
        'lastname',
        'gender',
        'birthday',
        'religion',
        'country',
        'region',
        'address',
        'contact',
        'personnelType'
    );

    for ($i = 0; $i < count($addPersonnelForm); $i++) { 
        
        if (empty($_POST[$addPersonnelForm[$i]])) {
            $isValid = false;
        }
    }

    if (decryptData($_POST['personnelType']) > 2 || decryptData($_POST['personnelType']) < 1) {
        $isValid = false;
    }

    return $isValid;

}

#
# CREATE SECTION INPUT VALIDATION
#

function createSectionInputAreValid() {

    $isValid = 1;

    $createSectionFormList = array(
        'create-section-name',
        'create-section-strand',
        'create-section-adviser'
    );

    for ($i = 0; $i < count($createSectionFormList); $i++) { 
        
        if (empty($_POST[$createSectionFormList[$i]])) {
            $isValid = 0;
        }

    }
    return $isValid;
}

#
# EDIT SECTION INPUT VALIDATION
#

function editSectionFormsAreValid() {
    $isValid = 1;

    $createSectionFormList = array(
        'edit-section-name',
        'edit-section-strand',
        'edit-section-adviser'
    );

    for ($i = 0; $i < count($createSectionFormList); $i++) { 
        
        if (empty($_POST[$createSectionFormList[$i]])) {
            $isValid = 0;
        }

    }

    return $isValid;

}

#
# POST ANNOUNCEMENT INPUT VALIDATION
#


function postAnnouncementFormsAreValid() {
    $isValid = 1;

    $createSectionFormList = array(
        'post-announcement-title',
        'post-announcement-content'
    );

    for ($i = 0; $i < count($createSectionFormList); $i++) { 
        
        if (empty($_POST[$createSectionFormList[$i]])) {
            $isValid = 0;
        }

    }

    return $isValid;
}

#
# TODO INPUT VALIDATION
#

function verifyActivityFormFields() {
    if (empty($_POST['todo-text'])) {
        $_SESSION['todo'] = 'todoEmpty';
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    } else if (strlen($_POST['todo-text']) > 150) {
        $_SESSION['todo'] = 'todoEmpty';
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    }
    else {
        addTodoActivity();
    }
}

#
# EDIT STUDENT INPUT VALIDATION
#

function editStudentInputFieldAreNotEmpty() {

    $isValid = true;

    $editStudentForm = array(
        'edit-student-firstname',
        // 'middlename', == OPTIONAL
        'edit-student-lastname',
        'edit-student-gender',
        'edit-student-birthday',
        'edit-student-religion',
        'edit-student-country',
        'edit-student-region',
        'edit-student-address',
        'edit-student-contact',
        'edit-student-mother-name',
        'edit-student-mother-work',
        'edit-student-mother-contact',
        'edit-student-father-name',
        'edit-student-father-work',
        'edit-student-father-contact',
        'edit-student-guardian-name',
        'edit-student-guardian-contact',
        'edit-student-relationship',
        'edit-student-lrn',
        'edit-student-section'
    );

    for ($i = 0; $i < count($editStudentForm); $i++) { 
        
        if (empty($_POST[$editStudentForm[$i]])) {
            $isValid = false;

        }

    }

    return $isValid;

}

#
# TEACHER INPUT VALIDATION
#

function isTeacherEditInputFieldValid() {
    $isValid = 1;

    $teacherEditFormList = array(
        'edited-teacher-firstname',
        'edited-teacher-lastname'
    );

    for ($i = 0; $i < count($teacherEditFormList); $i++) { 
        
        if (empty($_POST[$teacherEditFormList[$i]])) {
            $isValid = 0;
        }

    }

    return $isValid;
}

function createSubjectInputAreValid() {
    $isValid = 1;

    $createSubjectFormList = array(
        'create-subject-name',
        'create-subject-type',
        'create-subject-semester'
    );

    for ($i = 0; $i < count($createSubjectFormList); $i++) { 
        
        if (empty(validateInput($_POST[$createSubjectFormList[$i]]))) {
            $isValid = 0;
        }
    }

    if (validateInput($_POST[$createSubjectFormList[1]]) > 1) {
        $isValid = 0;
    }

    if (validateInput($_POST[$createSubjectFormList[2]]) > 2) {
        $isValid = 0;
    }

    return $isValid;
}

function editSubjectInputAreValid() {
    $isValid = 1;

    $editSubjectFormList = array(
        'edit-subject-name',
        'edit-subject-type'
    );

    for ($i = 0; $i < count($editSubjectFormList); $i++) { 
        
        if (empty(validateInput($_POST[$editSubjectFormList[$i]]))) {
            $isValid = 0;
        }
    }

    if (validateInput($_POST[$editSubjectFormList[1]]) > 1) {
        $isValid = 0;
    }

    return $isValid;
}

function strandEditValuesAreValid() {
    $isValid = 1;

    $editStrandForm = array(
        'new-strand-name',
        'new-grade-level'
    );

    for ($i = 0; $i < count($editStrandForm); $i++) { 
        
        if (empty(validateInput($_POST[$editStrandForm[$i]]))) {
            $isValid = 0;
        }
    }

    if (validateInput(decryptData($_POST[$editStrandForm[1]])) < 11 || validateInput(decryptData($_POST[$editStrandForm[1]])) > 12) {
        $isValid = 0;
    }

    return $isValid;
}

function createStrandInputsAreValid() {
    $isValid = 1;

    $createStrandForm = array(
        'strand-name',
        'grade-level'
    );

    for ($i = 0; $i < count($createStrandForm); $i++) { 
        
        if (empty(validateInput($_POST[$createStrandForm[$i]]))) {
            $isValid = 0;
        }
    }

    if (validateInput(decryptData($_POST[$createStrandForm[1]])) < 11 || validateInput(decryptData($_POST[$createStrandForm[1]])) > 12) {
        $isValid = 0;
    }

    return $isValid;
}

?>