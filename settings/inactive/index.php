<?php 

session_start();
include '../../php/inc.php';
include '../../php/data-encryption.inc.php';

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
    
    if (isset($_SESSION['restriction'])) {
        header('location: ../../restrict');
    }
}

function showPopups() {
    if (isset($_SESSION['teacherReactivated'])) {
        showSuccessPopup($_SESSION['teacherReactivated']. '\'s Account Was Reactivated');
        unset($_SESSION['teacherReactivated']);
    }

    if (isset($_SESSION['failedReactivation'])) {
        showInvalidPopup('Reactivation Failed','Try again later.');
        unset($_SESSION['failedReactivation']);
    }

    if (isset($_SESSION['teacherEditInvalid'])) {
        showInvalidPopup('Invalid Teacher Name Form Field', 'Please make sure to complete all of the fields needed');
        unset($_SESSION['teacherEditInvalid']);
    }

    if (isset($_SESSION['errorEditTeacher'])) {
        showErrorPopup('Error Editing Teacher\'s Name', 'An internal error occured. Report this immediately.');
        unset($_SESSION['errorEditTeacher']);
    }

    if (isset($_SESSION['successDeleteTeacher'])) {
        showSuccessPopup($_SESSION['successDeleteTeacher'] . '\'s Account Was Deleted Permanently.');
        unset($_SESSION['successDeleteTeacher']);
    }

    if (isset($_SESSION['failedDeleteTeacher'])) {
        showErrorPopup('Error Deleting Teacher\'s Account', 'An internal error occured. Report this immediately.');
        unset($_SESSION['failedDeleteTeacher']);
    }

}

function page_init() {
    restrict();
    showPopups();
}

page_init();

#
# MANAGE TEACHERS
#

define('recordsPerPage', 10);
$paginationCount = initiatePaginationOfInactiveTeachers();
$currentPage = getCurrentPage($paginationCount);
$offset = ($currentPage - 1) * recordsPerPage;

function getCurrentPage($paginationCount) {

    if (!isset($_GET['inp'])) {
        return 1;
    }

    if (isset($_GET['inp']) && $_GET['inp'] > 0 && $_GET['inp'] < $paginationCount + 1) {
        return $_GET['inp'];
    } else if ($_GET['inp'] > $paginationCount) {
        return 1;
    } else if ($_GET['inp'] <= 0) {
        return 1;
    } else {
        return 1;
    }
}

