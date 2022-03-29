<?php 

session_start();
include '../php/inc.php';
include '../php/data-encryption.inc.php';
include '../php/announcement.php';
include '../php/todo.php';

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

    if (!isset($_SESSION['loggedin'])) {
        sendToHomePage();
    }

    if (!$user->isTeacher()) {
        sendToHomePage();
    }

    if (isset($_SESSION['restriction'])) {
        header('location: ../restrict');
    }
}

function showPopups() {

}

function page_init() {
    restrict();
}

page_init();

define('ACHIEVER_GRADE', 89.5);
define('PASSING_GRADE', 74.5);
define('FAILING_GRADE', 74.4);

class sectionStatistics {

    public $achievers_q1 = 0;
    public $passers_q1 = 0;
    public $failures_q1 = 0;

    public $achievers_q2 = 0;
    public $passers_q2 = 0;
    public $failures_q2 = 0;

    public $achievers_q3 = 0;
    public $passers_q3 = 0;
    public $failures_q3 = 0;

    public $achievers_q4 = 0;
    public $passers_q4 = 0;
    public $failures_q4 = 0;

    public function __construct() {
        $this->setQuarterOneStats();
        $this->setQuarterTwoStats();
        $this->setQuarterThreeStats();
        $this->setQuarterFourStats();
    }

    # QUARTER STATS SETTERS

    private function setQuarterFourStats() {
        $con = connect();
        $teacher = new teacher;
        $advisoryID = $teacher->getHandleSectionID();

        $sql = "SELECT GROUP_CONCAT(grading_period_2) 
                FROM student_grades 
                INNER JOIN subjects 
                ON subjects.subject_id = student_grades.subject_id 
                INNER JOIN user_school_info
                ON student_grades.student_id = user_school_info.id 
                WHERE semester = 2 AND section = ?
                GROUP BY student_id";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $advisoryID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($gradesCollection);

        while ($stmt->fetch()) {

            $gradesCollection = explode(',', $gradesCollection);
            $average = $this->getAverage($gradesCollection);

            if ($this->isAchiever($average)) {
                $this->achievers_q4++;
            }
    
            if ($this->isPasser($average)) {
                $this->passers_q4++;
            }
    
            if ($this->isFailure($average)) {
                $this->failures_q4++;
            }
        }
    }

    private function setQuarterThreeStats() {
        $con = connect();
        $teacher = new teacher;
        $advisoryID = $teacher->getHandleSectionID();

        $sql = "SELECT GROUP_CONCAT(grading_period_1) 
                FROM student_grades 
                INNER JOIN subjects 
                ON subjects.subject_id = student_grades.subject_id
                INNER JOIN user_school_info
                ON student_grades.student_id = user_school_info.id 
                WHERE semester = 2 AND section = ?
                GROUP BY student_id";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $advisoryID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($gradesCollection);

        while ($stmt->fetch()) {

            $gradesCollection = explode(',', $gradesCollection);
            $average = $this->getAverage($gradesCollection);

            if ($this->isAchiever($average)) {
                $this->achievers_q3++;
            }
    
            if ($this->isPasser($average)) {
                $this->passers_q3++;
            }
    
            if ($this->isFailure($average)) {
                $this->failures_q3++;
            }
        }
    }

    private function setQuarterTwoStats() {
        $con = connect();
        $teacher = new teacher;
        $advisoryID = $teacher->getHandleSectionID();

        $sql = "SELECT GROUP_CONCAT(grading_period_2) 
                FROM student_grades 
                INNER JOIN subjects 
                ON subjects.subject_id = student_grades.subject_id 
                INNER JOIN user_school_info
                ON student_grades.student_id = user_school_info.id 
                WHERE semester = 1 AND section = ? 
                GROUP BY student_id";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $advisoryID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($gradesCollection);

        while ($stmt->fetch()) {

            $gradesCollection = explode(',', $gradesCollection);
            $average = $this->getAverage($gradesCollection);

            if ($this->isAchiever($average)) {
                $this->achievers_q2++;
            }
    
            if ($this->isPasser($average)) {
                $this->passers_q2++;
            }
    
            if ($this->isFailure($average)) {
                $this->failures_q2++;
            }
        }
    }

