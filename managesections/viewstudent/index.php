<?php
session_start();
include '../../php/inc.php';
include '../../php/data-encryption.inc.php';

$user = new user;

function restrict() {
    $user = new user;

    if (!$user->isFaculty()) {
        header('location: ../../');
    }
    
    if (!isset($_SESSION['loggedin'])) {
        header('location: ../../');
    }

    if (!isset($_SESSION['viewSection'])) {
        $_SESSION['execution'] = 'noSetSection';
        header('location: ..');
        exit(0);
    }

    if (isRestricted()) {
        header('location: ../../restrict');
    }
}

function showPopups() {
    if (isset($_SESSION['execution']) == 'deactivatedStudentAccount') {
        showSuccessPopup('The Student Account Was Deactivated');
        unset($_SESSION['execution']);
    }
}

function page_init() {
    
    showPopups();
    restrict();

}

page_init();

#
# CONSTRUCTION OF WHOLE PAGE'S INFORMATION
#

$viewSection = new viewSection;
class viewSection{

    private $viewSectionID;
    private $sectionName;
    private $sectionStudentsCount;
    private $sectionAdviserID;
    private $sectionAdviserName;

    public function __construct() {
        $this->setViewSectionID();
        $sectionID = $this->getViewSectionID();

        $con = connect();

        $stmt = $con->prepare('SELECT * FROM sections WHERE id = ?');
        $stmt->bind_param('i', $sectionID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $this->setSectionName($row['section_name']);
        $this->setSectionAdviserID($row['adviser_id']);
        $this->setSectionAdviserName($row['adviser_id']);
        $this->setSectionStudentCount($sectionID);
    }

    public function getSectionStudentCount() {
        return $this->sectionStudentsCount;
    }

    private function setSectionStudentCount($sectionID) {
        $con = connect();

        $stmt = $con->prepare('SELECT * FROM user_school_info
        INNER JOIN user_credentials
        ON user_credentials.id = user_school_info.id 
        WHERE section = ? AND account_status = 1');
        
        $stmt->bind_param('i', $sectionID);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->sectionStudentsCount = $result->num_rows;
    }

    public function getSectionAdviserName() {
        return $this->sectionAdviserName;
    }

    private function setSectionAdviserName($sectionAdviserID) {
        $con = connect();

        $stmt = $con->prepare('SELECT 
            firstname, 
            lastname 
            FROM user_information
            WHERE id = ?'
        );
        $stmt->bind_param('i', $sectionAdviserID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($firstName, $lastName);
        $stmt->fetch();

        $this->sectionAdviserName = $firstName . " " . $lastName;
    }

    public function getSectionAdviserID() {
        return $this->sectionAdviserID;
    }

    private function setSectionAdviserID($sectionAdviserID) {
        $this->sectionAdviserID = $sectionAdviserID;

    }

    public function getSectionName() {
        return $this->sectionName;
    }

    private function setSectionName($section) {
        $this->sectionName = $section;
    }

    public function getViewSectionID() {
        return $this->viewSectionID;
    }

    private function setViewSectionID() {
        if (isset($_SESSION['viewSection'])) {
            $this->viewSectionID = validateInput(decryptData($_SESSION['viewSection']));
        } else {
            $this->viewSectionID = null;
        }
    }
    
}

function populateStudentListTable() {
    $con = connect();

    $viewSectionID = validateInput(decryptData($_SESSION['viewSection']));

    $sql = '';

    if (isset($_GET['q'])) {
        $sql = getSearchSql();
    } else {
        $sql = getOrderedSql();
    }

    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $viewSectionID);

    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($studentID, $firstName, $middleName, $lastName, $gender);
    
    while ($stmt->fetch()) {
        ?>
        <form action="" method="post">
            <tr>
                <td><?php echo $lastName; ?></td>
                <td><?php echo $firstName; ?></td>
                <td><?php echo $middleName; ?></td>
                <td><?php echo $gender; ?></td>
                <input type="hidden" name="student-id" value="<?php echo encryptData($studentID); ?>">
                <td><button name="view-student"><i class="fas fa-eye"></i></button></td>
            </tr>
        </form>
        <?php
    }
}

function getSearchSql() {
    $sql = 'SELECT user_information.id, firstname, middlename, lastname, gender 
    FROM user_information
    INNER JOIN user_school_info
    ON user_information.id = user_school_info.id
    INNER JOIN user_credentials
    ON user_credentials.id = user_information.id
    WHERE user_school_info.section = ? 
    AND account_status = 1 
    AND user_information.lastname 
    LIKE \'%'.validateInput($_GET['q']).'%\'';

    return $sql;
}

function getOrderedSql() {

    $allowedSortValues = array(
        'firstname',
        'lastname',
        'gender',
        'random',
    );

    $orderBy = '';

    if (isset($_GET['order'])) {
        $orderBy = validateInput($_GET['order']);
    }

    if (in_array($orderBy, $allowedSortValues)) {
        $orderBy = $orderBy;

    } else {
        $orderBy = 'lastname';
    }

    if ($orderBy === 'random') {
        $orderBy = 'RAND()';
    }

    $sql = 'SELECT user_information.id, firstname, middlename, lastname, gender 
    FROM user_information
    INNER JOIN user_school_info
    ON user_information.id = user_school_info.id
    INNER JOIN user_credentials
    ON user_credentials.id = user_information.id
    WHERE user_school_info.section = ? 
    AND account_status = 1 
    ORDER BY '.$orderBy;

    return $sql;
}

if (isset($_POST['search'])) {
    header('location: ?q='.$_POST['searchStudent']);
}

#
# VIEW STUDENT
#

if (isset($_POST['view-student'])) {
    $_SESSION['viewStudent'] = $_POST['student-id'];
    header("location: student");
    exit(0);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../styles/query.css">
    <link rel="stylesheet" href="../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../styles/popup.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | View List of Students</title>
</head>
<body>

    <section id="main-UI">
    <aside class="sidebar-pane">
            <div class="container-fluid">
            <button class="show-side-bar side-bar-close" type="button"><i class="fas fa-times"></i></button>
                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="../../assets/images/AULOGO.png" alt="AU">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4><?php echo $user->getFullName(); ?></h4>
                            <p>Faculty Admin</p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Administrator</h1>

                            <div class="sidebar-link">
                                <a href="../../faculty"><i class="fas fa-home"></i> <span>Faculty Portal</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../addstudent"><i class="fas fa-user-plus"></i> <span>Add Students</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../addpersonnel"><i class="fas fa-user-shield"></i> <span>Add Personnel</span></a>
                            </div>
                            <div class="sidebar-link active-link">
                                <a href="../../managesections"><i class="fas fa-th-list"></i> <span>Manage Sections</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../managesubjects"><i class="fas fa-book"></i> <span>Manage Subjects</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../managestrands"><i class="fas fa-layer-group"></i> <span>Manage Strands</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../manageteachers"><i class="fas fa-chalkboard-teacher"></i> <span>Manage Teachers</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../inactivestudents"><i class="fas fa-ban"></i> <span>Inactive Students</span></a>
                            </div>

                            <h1 class="list-title">Personal Settings</h1>

                            <div class="sidebar-link">
                                <a href="../faculty"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../faculty"><i class="fas fa-user-cog"></i> <span>Personalization</span></a>
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
                            <h1><?php echo $viewSection->getSectionName(); ?> Students List</h1>
                        </div>

                        <div class="topper-right">
                            <form action="" method="post">
                                <button class="show-side-bar" type="button"><i class="fas fa-bars"></i></button>
                            </form>
                            <form action="" method="post">
                                <button id="message" type="submit"><i class="fad fa-envelope"></i></button>
                            </form>
                            <form action="" method="post">
                                <button name="signout" type="submit"><i class="fad fa-sign-out"></i></button>
                            </form>
                        </div>

                    </div>
                </div>
            </nav>

            <section id="filters">
                <div class="container">
                    <div class="filters-wrapper">
                        <h1>Sort students by</h1>
                        <div data-aos="fade-down" class="filters-content">
                            <a href="?order=lastname">Last Name</a>
                            <a href="?order=firstname">First Name</a>
                            <a href="?order=gender">Gender</a>
                            <a href="?order=random">Random</a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="search">
                <div class="container">
                    <div class="search-wrapper">
                        <form action="" method="post">
                            <input type="text" name="searchStudent" placeholder="Search student name" autocomplete="off">
                            <div class="search-button-container">
                                <button type="submit" name="search"> <i class="fad fa-search"></i> </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section id="adviser">
                <div class="container">
                    <div class="wrapper">
                        <h1>Adviser: <?php echo $viewSection->getSectionAdviserName(); ?></h1>
                    </div>
                </div>
            </section>

            <section id="student-list">
                <div class="container">
                    <div class="student-list-wrapper">
                        <table>

                            <tr>
                                <th colspan="5">
                                    <?php echo $viewSection->getSectionStudentCount(); ?> TOTAL STUDENTS
                                </th>
                            </tr>

                            <?php 

                                if (isset($_GET['q'])) {
                                    ?>
                                    <tr>
                                        <td colspan="5">SEARCHING SURNAME BY KEYWORD: <strong> <?php echo $_GET['q']; ?></strong></td>
                                    </tr>
                                    <?php
                                }

                                if (isset($_GET['order'])) {
                                    ?>
                                    <tr>
                                        <td colspan="5">SHOWING LIST BY: <strong> <?php echo $_GET['order']; ?></strong></td>
                                    </tr>
                                    <?php
                                }

                            ?>

                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Gender</th>
                                <th>Action</th>
                            </tr>
                            <?php populateStudentListTable(); ?>

                        </table>
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