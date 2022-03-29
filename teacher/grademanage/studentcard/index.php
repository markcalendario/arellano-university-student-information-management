<?php
session_start();

include '../../../php/inc.php';
include '../../../php/view-student.php';
include '../../../php/data-encryption.inc.php';

$user = new user;

function sendToHomePage() {
    header('location: ..');
}

if (!isset($_SESSION['loggedin'])) {
    sendToHomePage();
}

if ($user->isStudent()) {
    sendToHomePage();
}

########

class viewStudentCardInformation {

    public $fullName = '-';
    public $gender = '-';
    public $lrn = '-';
    public $age = '';

    public $sectionName = '-';
    public $adviserFullName = '-';
    public $strand = '-';


    public function __construct() {
        $con = connect();

        $viewStudentGradesID = null;

        if (isset($_SESSION['viewStudentGradesID'])) {
            $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
        }

        $stmt = $con->prepare("SELECT 
        GROUP_CONCAT(lastname, ', ', firstname, ' ', middlename),
        gender, lrn, birthday, section
        FROM user_information 
        INNER JOIN user_school_info 
        ON user_school_info.id = user_information.id
        WHERE user_information.id = ?");
        $stmt->bind_param('i', $viewStudentGradesID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($fullName, $gender, $lrn, $birthday, $section);
        $stmt->fetch();

        $this->fullName = $fullName;
        $this->gender = $gender;
        $this->lrn = $lrn;
        $this->setAge($birthday);
        $this->setCardSectionInfo($section);
    }

    private function setAge($birthday) {
        $birthday = new DateTime($birthday);
        $now = new DateTime();
        $age = $now->diff($birthday);
        $this->age = $age->y;
    }

    private function setCardSectionInfo($section) {
        $con = connect();

        $stmt = $con->prepare("SELECT section_name, strand_name, GROUP_CONCAT(firstname, ' ', lastname) FROM sections
        INNER JOIN user_information
        ON sections.adviser_id = user_information.id
        INNER JOIN strands
        ON sections.strand_id = strands.strand_id
        WHERE sections.id = ?");
        $stmt->bind_param('i', $section);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($sectionName, $strandName, $adviserFullName);
        $stmt->fetch();

        $this->sectionName = $sectionName;
        $this->adviserFullName = $adviserFullName;
        $this->strand = $strandName;
    }
}

#######

function getAverage($grade1, $grade2) {
    return number_format((($grade1 + $grade2) / 2), 2, '.', ',');
}

function getGeneralAverageFirstSemester() {
    $con = connect();

    $totalGrades = 0;
    $totalCountOfQuarters = 0;

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
        $totalCountOfQuarters += 2;
        $totalGrades += $grading1 + $grading2;
    }
    
    $average = '';

    set_error_handler(function () {
    throw new Exception('Invalid grades.');
    });

    try {
        $average = number_format($totalGrades / $totalCountOfQuarters, '3');
    } catch (Exception $e) {
        $average = 'No grades listed to get mean.';
    }

    return $average;

}

function getGeneralAverageSecondSemester() {
    $con = connect();

    $totalGrades = 0;
    $totalCountOfQuarters = 0;

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
        $totalCountOfQuarters += 2;
        $totalGrades += $grading1 + $grading2;
    }
    
    $average = '';

    set_error_handler(function () {
    throw new Exception('Invalid grades.');
    });

    try {
        $average = number_format($totalGrades / $totalCountOfQuarters, '3');
    } catch (Exception $e) {
        $average = 'No grades listed to get mean.';
    }

    return $average;
}

function fetchFirstSemesterCoreGrade() {
    $con = connect();

    $totalGrades = 0;
    $totalCountOfQuarters = 0;

    $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 1 AND subjects.subject_type = 0');
    $stmt->bind_param('i', $viewStudentGradesID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        ?>
        <tr>
            <td class="subject-name"><?php echo $subjectName; ?></td>
            <td> <?php echo $grading1; ?> </td>
            <td> <?php echo $grading2; ?> </td>
            <td> <?php echo getAverage($grading1, $grading2); ?> </td>
        </tr>
        <?php
        $totalCountOfQuarters += 2;
        $totalGrades += $grading1 + $grading2;
    }

}

function fetchFirstSemesterSpecGrade() {
    $con = connect();

    $totalGrades = 0;
    $totalCountOfQuarters = 0;

    $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 1 AND subjects.subject_type = 1');
    $stmt->bind_param('i', $viewStudentGradesID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        ?>
        <tr>
            <td class="subject-name"><?php echo $subjectName; ?></td>
            <td> <?php echo $grading1; ?> </td>
            <td> <?php echo $grading2; ?> </td>
            <td> <?php echo getAverage($grading1, $grading2); ?> </td>
        </tr>
        <?php
        $totalCountOfQuarters += 2;
        $totalGrades += $grading1 + $grading2;
    }

}

function fetchSecondSemesterCoreGrade() {
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
        <tr>
            <td class="subject-name"><?php echo $subjectName; ?></td>
            <td> <?php echo $grading1; ?> </td>
            <td> <?php echo $grading2; ?> </td>
            <td> <?php echo getAverage($grading1, $grading2); ?> </td>
        </tr>
        <?php
    }

}

