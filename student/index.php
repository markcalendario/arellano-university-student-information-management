<?php
session_start();

include '../php/inc.php';
$user = new user;
$student = new student;

function sendToHomePage() {
    header('location: ..');
}

if (!isset($_SESSION['loggedin'])) {
    sendToHomePage();
}

if (!$user->isStudent()) {
    sendToHomePage();
}

    if (isRestricted()) {
        header('location: ../restrict');
    }

####

function getRemarks($grading1, $grading2) {
    
    $average = ($grading1 + $grading2) / 2;
    
    if ($grading1 == 0 || $grading2 == 0) {
        return '<p class="incomplete"> Ungraded </p>';
    } else if ($average >= 74.5) {
        return '<p class="passed"> Passed </p>';
    } else {
        return '<p class="failed"> Failed </p>';
    }
}

function fetchFirstSemesterGrade() {
    $con = connect();

    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 1');
    $a = $_SESSION['id'];
    $stmt->bind_param('i', $a);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        
        ?>
        <form method="POST">
            <tr>
                <td> <?php echo $subjectName; ?> </td>
                <td> <?php echo $grading1; ?> </td>
                <td> <?php echo $grading2; ?> </td>
                <td> <?php echo getRemarks($grading1, $grading2); ?> </td>
            </tr>
        </form>
        <?php
    }

}