    private function setQuarterOneStats() {
        $con = connect();
        $teacher = new teacher;
        $advisoryID = $teacher->getHandleSectionID();

        $sql = "SELECT GROUP_CONCAT(grading_period_1) 
                FROM student_grades 
                INNER JOIN subjects 
                ON subjects.subject_id = student_grades.subject_id 
                INNER JOIN user_school_info
                ON student_grades.student_id = user_school_info.id 
                WHERE semester = 1 AND section = ? 
                GROUP BY student_id";

        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $advisoryID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($gradesCollection);

        while ($stmt->fetch()) {

            $gradesCollection = explode(',', $gradesCollection);
            $average = $this->getAverage($gradesCollection);

            if ($this->isAchiever($average)) {
                $this->achievers_q1++;
            }
    
            if ($this->isPasser($average)) {
                $this->passers_q1++;
            }
    
            if ($this->isFailure($average)) {
                $this->failures_q1++;
            }
        }
    }

    # ISSER

    private function getAverage($gradesCollection) {

        $average = 0;

        for ($i = 0; $i < count($gradesCollection); $i++) { 
            $average += $gradesCollection[$i];
        }

        return $average / count($gradesCollection);
    }

    private function isAchiever($grade) {
        
        if ($grade >= ACHIEVER_GRADE) {
            return 1;
        } else {
            return 0;
        }
    }

    private function isPasser($grade) {
        
        if ($grade >= PASSING_GRADE) {
            return 1;
        } else {
            return 0;
        }
    }

    private function isFailure($grade) {
        
        if ($grade <= FAILING_GRADE) {
            return 1;
        } else {
            return 0;
        }
    }
}

$sectionStatistics = new sectionStatistics;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../styles/query.css">
    <link rel="stylesheet" href="../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/popup.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | <?php echo $user->getFullName(); ?></title>