function fetchSecondSemesterSpecGrade() {
    $con = connect();

    $viewStudentGradesID = validateInput(decryptData($_SESSION['viewStudentGradesID']));
    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 2 AND subjects.subject_type = 1');
    $stmt->bind_param('i', $viewStudentGradesID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        
        ?>
        <tr>
            <td class="subject-name"><?php echo $subjectName; ?></td>
            <td> <?php echo $grading1; ?> </td>
            <td> <?php echo $grading2; ?> </td>
            <td> <?php echo getAverage($grading1, $grading2); ?> </td>
        </tr>
        <?php
    }

}


$viewStudentCardInformation = new viewStudentCardInformation;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../../styles/query.css">
    <link rel="stylesheet" href="../../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Card View</title>
</head>
<body>

    <nav>
        <div class="container">
            <h1>STUDENT MANAGEMENT SYSTEM</h1>
            <h3> <?php echo strtoupper($viewStudentCardInformation->fullName); ?> REPORT CARD</h3>
        </div>

        <form action="" method="post">
            <button onclick="window.print()"> <i class="fas fa-print"></i> Print </button>
            <!-- <button type="submit"> <i class="fas fa-edit"></i> Edit Core Values </button>
            <button type="submit"> <i class="fas fa-calendar-alt"></i> Edit Core Values </button> -->
        </form>
    </nav>

    <div class="container">

        <div id="card">
            <div class="container-fluid">
                <div class="card-wrapper">

                    <div class="left">
                        <h1 class="head">REPORT ON LEARNING PROGRESS AND ACHIEVEMENT</h1>
                        
                        <div class="grades-tabular">
                            <p class="semester-text">First Semester</p>
                            <table>
                                <thead>
                                <tr>
                                    <th class="for-subjects" rowspan="2">Subjects</th>
                                    <th class="for-quarter" colspan="2">Quarter</th>
                                    <th class="for-semester-grade" rowspan="2">Semester Final Grade</th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>2</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="subject-category" colspan="4">Core Subjects</td>
                                </tr>
                                <?php fetchFirstSemesterCoreGrade(); ?>
                                <tr>
                                    <td class="subject-name"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="subject-name"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="subject-category" colspan="4">Applied and Specialized Subjects</td>
                                </tr>
                                <?php fetchFirstSemesterSpecGrade(); ?>
                                <tr>
                                    <td class="subject-name"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="subject-name"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">General Average for the Semester</td>
                                    <td colspan="1"> <?php echo getGeneralAverageFirstSemester(); ?> </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="grades-tabular">
                            <p class="semester-text">Second Semester</p>
                            <table>
                                <thead>
                                <tr>
                                    <th class="for-subjects" rowspan="2">Subjects</th>
                                    <th class="for-quarter" colspan="2">Quarter</th>
                                    <th class="for-semester-grade" rowspan="2">Semester Final Grade</th>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>4</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="subject-category" colspan="4">Core Subjects</td>
                                </tr>
                                <?php fetchSecondSemesterCoreGrade(); ?>
                                <tr>
                                    <td class="subject-name"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="subject-name"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="subject-category" colspan="4">Applied and Specialized Subjects</td>
                                </tr>
                                <?php fetchSecondSemesterSpecGrade(); ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">General Average for the Semester</td>
                                    <td colspan="1"> <?php echo getGeneralAverageSecondSemester(); ?> </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="right">
                        <h1 class="head">REPORT ON LEARNER'S OBSERVED VALUES</h1>
                        
                        <div class="values-table">
                            <table>
                                <tr>
                                    <th class="core-values-head" rowspan="2">Core Values</th>
                                    <th class="behavior-statement-head" rowspan="2">Behavior Statements</th>
                                    <th class="quarter-head" colspan="4">Quarter</th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                    <td>4</td>
                                </tr>
                                <tr>
                                    <td class="core-values" rowspan="2">1. Maka-Diyos</td>
                                    <td>Expresses one's spiritual beliefs while respecting the spiritual beliefs of others.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Shows adherence to tethical principles by upholding truth</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!-- MAKATAO -->
                                <tr>
                                    <td class="core-values" rowspan="2">1. Makatao</td>
                                    <td>Is sensitive to individual, social, and cultural differences.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Demonstrates contributions toward solidarity.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!-- MAKAKALIKASAN -->
                                <tr>
                                    <td class="core-values">1. Maka Kalikasan</td>
                                    <td>Expresses one's spiritual beliefs while respecting the spiritual beliefs of others.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!-- MAKABANSA -->
                                <tr>
                                    <td class="core-values" rowspan="2">1. Makatao</td>
                                    <td>Is sensitive to individual, social, and cultural differences.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Demonstrates contributions toward solidarity.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>

                        <div class="observed-values">
                            <div class="container">
                                <h4>Observed Values</h4>
                                <table id="observed-values-table">
                                    <tr>
                                        <th>Marking</th>
                                        <th>Non-numerical Rating</th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Always Observed</td>
                                    </tr>
                                    <tr>
                                        <td>SO</td>
                                        <td>Sometimes Observed</td>
                                    </tr>
                                    <tr>
                                        <td>RO</td>
                                        <td>Rarely Observed</td>
                                    </tr>
                                    <tr>
                                        <td>NO</td>
                                        <td>Not Observed</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="learner-progress-and-achievement">
                            <h4>Learner Progress and Achievement</h4>
                            <table>
                                <tr>
                                    <th>Descriptors</th>
                                    <th>Grading Scale</th>
                                    <th>Remarks</th>
                                </tr>
                                <tr>
                                    <td>Outstanding</td>
                                    <td>90-100</td>
                                    <td>Passed</td>
                                </tr>
                                <tr>
                                    <td>Very Satisfactory</td>
                                    <td>85-89</td>
                                    <td>Passed</td>
                                </tr>
                                <tr>
                                    <td>Satisfactory</td>
                                    <td>80-84</td>
                                    <td>Passed</td>
                                </tr>
                                <tr>
                                    <td>Fairly Satisfactory</td>
                                    <td>75-79</td>
                                    <td>Passed</td>
                                </tr>
                                <tr>
                                    <td>Did Not Meet Expectation</td>
                                    <td>Belo 75</td>
                                    <td>Failed</td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <br>

        <div id="card-back">
            <div class="container-fluid">
                <div class="card-wrapper">

                    <div class="left">
                        <h1 class="head">REPORT ON ATTENDANCE</h1>
                        <table>
                            <tr>
                                <th></th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Aug</th>
                                <th>Sept</th>
                                <th>Oct</th>
                                <th>Nov</th>
                                <th>Dec</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <td>No. of School days</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>No. of days present</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>No. of days absent</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>

                        <div class="parent-guard-signature">
                            <h4>PARENT / GUARDIAN'S SIGNATURE</h4>
                            <p>1st Quarter: __________________________</p>
                            <p>2nd Quarter: __________________________</p>
                            <p>3rd Quarter: __________________________</p>
                            <p>4th Quarter: __________________________</p>
                        </div>
                    </div>

                    <div class="right">

                        <div class="card-banner">
                            <div class="card-banner-img">
                                <img src="../../../assets/images/AULOGO.png" alt="" srcset="">
                            </div>
                            <div class="banner-content">
                                <h4>ARELLANO UNIVERSITY</h4>
                                <h4>JOSE RIZAL HIGH SCHOOL</h4>
                                <h5>MALABON CITY</h5>
                            </div>
                        </div>
                        
                        <h3 class="report">REPORT ON LEARNING PROGRESS AND ACHIEVEMENT</h3>


                        <div class="student-info">
                            <table>
                                <tr>
                                    <td colspan="3">
                                        LRN: <?php echo $viewStudentCardInformation->lrn; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Name: <?php echo $viewStudentCardInformation->fullName; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Track/Strand: <?php echo $viewStudentCardInformation->strand; ?>
                                    </td>
                                    <td>
                                        Age: <?php echo $viewStudentCardInformation->age; ?>
                                    </td>
                                    <td>
                                        Sex: <?php echo $viewStudentCardInformation->gender; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        Section:  <?php echo $viewStudentCardInformation->sectionName; ?>
                                    </td>
                                    <td colspan="2">
                                        School Year: 2020-2021
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="dear-parent">
                            <p>Dear Parent: </p>
                            <p>This report card shows the ability and progress your child has made in the different learning areas as well as his/her core values.</p>
                            <p>The school welcomes you should desire to know more about your child's progress.</p>
                        </div>

                        <div class="primary-signature">
                            <div>
                                <p class="name">Mrs. Ma. Aileene D. Cruz</p>
                                <p>Principal</p>
                            </div>
                            <div>
                                <p class="name">  <?php echo $viewStudentCardInformation->adviserFullName; ?> </p>
                                <p>Teacher</p>
                            </div>
                        </div>

                        <div class="certificate-of-transfer">
                            <h4>Certificate of Transfer</h4>
                            <table>
                                <tr>
                                    <td>Admitted to Grade:</td>
                                    <td>Section: </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Eligibility for Admission to Grade:</td>
                                </tr>
                                <tr>
                                    <td>Approved</td>
                                </tr>
                                <tr>
                                    <td>Principal:</td>
                                    <td>Teacher:</td>
                                </tr>
                            </table>
                        </div>
                        

                        <div class="certificate-of-transfer">
                            <h4>Certificate of Eligibility</h4>
                            <table>
                                <tr>
                                    <td>Admitted in:</td>
                                    <td>Date: </td>
                                </tr>
                                <tr>
                                    <td>Principal:</td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
    

</body>
</html>

<script src="../../../js/aos/dist/aos.js"></script>
<script>
  AOS.init();
</script>
