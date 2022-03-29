<?php
session_start();
include '../../php/inc.php';
include '../../php/data-encryption.inc.php';

$user = new user;
$teacher = new teacher;

function sendToHomePage() {
    header('location: ..');
}

function restrict() {
    $user = new user;

    if (!isset($_SESSION['loggedin'])) {
        sendToHomePage();
    }
    
    if (!$user->isTeacher()) {
        sendToHomePage();
    }

    if (isset($_SESSION['restriction'])) {
        header('location: ../../restrict');
    }
}

function showPopups() {
    if (isset($_SESSION['successCreateGradingField'])) {
        showSuccessPopup('Grading Field Created Successfuly.');
        unset($_SESSION['successCreateGradingField']);
    }

    if (isset($_SESSION['failedCreateGradingField'])) {
        showErrorPopup('Failed Creating Grading Field', 'An internal error occured. Report it immediately.');
        unset($_SESSION['failedCreateGradingField']);
    }
}

function page_init() {
    showPopups();
    restrict();
}

page_init();

#
# STUDENT LIST
#

function fetchStudentsList() {
    $con = connect();

    $teacher = new teacher;
    $sectionID = $teacher->getHandleSectionID();

    $stmt = $con->prepare('SELECT 
    user_information.id, 
    firstname, 
    middlename, 
    lastname,
    gender
    FROM user_information
    INNER JOIN user_school_info ON 
    user_school_info.id = user_information.id 
    INNER JOIN user_credentials ON
    user_credentials.id = user_school_info.id
    WHERE user_school_info.section = ?
    AND account_status = 1
    ORDER BY gender DESC, lastname ASC');
    
    $stmt->bind_param('i', $sectionID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($sid, $firstName, $middleName, $lastName, $gender);
    
    if ($stmt->num_rows() == 0) {
        ?>
        <form method="post">
            <input type="hidden" name="student-id" value="<?php echo encryptData($sid); ?>">
            <button class="no-student-to-show" type="button"> <i class="fad fa-user-slash"></i> No students to show. </button>
        </form>
        <?php
    }

    while ($stmt->fetch()) {
        ?>
        <form method="post">
            <input type="hidden" name="student-id" value="<?php echo encryptData($sid); ?>">
            <button class="<?php if ($gender == 'Male') { echo 'male'; } else { echo 'female'; } ?>" name="view-student-grade" type="submit"> <i class="fad fa-user"></i> <?php echo $lastName . ', ' . $firstName . ' ' . $middleName; ?> </button>
        </form>
        <?php
    }
}

if (isset($_POST['view-student-grade'])) {
    $_SESSION['viewStudentGradesID'] = $_POST['student-id'];
    header('location: ../grademanage/');
}

#
# VIEW STUDENT GRADES
#

$viewStudentGrades = new viewStudentGrades;
class viewStudentGrades {

    public $fullName = '-';
    public $gender = '-';
    public $lrn = '-';

    public function __construct() {
        $con = connect();

        $viewStudentGradesID = null;

        if (isset($_SESSION['viewStudentGradesID'])) {
            $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
        }

        $stmt = $con->prepare("SELECT 
        GROUP_CONCAT(lastname, ', ', firstname, ' ', middlename),
        gender, lrn
        FROM user_information 
        INNER JOIN user_school_info 
        ON user_school_info.id = user_information.id
        WHERE user_information.id = ?");
        $stmt->bind_param('i', $viewStudentGradesID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($fullName, $gender, $lrn);
        $stmt->fetch();

        $this->fullName = $fullName;
        $this->gender = $gender;
        $this->lrn = $lrn;

    }

}

#
# Remarks
#

function getRemarks($grading1, $grading2) {
    
    $average = ($grading1 + $grading2) / 2;
    
    if ($grading1 == 0 || $grading2 == 0) {
        return '<p class="incomplete"> Incomplete </p>';
    } else if ($average >= 74.5) {
        return '<p class="passed"> Passed </p>';
    } else {
        return '<p class="failed"> Failed </p>';
    }
}

#
# First Semester Grade
#

function fetchFirstSemesterGrade() {
    $con = connect();

    $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 1');
    $stmt->bind_param('i', $viewStudentGradesID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        
        ?>
        <form method="POST">
            <input type="hidden" name="student-grade-field-id" value='<?php echo encryptData($grade_id); ?>'>
            <tr>
                <td> <?php echo $subjectName; ?> </td>
                    <?php 
                        if (isset($_POST['edit-grade']) && decryptData($_POST['student-grade-field-id']) == $grade_id) {
                            ?>
                                <td> <input type="number" min='0' max='100' step='0.01' name='grade-period-1' value="<?php echo $grading1; ?>"> </td>
                                <td> <input type="number" min='0' max='100' step='0.01'  name='grade-period-2' value="<?php echo $grading2; ?>"> </td>
                            <?php
                        } else {
                            ?>
                                <td> <?php echo $grading1; ?> </td>
                                <td> <?php echo $grading2; ?> </td>
                            <?php
                        }
                    ?>
                <td> <?php echo getRemarks($grading1, $grading2); ?> </td>
                <td>
                    <?php 
                        if (isset($_POST['edit-grade']) && decryptData($_POST['student-grade-field-id']) == $grade_id) {
                            ?>
                            <button name="save-grade"><i class="fas fa-save"></i></button>
                            <?php
                        } 
                        else {
                            ?>
                            <button name="edit-grade"><i class="fas fa-edit"></i></button>
                            <?php
                        }
                    ?>
                </td>
            </tr>
        </form>
        <?php
    }

}

function fetchSecondSemesterGrade() {
    $con = connect();

    $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 2');
    $stmt->bind_param('i', $viewStudentGradesID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        
        ?>
        <form method="POST">
            <input type="hidden" name="student-grade-field-id" value='<?php echo encryptData($grade_id); ?>'>
            <tr>
                <td> <?php echo $subjectName; ?> </td>
                    <?php 
                        if (isset($_POST['edit-grade']) && decryptData($_POST['student-grade-field-id']) == $grade_id) {
                            ?>
                                <td> <input type="number" min='0' max='100' step='0.01' name='grade-period-1' value="<?php echo $grading1; ?>"> </td>
                                <td> <input type="number" min='0' max='100' step='0.01' name='grade-period-2' value="<?php echo $grading2; ?>"> </td>
                            <?php
                        } else {
                            ?>
                                <td> <?php echo $grading1; ?> </td>
                                <td> <?php echo $grading2; ?> </td>
                            <?php
                        }
                    ?>
                <td> <?php echo getRemarks($grading1, $grading2); ?> </td>
                <td>
                    <?php 
                        if (isset($_POST['edit-grade']) && decryptData($_POST['student-grade-field-id']) == $grade_id) {
                            ?>
                            <button name="save-grade"><i class="fas fa-save"></i></button>
                            <?php
                        } 
                        else {
                            ?>
                            <button name="edit-grade"><i class="fas fa-edit"></i></button>
                            <?php
                        }
                    ?>
                </td>
            </tr>
        </form>
        <?php
    }

}

#
# CREATE STUDENT GRADE FIELD
#

if (isset($_POST['create-grade-field-prompt'])) {
    showCreateGradeFieldModal();
}

if (isset($_POST['create-grade-field-final'])) {
    if (isValidGradeFieldSubject($_POST['subject-grade-field'])) {
        createGradeField();
    } else {
        showInvalidPopup('Select Subject For Grade Field First.', 'Or you have already a complete set of subject for this student.');
    }
}

function isValidGradeFieldSubject($subjectGradeField) {
    if (empty($subjectGradeField)) {
        return 0;
    } else {
        return 1;
    }
}

function createGradeField() {
    $con = connect();
    $viewStudentGrades = new viewStudentGrades;
    $activityLog = new activityLog;

    $teacher = new teacher;
    $subjectID = decryptData($_POST['subject-grade-field']);
    $studentID = decryptData($_SESSION['viewStudentGradesID']);
    $subjectIDName = getSubjectName($subjectID);

    $stmt = $con->prepare('INSERT INTO student_grades (student_id, subject_id)
                            VALUES (?, ?)');
    $stmt->bind_param('ii', $studentID, $subjectID);

    if ($stmt->execute()) {
        $activityLog->recordActivityLog('started editing grades of '. $viewStudentGrades->fullName . ' of ' . $teacher->getSectionAndGradeLevel() . ' in ' . $subjectIDName);
        $_SESSION['successCreateGradingField'] = true;
        header('location: ../grademanage/');
        exit(0);
    } else {
        $_SESSION['failedCreateGradingField'] = true;
        header('location: ../grademanage/');
        exit(0);
    }
}


#
# SUBJECT FETCH
#


function fetchSubjects() {
    $con = connect();
    
    $teacher = new teacher;
    $sectionStrandID = $teacher->getSectionStrandID();
    $sectionGradeLevel = $teacher->getSectionGradeLevel();
    define('NOT_ENROLLED', 0);
    

    $stmt = $con->prepare('SELECT 
    subject_1,
    subject_2,
    subject_3,
    subject_4,
    subject_5,
    subject_6,
    subject_7,
    subject_8,
    subject_9
    FROM strand_subjects
    WHERE strand_id = ?');
    $stmt->bind_param('i', $sectionStrandID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($subject1, $subject2, $subject3, $subject4, $subject5, $subject6, $subject7, $subject8, $subject9);

    while ($stmt->fetch()) {

        $subjectIDs = array(
            $subject1,
            $subject2,
            $subject3,
            $subject4,
            $subject5,
            $subject6,
            $subject7,
            $subject8,
            $subject9
        );

        for ($i = 0; $i < count($subjectIDs); $i++) { 

            if (noGradeFieldYet($subjectIDs[$i]) && $subjectIDs[$i] != NOT_ENROLLED) {
                ?>
                <option value="<?php echo encryptData($subjectIDs[$i]); ?>"> <?php echo  getSubjectType($subjectIDs[$i]) . " " . getSubjectName($subjectIDs[$i]); ?> </option>
                <?php
            }

        }

    }

}

function getSubjectName($subjectID) {
    $con = connect();

    $stmt = $con->prepare('SELECT subject_name FROM subjects WHERE subject_id = ?');
    $stmt->bind_param('i', $subjectID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($subjectName);
    $stmt->fetch();

    if ($subjectName != '') {
        return $subjectName;
    } else {
        return 'Not Currently Enrolled';
    }
}

function getSubjectType($subjectID) {
    $con = connect();

    $stmt = $con->prepare('SELECT subject_type FROM subjects WHERE subject_id = ?');
    $stmt->bind_param('i', $subjectID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($subjectType);
    $stmt->fetch();

    if ($subjectType == 0) {
        return '[CORE]';
    } else {
        return '[SPEC]';
    }
}

function noGradeFieldYet($subjectID) {

    $con = connect();
    $studentID = decryptData($_SESSION['viewStudentGradesID']);

    $stmt = $con->prepare('SELECT * FROM student_grades
    WHERE student_id = ? AND subject_id = ?');
    $stmt->bind_param('ii', $studentID, $subjectID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return 1;
    } else {
        return 0;
    }
}

#
# EDIT GRADES
#

if (isset($_POST['save-grade'])) {
    if (isGradeValuesValid()) {
        saveStudentGrade();
    } else {
        showInvalidPopup('Grades Values are Not Valid.', 'Valid grades range from 0 - 99.9');
    }
}

function isGradeValuesValid() {
    
    $grade1Value = $_POST['grade-period-1'];
    $grade2Value = $_POST['grade-period-2'];

    $isValid = 1;

    if (empty($grade1Value) && empty($grade2Value)) {
        $isValid = 0;
    }

    if ($grade1Value > 100 
    || $grade2Value > 100
    || $grade1Value < 0
    || $grade2Value < 0) {
        $isValid = 0;
    }

    return $isValid;
}

function saveStudentGrade() {
    $con = connect();

    $gradingPeriod1 = validateInput($_POST['grade-period-1']);
    $gradingPeriod2 = validateInput($_POST['grade-period-2']);
    $gradeFieldID = validateInput(decryptData($_POST['student-grade-field-id']));

    $stmt = $con->prepare('UPDATE student_grades SET grading_period_1 = ?, grading_period_2 = ? WHERE grade_id = ?');
    $stmt->bind_param('ddi', $gradingPeriod1, $gradingPeriod2, $gradeFieldID);
    $stmt->execute();
    header('location: ../grademanage/');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../styles/query.css">
    <link rel="stylesheet" href="../../styles/modal.css">
    <link rel="stylesheet" href="../../styles/popup.css">
    <link rel="stylesheet" href="../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Grade Management</title>
</head>
<body>

    <section id="main-UI">
        <aside class="sidebar-pane">
            <div class="container-fluid">
            <button class="show-side-bar side-bar-close" type="button"><i class="fas fa-times"></i></button>

                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="<?php echo fetchProfilePicture('../../'); ?>" alt="AU">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4><?php echo $user->getFullName(); ?></h4>
                            <p>Adviser</p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Administrator</h1>

                            <div class="sidebar-link">
                                <a href="../"><i class="fas fa-home"></i> <span>Teacher Portal</span></a>
                            </div>
                            <div class="sidebar-link ">
                                <a href="../viewadvisory"><i class="fas fa-user-plus"></i> <span>Manage Advisory</span></a>
                            </div>
                            <div class="sidebar-link active-link">
                                <a href="../grademanage"><i class="fas fa-award"></i> <span>Manage Grades</span></a>
                            </div>

                            <h1 class="list-title">Personal Settings</h1>

                            <div class="sidebar-link">
                                <a href="../../settings"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
                            </div>
                                                        
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <div class="main-pane">
            <nav>
                <div class="container">
                    <div class="main-pane-topper">

                        <div class="topper-left">
                            <h1>Grading Management</h1>
                        </div>

                        <div class="topper-right">
                            <form action="" method="post">
                                <button class="show-side-bar" type="button"><i class="fas fa-bars"></i></button>
                            </form>
                            <form action="" method="post">
                                <button name="signout" type="submit"><i class="fad fa-sign-out"></i></button>
                            </form>
                        </div>

                    </div>
                </div>
            </nav>

            <section id="grade-management">
                <div class="container">
                    <div class="wrapper">

                        <section id="student-selector">

                            <div class="title">
                                <h1>Students List</h1>
                                <p><?php echo $teacher->getSectionAndGradeLevel(); ?></p>
                            </div>
                            
                            <div class="student-list">
                                <?php fetchStudentsList(); ?>
                            </div>

                        </section>

                        <section id="student-information">
                            <div class="wrapper">
                                <div class="title">
                                    <h1>Student Information</h1>
                                    <?php 
                                
                                        if (isset($_SESSION['viewStudentGradesID'])) {
                                            ?>
                                            <div id="profile-container-view">
                                                <img src="<?php echo fetchUserProfilePicture($_SESSION['viewStudentGradesID'], '../../'); ?>">
                                            </div>
                                            <?php
                                        }
                                        
                                    ?>
                                </div>

                                <table>
                                    <tr>
                                        <th>Name</th>
                                        <td style="color: royalblue;"> <h1><?php echo $viewStudentGrades->fullName; ?></h1> </td>
                                    </tr>
                                    <tr>
                                        <th>LRN</th>
                                        <td> <?php echo $viewStudentGrades->lrn; ?> </td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td> <?php echo $viewStudentGrades->gender; ?> </td>
                                    </tr>
                                </table>
                            </div>
                        </section>

                    <?php 
                    
                    if (isset($_SESSION['viewStudentGradesID'])) {
                        ?>
                        <section id="student-grade">
                            <div class="wrapper">

                                <div class="title">
                                    <h1>Student's Grade</h1>
                                    <?php

                                    if (isset($_SESSION['viewStudentGradesID'])) {
                                        ?>
                                        <form method="post">
                                            <button name="create-grade-field-prompt" type="submit"> <i class="fas fa-plus-circle"></i> Create Grade Field </button>
                                            <a target="_blank" href="studentcard" class="view-card-button"> <i class="fas fa-eye"></i> View Card </a>
                                        </form>
                                        <?php
                                    }

                                    ?>
                                </div>

                                <table>
                                    <tr>
                                        <th class="sem-title" colspan="5"> <i class="fas fa-flag"></i> First Semester</th>
                                    </tr>
                                    <tr>
                                        <th class="subject-name">Subject Name</th>
                                        <th class="small-data">P</th>
                                        <th class="small-data">M</th>
                                        <th class="small-data">R</th>
                                        <th class="small-data">Edit</th>
                                    </tr>
                                    <?php fetchFirstSemesterGrade(); ?>
                                </table>
                                
                                <table>
                                    <tr>
                                        <th class="sem-title" colspan="5"> <i class="fas fa-flag"></i> Second Semester</th>
                                    </tr>
                                    <tr>
                                        <th class="subject-name">Subject Name</th>
                                        <th class="small-data">PF</th>
                                        <th class="small-data">F</th>
                                        <th class="small-data">R</th>
                                        <th class="small-data">Edit</th>
                                    </tr>
                                    <?php fetchSecondSemesterGrade(); ?>
                                </table>

                            </div>
                        </section>
                        <?php
                    }
                    
                    ?>

                    </div>
                </div>
            </section>



        </div>
    </section>
    

</body>
</html>

<script src="../../js/jquery-3.5.1.min.js"></script>
<script src="../../js/aos/dist/aos.js"></script>
<script>AOS.init();</script>
<script src="../../js/sidebar.js"></script>