function initiatePaginationOfInactiveTeachers() {
    $con = connect();

    $stmt = $con->prepare('SELECT
    COUNT(*) AS numberOfInactiveTeachers
    FROM user_information
    INNER JOIN user_credentials
    ON user_information.id = user_credentials.id AND user_credentials.usertype = 1 AND user_credentials.account_status = 0');
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    $paginationCount = ceil($count['numberOfInactiveTeachers'] / recordsPerPage);
    return $paginationCount;
}

function populatePaginationOfInactiveTeachers($currentPage, $paginationCount) {

    # NEXT
    if ($currentPage != $paginationCount) {
        $inactiveAdviserPage = $currentPage;
        ?>
        <a class="control" href="?inp=<?php echo ++$inactiveAdviserPage; ?>">Next <i class="far fa-angle-right"></i> </a>
        <?php
    } else {
        ?>
        <a href="javascript:void(0)">Next <i class="far fa-angle-right"></i> </a>
        <?php     
    }
    # PAGES
    for ($i=1; $i <= $paginationCount; $i++) { 
        if ($i == $currentPage) {
            ?>
            <a class="active" href="?inp=<?php echo $i ?>"><?php echo $i; ?></a>
            <?php
        } else {
            ?>
            <a href="?inp=<?php echo $i ?>"><?php echo $i; ?></a>
            <?php
        }
    }
    # PREV
    if ($currentPage != 1) {
        $inactiveAdviserPage = $currentPage;
        ?>
        <a class="control" href="?inp=<?php echo $inactiveAdviserPage - 1; ?>"><i class="far fa-angle-left"></i> Prev</a>
        <?php
    } else {
        ?>
        <a href="javascript:void(0)"><i class="far fa-angle-left"></i> Prev</a>
        <?php
    }

}

function showInactiveTeachersResult($recordsPerPage, $offset) {
    $con = connect();

    $sql = getSQL();
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ii', $offset, $recordsPerPage);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($tID, $tFirstname, $tLastname);

    while ($stmt->fetch()) {
        ?>
        <div class="inactive teacher-bar">
            <div class="user-profile-wrap">
                <div class="profile-container">
                    <img src="../../userfiles/profilePictures/default.jpg">
                </div>
            </div>

            <form class="teacher-control" method="post">
                <div class="teacher-info">
                    <h1><?php echo $tFirstname . ' ' . $tLastname; ?></h1>
                </div>

                <input type="hidden" name="teacher-id" value="<?php echo encryptData($tID); ?>">
                <input type="hidden" name="teacher-firstname" value="<?php echo encryptData($tFirstname); ?>">
                <input type="hidden" name="teacher-lastname" value="<?php echo encryptData($tLastname); ?>">

                <button name="teacher-reactivate" type="submit">
                    <i class="fad fa-thumbs-up"></i>
                </button>

                <button name="teacher-delete" type="submit">
                    <i class="fad fa-trash"></i>
                </button>

            </form>
        </div>
        <?php
    }
}

function getSQL() {

    $sql = 'SELECT
    user_information.id, 
    user_information.firstname,
    user_information.lastname
    FROM user_information
    INNER JOIN user_credentials
    ON user_information.id = user_credentials.id
    WHERE user_credentials.account_status = 0 AND user_credentials.usertype = 1 ';
    
    if (isset($_GET['s'])) {
        $validSearch = validateInput($_GET['s']);
        $sql .= 'AND user_information.lastname LIKE \'%'.$validSearch.'%\' OR user_information.firstname LIKE \'%'.$validSearch.'%\' LIMIT ?,?';
    } else {
        $sql .= 'LIMIT ?,?';
    }

    return $sql;
}

#
# TEACHER BAN
#

if (isset($_POST['teacher-reactivate'])) {
    reactivateTeacher();
}

function reactivateTeacher() {
    $con = connect();

    $teacherIDToReactivate = decryptData($_POST['teacher-id']);
    $stmt = $con->prepare('UPDATE user_credentials SET account_status = 1 WHERE id = ?');
    $stmt->bind_param('i', $teacherIDToReactivate);
    
    if ($stmt->execute()) {
        $_SESSION['teacherReactivated'] = decryptData($_POST['teacher-firstname']) . ' ' . decryptData($_POST['teacher-lastname']);
        $activityLog = new activityLog;
        $activityLog->recordActivityLog('reactivated ' . $_SESSION['teacherReactivated'] . ' \'s account. (teacher)');
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['failedReactivation'] = true;
        header('location: ../inactive');
        exit(0);
    }
}

#
# TEACHER DELETE
#

if (isset($_POST['teacher-delete'])) {
    $teacherFirstname = $_POST['teacher-firstname'];
    $teacherLastname = $_POST['teacher-lastname'];
    showTeacherDeletePromptModal($teacherFirstname, $teacherLastname, $_POST['teacher-id']);
}

if (isset($_POST['delete-teacher-permanently'])) {
    deleteTeacherAccountPermanently();
}

function deleteTeacherAccountPermanently() {
    $con = connect();

    $teacherIDToDelete = decryptData($_POST['teacher-to-delete-id']);
    $teacherFullNameToDelete = decryptData($_POST['teacher-to-delete-firstname']) . ' ' . decryptData($_POST['teacher-to-delete-lastname']);

    $stmt = $con->prepare('DELETE FROM user_credentials WHERE user_credentials.id = ?');

    $stmt->bind_param('i', $teacherIDToDelete);
    if ($stmt->execute()
        && deleteTeacherInformationPermanently($teacherIDToDelete)
        && deleteTeacherFamilyBackgroundPermanently($teacherIDToDelete)) {

        $activityLog = new activityLog;
        $activityLog->recordActivityLog('deleted ' .$teacherFullNameToDelete . ' \'s account.');

        $_SESSION['successDeleteTeacher'] = $teacherFullNameToDelete;
        header('location: ../inactive');
        exit(0);
    } else {
        $_SESSION['failedDeleteTeacher'] = true;
        header('location: ../inactive');
    }

}

function deleteTeacherInformationPermanently($teacherIDToDelete) {
    $con = connect();

    $stmt = $con->prepare('DELETE FROM user_information WHERE user_information.id = ?');

    $stmt->bind_param('i', $teacherIDToDelete);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

function deleteTeacherFamilyBackgroundPermanently($teacherIDToDelete) {
    $con = connect();

    $stmt = $con->prepare('DELETE FROM user_family_background WHERE user_family_background.id = ?');
    $stmt->bind_param('i', $teacherIDToDelete);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}

#
# SEARCH
#

if (isset($_POST['search'])) {
    header('location: ' . $_SERVER['PHP_QUERY'] . ' ?s='. $_POST['searchTeachers']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../styles/query.css">
    <link rel="stylesheet" href="../../styles/modal.css">
    <link rel="stylesheet" href="../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../styles/popup.css">
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
                            <div class="sidebar-link">
                                <a href="../../managesections"><i class="fas fa-th-list"></i> <span>Manage Sections</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../managesubjects"><i class="fas fa-book"></i> <span>Manage Subjects</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../managestrands"><i class="fas fa-layer-group"></i> <span>Manage Strands</span></a>
                            </div>
                            <div class="sidebar-link active-link">
                                <a href="../../manageteachers"><i class="fas fa-chalkboard-teacher"></i> <span>Manage Teachers</span></a>
                            </div>
                            <div class="sidebar-link ">
                                <a href="../../inactivestudents"><i class="fas fa-ban"></i> <span>Inactive Students</span></a>
                            </div>

                            <h1 class="list-title">Personal Settings</h1>

                            <div class="sidebar-link">
                                <a href="../../faculty"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../faculty"><i class="fas fa-user-cog"></i> <span>Personalization</span></a>
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
                            <h1>Manage Teachers</h1>
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

            <section id="search">
                <div class="container">
                    <div class="search-wrapper">
                        <form action="" method="post">
                            <input type="text" name="searchTeachers" placeholder="Search personnel by surname" autocomplete="off">
                            <div class="search-button-container">
                                <button type="submit" name="search"> <i class="fad fa-search"></i> </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section id="teachers-list">
                <div class="container">
                    <div class="teachers-list-wrapper">

                        <div class="indicator">
                            <div class="content inactive-indicator">
                                <div class="inactive-icon">
                                    <i class="fas fa-flag"></i>
                                </div>
                                <div class="text">
                                    <h3>INACTIVE TEACHERS ACCOUNT</h3>
                                </div>
                            </div>
                        </div>

                        <?php showInactiveTeachersResult(recordsPerPage, $offset); ?>

                    </div>

                    <div class="pagination">
                        <?php populatePaginationOfInactiveTeachers($currentPage, $paginationCount); ?>
                    </div>
                </div>
            </section>

            <section id="show-active-teachers-list">
                <div class="container">
                    <div class="wrapper">
                        <a href="..">
                            <i class="fad fa-user-check"></i>
                            <h3>Show Active Teachers</h3>
                        </a>
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