<?php 

session_start();

include '../php/data-encryption.inc.php';
include '../php/inc.php';
include '../php/announcement.php';
include '../php/todo.php';

$user = new user;

function restriction() {
    $user = new user;
    
    function sendToHomePage() {
        header('location: ..');
    }
    
    if (!isset($_SESSION['loggedin'])) {
        sendToHomePage();
    }

    if (!$user->isFaculty()) {
        sendToHomePage();
    }
    
    if (isRestricted()) {
        header('location: ../restrict');
    }
}

function page_init() {
    restriction();
    showPopups();
}

function showPopups() {
    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'invalidPostAnnouncement') {
        showInvalidPopup('Announcement Was Not Posted.', 'Some forms are invalid, try to fill in again.');
        unset($_SESSION['execution']);
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'successPostAnnouncement') {
        showSuccessPopup('The Announcement Posted Successfully.');
        unset($_SESSION['execution']);
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'failedPostAnnouncement') {
        showErrorPopup('An Error Occured.', 'An internal error occured while posting your announcement. Try again later.');
        unset($_SESSION['execution']);
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'failedDeleteAnnouncement') {
        showErrorPopup('An Error Occured.', 'An internal error occured while deleting your announcement. Try again later.');
        unset($_SESSION['execution']);
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'successDeleteAnnouncement') {
        showSuccessPopup('The Announcement Deleted Successfully.');
        unset($_SESSION['execution']);
    }

}

class analyticsOverview {
    
    private $studentCount = 0;
    private $teacherCount = 0;
    private $facultyCount = 0;

    public function __construct() {
        $con = connect();
        $stmt = $con->prepare(
            'SELECT usertype FROM user_credentials
            WHERE account_status = 1'
        );
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($usertype);

        while ($stmt->fetch()) {
            $this->setStudentCount($usertype);
            $this->setTeacherCount($usertype);
            $this->setFacultyCount($usertype);
        }
    }

    public function getFacultyCount() {
        return $this->facultyCount;

    }

    public function getTeacherCount() {
        return $this->teacherCount;

    }

    public function getStudentCount() {
        return $this->studentCount;

    }

    private function setFacultyCount($usertype) {
        if ($usertype == 2) {
            $this->facultyCount++;
        }
    }

    private function setTeacherCount($usertype) {
        if ($usertype == 1) {
            $this->teacherCount++;
        }
    }

    private function setStudentCount($usertype) {
        if ($usertype == 0) {
            $this->studentCount++;
        }
    }

    public function getOverallTotal() {
        return $this->getStudentCount() 
                + $this->getFacultyCount()
                + $this->getTeacherCount();
    }
}

$analyticsOverview = new analyticsOverview;
page_init();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../styles/query.css">
    <link rel="stylesheet" href="../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../styles/popup.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty | <?php echo $user->getFirstName(); ?></title>
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
                            <h4><?php echo $user->getFullName(); ?></h4>
                            <p>Faculty Admin</p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Administrator</h1>

                            <div class="sidebar-link active-link">
                                <a href="../faculty"><i class="fas fa-home"></i> <span>Faculty Portal</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../addstudent"><i class="fas fa-user-plus"></i> <span>Add Students</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../addpersonnel"><i class="fas fa-user-shield"></i> <span>Add Personnel</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../managesections"><i class="fas fa-th-list"></i> <span>Manage Sections</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../managesubjects"><i class="fas fa-book"></i> <span>Manage Subjects</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../managestrands"><i class="fas fa-layer-group"></i> <span>Manage Strands</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../manageteachers"><i class="fas fa-chalkboard-teacher"></i> <span>Manage Teachers</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../inactivestudents"><i class="fas fa-ban"></i> <span>Inactive Students</span></a>
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
                            <h1>Faculty Portal</h1>
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
                                    Welcome! You are logged in as a Faculty Administrator.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <section id="analytics">
                <div class="container">
                    <h1 class="analytics-title">Analytics Overview</h1>
                    <div class="analytics-wrapper">
                        
                        <div data-aos="fade-left" class="analytics-card analytics-accent-blue">
                            <i class="fad fa-users fa-2x"></i>
                            <div class="analytics-content">
                                <h1><?php echo $analyticsOverview->getStudentCount(); ?></h1>
                                <p>STUDENTS</p>
                            </div>
                        </div>

                        <div data-aos="fade-up" class="analytics-card analytics-accent-orange">
                            <i class="far fa-chalkboard fa-2x"></i>
                            <div class="analytics-content">
                                <h1><?php echo $analyticsOverview->getTeacherCount(); ?></h1>
                                <p>TEACHERS</p>
                            </div>
                        </div>

                        <div data-aos="fade-down" class="analytics-card analytics-accent-red">
                            <i class="far fa-crown fa-2x"></i>
                            <div class="analytics-content">
                                <h1><?php echo $analyticsOverview->getFacultyCount(); ?></h1>
                                <p>FACULTY </p>
                            </div>
                        </div>

                        <div data-aos="fade-right" class="analytics-card analytics-accent-green">
                            <i class="fas fa-users-crown fa-2x"></i>
                            <div class="analytics-content">
                                <h1><?php echo $analyticsOverview->getOverallTotal(); ?></h1>
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
                            <form action="" method="post">
                                <button type="submit" name="announce-trigger" class="announcement-poster">
                                    <div class="announcement-poster-content">
                                        <i class="fas fa-plus-circle"></i>
                                        <h1> Post new announcement</h1>
                                    </div>
                                </button>
                            </form>
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
                            <form action="" method="post">
                                <input type="submit" name="trash-all-logs" value="Trash All Logs">
                            </form>
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