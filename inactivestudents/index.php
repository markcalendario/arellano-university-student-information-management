<?php

session_start();
include '../php/inc.php';
include '../php/data-encryption.inc.php';

$user = new user;

function sendToHomePage() {
    header('location: ..');
}

function restrict() {
    $user = new user;

    if (!isset($_SESSION['loggedin'])) {
        sendToHomePage();
    }
    
    if (!$user->isFaculty()) {
        sendToHomePage();
    }
}

function showPopups() {

    if (isset($_SESSION['successUserAccountDelete'])) {
        showSuccessPopup('The Account of ' . $_SESSION['successUserAccountDelete'] . ' Deleted Successfully');
        unset($_SESSION['successUserAccountDelete']);
    }

    if (isset($_SESSION['failedUserAccountDelete'])) {
        showErrorPopup('The Account of ' . $_SESSION['successUserAccountDelete'] . ' Was Not Deleted', 'An internal error occured, report this immediately.');
        unset($_SESSION['failedUserAccountDelete']);
    }

    if (isset($_SESSION['successStudentReactivate'])) {
        showSuccessPopup('Student Activation Success, ' . $_SESSION['successStudentReactivate']);
        unset($_SESSION['successStudentReactivate']);
    }

    if (isset($_SESSION['failedStudentReactivate'])) {
        showErrorPopup('Uh oh!', 'The student cannot be reactivated. An Error Occured.');
        unset($_SESSION['failedStudentReactivate']);
    }

}

function page_init() {
    restrict();
    showPopups();
}

page_init();

#
# PAGE INIT
#

function populateInactiveStudentList() {
    $con = connect();

    $sql = getSql();
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $firstname, $lastname, $middlename, $gender, $lrn);

    while ($stmt->fetch()) {
        ?>
        <form method="post">
            <tr>
                <td> <?php echo $lrn ?> </td>
                <td> <?php echo $lastname . ', ' . $firstname . ' ' . $middlename; ?> </td>
                <td> <?php echo $gender ?> </td>
                <td>
                    <input type="hidden" name="student-id" value="<?php echo encryptData($id); ?>">
                    <input type="hidden" name="student-fullname" value="<?php echo encryptData($firstname . ' ' . $lastname); ?>">
                    <button name="delete-student-prompt" type="submit"> <i class="fas fa-trash"></i> </button>
                </td>
                <td>                  
                    <button name="reactivate-student" type="submit"> <i class="fas fa-undo"></i> </button>
                </td>
            </tr>
        </form>
        <?php
    }

}

function getSql() {

    $allowedSort = array(
        'lrn',
        'lastname',
        'gender'
    );

    $sql = 'SELECT 
            user_information.id,
            user_information.firstname, 
            user_information.lastname, 
            user_information.middlename,
            user_information.gender, 
            user_school_info.lrn
            FROM user_information
            INNER JOIN user_school_info
            ON user_school_info.id = user_information.id
            INNER JOIN user_credentials
            ON user_credentials.id = user_information.id 
            WHERE user_credentials.account_status = 0 
            AND user_credentials.usertype = 0 ';
    
    if (!isset($_GET['a']) && !isset($_GET['q'])) {
        $sql .= ' ORDER BY user_information.lastname';
    } else if (isset($_GET['a']) && in_array($_GET['a'], $allowedSort)) {
        $sql .= ' ORDER BY ' . validateInput($_GET['a']);
    } else {
        $sql .= ' ORDER BY user_information.lastname';
    }

    if (isset($_GET['q'])) {
        $sql .= ' AND user_information.firstname LIKE \'%'.validateInput($_GET['q']).'%\' ';
    }

    return $sql;
}


if (isset($_POST['search'])) {
    header('location: ?q=' . $_POST['search-student']);
}

#
# DELETE STUDENT PERMANENTLY
#

if (isset($_POST['delete-student-prompt'])) {
    showDeleteStudentModal($_POST['student-id'], $_POST['student-fullname']);
}

if (isset($_POST['delete-student-permanently'])) {
    $studentIDToDelete = decryptData($_POST['student-to-delete-id']);
    $studentFullName = decryptData($_POST['student-to-delete-fullname']);

    $isCredentialsDeleted = deleteStudentCredentials($studentIDToDelete);
    $isFamilyBackgroundDeleted = deleteFamilyBackground($studentIDToDelete);
    $isInformationDeleted = deleteStudentInformation($studentIDToDelete);
    $isStudentSchoolInfoDeleted = deleteStudentSchoolInfo($studentIDToDelete);

    if ($isInformationDeleted 
        && $isFamilyBackgroundDeleted
        && $isCredentialsDeleted
        && $isStudentSchoolInfoDeleted) {
        $activityLog = new activityLog;
        $activityLog->recordActivityLog('deleted ' . $studentFullName . '\'s account. (student)');
        $_SESSION['successUserAccountDelete'] = $studentFullName;
        header('location: ../inactivestudents');
        exit(0);
    } else {
        $_SESSION['failedUserAccountDelete'] = true;
        header('location: ../inactivestudents');
        exit(0);
    }
}