</head>
<body>

    <section id="main-UI">
        <aside class="sidebar-pane">
            <div class="container-fluid">
                <button class="show-side-bar side-bar-close" type="button"><i class="fas fa-times"></i></button>
                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="<?php echo fetchProfilePicture('../'); ?>" alt="AU">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4><?php echo $user->getFullName(); ?></h4>
                            <p><?php  echo $teacher->getSectionAndGradeLevel() . " Adviser"; ?></p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Administrator</h1>

                            <div class="sidebar-link active-link">
                                <a href="../faculty"><i class="fas fa-home"></i> <span>Teacher Portal</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="viewadvisory"><i class="fas fa-user-plus"></i> <span>Manage Advisory</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="grademanage"><i class="fas fa-award"></i> <span>Manage Grades</span></a>
                            </div>

                            <h1 class="list-title">Personal Settings</h1>

                            <div class="sidebar-link">
                                <a href="../settings"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
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
                            <h1>Teacher Portal</h1>
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
                                    <p>Welcome! You are logged in as an adivser of <?php echo $teacher->getSectionAndGradeLevel(); ?>.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <section id="analytics">
                <div class="container">
                    <h1 class="analytics-title"> <?php echo $teacher->getSectionAndGradeLevel(); ?> Statistics</h1>
                    <p class="analytics-title"> <i class="fad fa-info-circle"></i> The actual statistics may finalized only after giving grades to all advisory class. </p>

                    <!-- FIRST QUARTER -->

                    <div class="quarter-separator">
                        <i class="fad fa-flag"></i>
                        <p> 1st Semester - Prelim [P]</p>
                    </div>

                    <div class="analytics-wrapper">

                        <div data-aos="fade-left" class="analytics-card analytics-accent-blue">
                            <i class="fad fa-trophy fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->achievers_q1; ?> </h1>
                                <p>ACHIEVERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-up" class="analytics-card analytics-accent-green">
                            <i class="fad fa-check fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q1; ?> </h1>
                                <p>PASSERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-red">
                            <i class="fas fa-frown fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->failures_q1; ?> </h1>
                                <p>FAILURES</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-orange">
                            <i class="fad fa-chart-line fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q1 + $sectionStatistics->failures_q1; ?> </h1>
                                <p>TOTAL</p>
                            </div>
                        </div>

                    </div>

                    <!-- SECOND QUARTER -->

                    <div class="quarter-separator">
                        <i class="fad fa-flag"></i>
                        <p>1st Semester - Midterm [M]</p>
                    </div>

                    <div class="analytics-wrapper">

                        <div data-aos="fade-left" class="analytics-card analytics-accent-blue">
                            <i class="fad fa-trophy fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->achievers_q2; ?> </h1>
                                <p>ACHIEVERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-up" class="analytics-card analytics-accent-green">
                            <i class="fad fa-check fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q2; ?> </h1>
                                <p>PASSERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-red">
                            <i class="fas fa-frown fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->failures_q2; ?> </h1>
                                <p>FAILURES</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-orange">
                            <i class="fad fa-chart-line fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q2 + $sectionStatistics->failures_q2; ?> </h1>
                                <p>TOTAL</p>
                            </div>
                        </div>

                    </div>

                    <!-- THIRD QUARTER -->

                    <div class="quarter-separator">
                        <i class="fad fa-flag"></i>
                        <p>2nd Semester - Pre-Finals [PF]</p>
                    </div>

                    <div class="analytics-wrapper">

                        <div data-aos="fade-left" class="analytics-card analytics-accent-blue">
                            <i class="fad fa-trophy fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->achievers_q3; ?> </h1>
                                <p>ACHIEVERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-up" class="analytics-card analytics-accent-green">
                            <i class="fad fa-check fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q3; ?> </h1>
                                <p>PASSERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-red">
                            <i class="fas fa-frown fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->failures_q3; ?> </h1>
                                <p>FAILURES</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-orange">
                            <i class="fad fa-chart-line fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q3 + $sectionStatistics->failures_q3; ?> </h1>
                                <p>TOTAL</p>
                            </div>
                        </div>

                    </div>

                    <!-- FOURTH QUARTER -->

                    <div class="quarter-separator">
                        <i class="fad fa-flag"></i>
                        <p>2nd Semester - Finals [F]</p>
                    </div>

                    <div class="analytics-wrapper">

                        <div data-aos="fade-left" class="analytics-card analytics-accent-blue">
                            <i class="fad fa-trophy fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->achievers_q4; ?> </h1>
                                <p>ACHIEVERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-up" class="analytics-card analytics-accent-green">
                            <i class="fad fa-check fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q4; ?> </h1>
                                <p>PASSERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-red">
                            <i class="fas fa-frown fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->failures_q4; ?> </h1>
                                <p>FAILURES</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-orange">
                            <i class="fad fa-chart-line fa-2x"></i>
                            <div class="analytics-content">
                                <h1> <?php echo $sectionStatistics->passers_q4 + $sectionStatistics->failures_q4; ?> </h1>
                                <p>TOTAL</p>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            <section id="announcement-and-todo">
                <div class="container">
                    <div class="announcement-and-todo-wrapper">
                        <div class="announcement">
                            <div class="announcements-board">
                                <div class="container">
                                    <h1>Announcements</h1>
                                    <?php populateAnnouncementsBoard(); ?>
                                </div>
                            </div>
                        </div>
                        <div class="todo">
                            <div class="container">
                                <div class="todo-wrapper">
                                    <div class="todo-date">
                                        <div>
                                            <h1 class="day-time">AUSMS Clock</h1>
                                            <p class="day-date">Philippine Standard Time</p>
                                        </div>
                                        <div>
                                            <p class="week">Loading</p>
                                        </div>
                                    </div>
                                    <div class="todo-greet">
                                        <h2 data-aos="fade-right"><span class="greet"> Hello </span><span><?php echo $user->getFirstName(); ?>!</span></h2>
                                        <p class="greet-quote"></p>
                                    </div>
                                    <div class="todo-lists">
                                        <div class="todo-lists-top">
                                            <h1>Your activities</h1>
                                        </div>
                                        <form id="new-todo-form" action="" method="post">
                                            <input type="text" name="todo-text" placeholder="Todo content">
                                            <button name="add-todo-activity" type="submit"><i class="fad fa-clipboard-check"></i></button>
                                        </form>
                                        <?php populateTodoActivities(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="activity-monitor">
            <div class="container">
                <div class="activity-monitor-wrapper">
                    <div class="container">
                        <div class="activity-top">
                            <h1>Activity Logs</h1>
                        </div>

                        <table>
                            <tr>
                                <td><h1>NAME</h1></td>
                                <td><h1>ACTIVITY</h1></td>
                                <td><h1>TIME</h1></td>
                                <td></td>
                            </tr>
                            <?php 
                            
                            $activityLog->fetchAllActivityLog();
                            
                            ?>
                        </table>
                        
                    </div>
                </div>
            </div>
            </section>

        </div>
    </section>

</body>
</html>

<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../js/aos/dist/aos.js"></script>
<script src="../js/todo.js"></script>
<script>AOS.init();</script>
<script src="../js/sidebar.js"></script>