function fetchSecondSemesterGrade() {
    $con = connect();

    $stmt = $con->prepare('SELECT grade_id, subject_name, grading_period_1, grading_period_2
    FROM subjects 
    INNER JOIN student_grades
    ON subjects.subject_id = student_grades.subject_id
    WHERE student_grades.student_id = ?
    AND subjects.semester = 2');
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($grade_id, $subjectName, $grading1, $grading2);

    while ($stmt->fetch()) {
        
        ?>
        <form method="POST">
            <tr>
                <td> <?php echo $subjectName; ?> </td>
                    <td> <?php echo $grading1; ?> </td>
                    <td> <?php echo $grading2; ?> </td>
                <td> <?php echo getRemarks($grading1, $grading2); ?> </td>
            </tr>
        </form>
        <?php
    }

}

class standing {
    public $prelim = 'Unavailable';
    public $midterm = 'Unavailable';
    public $prefinals = 'Unavailable';
    public $finals = 'Unavailable';
    public $standing = '0';

    public function standing() {
        $con = connect();

        $stmt = $con->prepare('SELECT 
        subjects.semester, grading_period_1, grading_period_2
        FROM student_grades
        LEFT JOIN subjects
        ON subjects.subject_id = student_grades.subject_id
        WHERE student_id = ?');
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($semester, $gradingPeriod1, $gradingPeriod2);

        $numberOfSubjectsGraded = array(0, 0);
        $totalGradesForAllSemester = array(
            0, // [0] Prelim
            0, // [1] Midterm
            0, // [2] Prefinals
            0  // [3] Finals
        );

        while ($stmt->fetch()) {
            if ($semester == 1) {

                $totalGradesForAllSemester[0] += $gradingPeriod1;
                $totalGradesForAllSemester[1] += $gradingPeriod2;
                $numberOfSubjectsGraded[0]++;

            } else {

                $totalGradesForAllSemester[2] = $totalGradesForAllSemester[2] + $gradingPeriod1;
                $totalGradesForAllSemester[3] = $totalGradesForAllSemester[3] + $gradingPeriod2;
                $numberOfSubjectsGraded[1]++;

            }

        }


        $this->hasGraded($totalGradesForAllSemester, $numberOfSubjectsGraded);

    }

    private function hasGraded($totalGradesForAllSemester, $numberOfSubjectsGraded) {

        if ($numberOfSubjectsGraded[0] != 0) {
            $this->setRemarksForFirstSemester($totalGradesForAllSemester, $numberOfSubjectsGraded);
        }

        if ($numberOfSubjectsGraded[1] != 0) {
            $this->setRemarksForSecondSemester($totalGradesForAllSemester, $numberOfSubjectsGraded);
        }


    }

    private function setRemarksForFirstSemester($grades, $numberOfSubjectsGraded) {

        # PRELIM
        $average = $grades[0] / $numberOfSubjectsGraded[0];
        if ($this->isPassed($average)) {
            $this->prelim = 'PASSED';
        } else {
            $this->prelim = 'FAILED';
        }

        if ($average < 50) {
            $this->prelim = 'ON GOING';
        }

        # MIDTERM
        $average = $grades[1] / $numberOfSubjectsGraded[0];
        if ($this->isPassed($average)) {
            $this->midterm = 'PASSED';
        } else {
            $this->midterm = 'FAILED';
        }

        if ($average < 50) {
            $this->midterm = 'ON GOING';
        }

    }

    private function setRemarksForSecondSemester($grades, $numberOfSubjectsGraded) {
        # PREFINALS
        $average = $grades[2] / $numberOfSubjectsGraded[1];
        if ($this->isPassed($average)) {
            $this->prefinals = 'PASSED';
        } else {
            $this->prefinals = 'FAILED';
        }

        if ($average < 50) {
            $this->prefinals = 'ON GOING';
        }

        # FINALS
        $average = $grades[3] / $numberOfSubjectsGraded[1];
        if ($this->isPassed($average)) {
            $this->finals = 'PASSED';
        } else {
            $this->finals = 'FAILED';
        }

        if ($average < 50) {
            $this->finals = 'ON GOING';
        }
    }


    private function isPassed($average) {
        if ($average >= 74.5) {
            return 1;
        } else {
            return 0;
        }
    }
}

function getRankBatchWide() {
    $con = connect();
    $student = new student;

    $stmt = $con->prepare(

    'SELECT student_id, SUM(grading_period_1 + grading_period_2) AS totalGrade 
    
    FROM student_grades

    LEFT JOIN user_school_info 
    ON user_school_info.id = student_grades.student_id 
    
    LEFT JOIN sections 
    ON user_school_info.section = sections.id
    
    LEFT JOIN strands 
    ON sections.strand_id = strands.strand_id
    
    WHERE strands.strand_grade = ?
    
    GROUP BY student_id ORDER BY totalGrade DESC'
    
    );

    $stmt->bind_param('i', $student->studentStrandGrade);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($studentID, $totalGrade);

    $currentRank = 1;
    $finalRank = 0;
    $totalStudent = 0;
    $targetStudent = $_SESSION['id'];

    while ($stmt->fetch()) {

        if ($studentID != $targetStudent) {
            $currentRank++;
        } else {
            $finalRank = $currentRank;
        }

        $totalStudent++;
    }

    return $finalRank . " / ". $totalStudent;
}

function getRankStrandWide() {
    $con = connect();
    $student = new student;

    $stmt = $con->prepare(

    'SELECT student_id, SUM(grading_period_1 + grading_period_2) AS totalGrade 
    
    FROM student_grades

    LEFT JOIN user_school_info 
    ON user_school_info.id = student_grades.student_id 
    
    LEFT JOIN sections 
    ON user_school_info.section = sections.id
    
    LEFT JOIN strands 
    ON sections.strand_id = strands.strand_id
    
    WHERE strands.strand_id = ?
    
    GROUP BY student_id ORDER BY totalGrade DESC'
    
    );

    $stmt->bind_param('i', $student->studentStrandID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($studentID, $totalGrade);

    $currentRank = 1;
    $finalRank = 0;
    $totalStudent = 0;
    $targetStudent = $_SESSION['id'];

    while ($stmt->fetch()) {

        if ($studentID != $targetStudent) {
            $currentRank++;
        } else {
            $finalRank = $currentRank;
        }

        $totalStudent++;
    }

    return $finalRank . " / ". $totalStudent;
}

function getRankRoom() {
    $con = connect();
    $student = new student;

    $stmt = $con->prepare(

    'SELECT student_id, SUM(grading_period_1 + grading_period_2) AS totalGrade 
    
    FROM student_grades

    LEFT JOIN user_school_info 
    ON user_school_info.id = student_grades.student_id 
    
    LEFT JOIN sections 
    ON user_school_info.section = sections.id
    
    WHERE sections.id = ?
    
    GROUP BY student_id ORDER BY totalGrade DESC'
    
    );

    $stmt->bind_param('i', $student->studentSectionID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($studentID, $totalGrade);

    $currentRank = 1;
    $finalRank = 0;
    $totalStudent = 0;
    $targetStudent = $_SESSION['id'];

    while ($stmt->fetch()) {

        if ($studentID != $targetStudent) {
            $currentRank++;
        } else {
            $finalRank = $currentRank;
        }

        $totalStudent++;
    }

    return $finalRank . " / ". $totalStudent;
}

$standing = new standing;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../styles/query.css">
    <link rel="stylesheet" href="../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Student</title>
</head>
<body>

    <section id="main-UI">
        <aside class="sidebar-pane">
            <div class="container-fluid">
                <button class="show-side-bar side-bar-close" type="button"><i class="fas fa-times"></i></button>
                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="<?php echo fetchProfilePicture('../'); ?>">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4> <?php echo $user->getFullName(); ?> </h4>
                            <p> Student <?php echo $student->studentFullSectionDescription;  ?> </p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Navigation</h1>

                            <div class="sidebar-link active-link">
                                <a href="../student"><i class="fas fa-chart-bar"></i> <span>Statistics and Grades</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="rankings"><i class="fas fa-trophy"></i> <span>View Rankings</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="classmates"><i class="fas fa-users"></i> <span>View Classmates</span></a>
                            </div>

                            <h1 class="list-title">Information</h1>

                            <div class="sidebar-link">
                                <a href="me"><i class="fas fa-sticky-note"></i> <span>View Information</span></a>
                            </div>

                            <h1 class="list-title">Settings</h1>

                            <div class="sidebar-link">
                                <a href="../settings"><i class="fas fa-cog"></i> <span>Settings</span></a>
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
                            <h1>Student Portal</h1>
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

            <section data-aos="fade-right" id="greetings">
                <div class="container">
                    <div class="greetings-wrapper">
                        <div class="container">
                            <div class="greet-icon">
                                <img src="assets/images/notify.svg" alt="" srcset="">
                            </div>
                            <div class="greet-texts">
                                <div class="greet-title">
                                    <h1>Hello, 
                                        <span>
                                            <?php echo $user->getFullName(); ?>
                                        </span>
                                    </h1>
                                </div>
                                <div class="greet-text-content">
                                    Welcome! You are logged in as a student of <?php echo $student->studentFullSectionDescription; ?>.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <section id="standing">
                <div class="container">
                    <div class="standing-wrapper">
                        <h1>Your standing for the year 2020 - 2021</h1>
                        <p> <?php echo $user->getFullName(); ?> of <?php echo $student->studentFullSectionDescription; ?></p>
                        <p> <i class="fas fa-info-circle"></i> These standings update on every commits of grades from the adviser. </p>

                        <div>

                            <div class="standing">

                                <div class="batch standing-result">
                                    <h1> <?php echo getRankBatchWide(); ?> </h1>
                                    <p>Batch</p>
                                </div>

                                <div class="strand standing-result">
                                    <h1> <?php echo getRankStrandWide(); ?> </h1>
                                    <p>Strand</p>
                                </div>

                                <div class="room standing-result">
                                    <h1> <?php echo getRankRoom(); ?> </h1>
                                    <p>Room</p>
                                </div>

                            </div>

                        </div>

                        <div class="semester-result">
                            <h1> Remarks </h1>
                            <table>
                                <tr>
                                    <td>Preliminary</td>
                                    <th class="<?php echo strtolower($standing->prelim); ?>"> <?php echo $standing->prelim; ?> </th>
                                </tr>
                                <tr>
                                    <td>Midterm</td>
                                    <th class="<?php echo strtolower($standing->midterm); ?>"> <?php echo $standing->midterm; ?> </th>
                                </tr>
                                <tr>
                                    <td>Pre-finals</td>
                                    <th class="<?php echo strtolower($standing->prefinals); ?>"> <?php echo $standing->prefinals; ?> </th>
                                </tr>
                                <tr>
                                    <td>Finals</td>
                                    <th class="<?php echo strtolower($standing->finals); ?>"> <?php echo $standing->finals; ?> </th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section id="grade-management">
                <div class="container">
                    <div class="wrapper">

                        <section id="student-grade">
                            <div class="wrapper">

                                <div class="title">
                                    <h1>Your Grades</h1>
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
                                    </tr>
                                    <?php fetchSecondSemesterGrade(); ?>
                                </table>

                            </div>
                        </section>

                    </div>
                </div>
            </section>

        </div>
    </section>

</body>
</html>

<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../js/aos/dist/aos.js"></script>
<script src="js/todo.js"></script>
<script>AOS.init();</script>
<script src="../js/sidebar.js"></script>