function deleteStudentCredentials($sid) {
    $con = connect();

    $stmt = $con->prepare('DELETE FROM user_credentials WHERE id  = ?');
    $stmt->bind_param('i', $sid);

    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

function deleteStudentInformation($sid) {
    $con = connect();

    $stmt = $con->prepare('DELETE FROM user_information WHERE id  = ?');
    $stmt->bind_param('i', $sid);

    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

function deleteFamilyBackground($sid) {
    $con = connect();

    $stmt = $con->prepare('DELETE FROM user_family_background WHERE id  = ?');
    $stmt->bind_param('i', $sid);

    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

function deleteStudentSchoolInfo($sid) {
    $con = connect();

    $stmt = $con->prepare('DELETE FROM user_school_info WHERE id  = ?');
    $stmt->bind_param('i', $sid);

    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

#
# REACTIVATION
#

if (isset($_POST['reactivate-student'])) {
    reactivateStudent();
}

function reactivateStudent() {
    $con = connect();

    $IDStudentIDToReactivate = validateInput(decryptData($_POST['student-id']));
    $stmt = $con->prepare('UPDATE user_credentials 
    SET account_status = 1
    WHERE id = ?');
    $stmt->bind_param('i', $IDStudentIDToReactivate);

    $studentSectionName = getStudentSectionName($IDStudentIDToReactivate);
    
    if ($stmt->execute()) {
        $activityLog = new activityLog;
        $activityLog->recordActivityLog('activated ' . decryptData($_POST['student-fullname']) . '\'s account (student of ' . $studentSectionName . ')');
        $_SESSION['successStudentReactivate'] = decryptData($_POST['student-fullname']) . ' of ' . $studentSectionName;
        header('location: ../inactivestudents/');
        exit(0);
    } else {
        $_SESSION['failedStudentReactivate'] = true;
        header('location: ../inactivestudents/');
        exit(0);
    }

}

function getStudentSectionName($IDStudentIDToReactivate) {
    $con = connect();

    $sectionID = getStudentSectionID($IDStudentIDToReactivate);
    
    $stmt = $con->prepare('SELECT strand_grade, section_name 
    FROM strands
    INNER JOIN sections 
    ON strands.strand_id = sections.strand_id
    WHERE sections.id = ?');
    $stmt->bind_param('i', $sectionID);
    $stmt->execute();
    $stmt->bind_result($strandGrade, $sectionName);
    $stmt->fetch();
    
    return $strandGrade . ' ' . $sectionName; 
}

function getStudentSectionID($IDStudentIDToReactivate) {
    $con = connect();

    $stmt = $con->prepare('SELECT section 
    FROM user_school_info WHERE user_school_info.id = ?');
    $stmt->bind_param('i', $IDStudentIDToReactivate);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($sectionID);
    $stmt->fetch();

    return $sectionID;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../styles/query.css">
    <link rel="stylesheet" href="../styles/popup.css">
    <link rel="stylesheet" href="../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Manage Teachers</title>
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
                            <p>Faculty Admin</p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Administrator</h1>

                            <div class="sidebar-link">
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
                            <div class="sidebar-link active-link">
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
                            <h1>Inactive Students</h1>
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

            <section id="filters">
                <div class="container">
                    <div class="filters-wrapper">
                        <h1>Sort students by</h1>
                        <div data-aos="fade-down" class="filters-content">
                            <a href="?a=lrn">LRN</a>
                            <a href="?a=lastname">Alphabetical</a>
                            <a href="?a=gender">Gender</a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="search">
                <div class="container">
                    <div class="search-wrapper">
                        <form action="" method="post">
                            <input type="text" name="search-student" placeholder="Search student name" autocomplete="off">
                            <div class="search-button-container">
                                <button type="submit" name="search"> <i class="fad fa-search"></i> </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section id="student-list">
                <div class="container">
                    <div class="wrapper">
                        <table>
                            <tr>
                                <th colspan="5">List of Deleted Students</th>
                            </tr>
                            <tr>
                                <th>LRN</th>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>Delete</th>
                                <th>Reactivate</th>
                            </tr>
                            <?php populateInactiveStudentList(); ?>
                        </table>
                    </div>
                </div>
            </section>


        </div>
    </section>
    

</body>
</html>

<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../js/aos/dist/aos.js"></script>
<script>AOS.init();</script>
<script src="../js/sidebar.js"></script>