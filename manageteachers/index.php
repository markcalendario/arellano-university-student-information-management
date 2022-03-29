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
    
    if (isset($_SESSION['restriction'])) {
        header('location: ../restrict');
    }
}

function showPopups() {
    if (isset($_SESSION['teacherDeactivated'])) {
        showSuccessPopup($_SESSION['teacherDeactivated']. '\'s Account Was Deactivated');
        unset($_SESSION['teacherDeactivated']);
    }

    if (isset($_SESSION['teacherEditOldName']) && $_SESSION['teacherEditNewName']) {
        showSuccessPopup('You have ' . $_SESSION['teacherEditOldName'] . '\'s name into '. $_SESSION['teacherEditNewName']);
        unset($_SESSION['teacherEditOldName']);
        unset($_SESSION['teacherEditNewName']);
    }

    if (isset($_SESSION['teacherEditInvalid'])) {
        showInvalidPopup('Invalid Teacher Name Form Field', 'Please make sure to complete all of the fields needed');
        unset($_SESSION['teacherEditInvalid']);
    }

    if (isset($_SESSION['errorEditTeacher'])) {
        showErrorPopup('Error Editing Teacher\'s Name', 'An internal error occured. Report this immediately.');
        unset($_SESSION['errorEditTeacher']);
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
$paginationCount = initiatePaginationOfActiveTeachers();
$currentPage = getCurrentPage($paginationCount);
$offset = ($currentPage - 1) * recordsPerPage;

function getCurrentPage($paginationCount) {

    if (!isset($_GET['ap'])) {
        return 1;
    }

    if (isset($_GET['ap']) && $_GET['ap'] > 0 && $_GET['ap'] < $paginationCount + 1) {
        return $_GET['ap'];
    } else if ($_GET['ap'] > $paginationCount) {
        return 1;
    } else if ($_GET['ap'] <= 0) {
        return 1;
    } else {
        return 1;
    }
}

function initiatePaginationOfActiveTeachers() {
    $con = connect();

    $stmt = $con->prepare('SELECT
    COUNT(*) AS numberOfActiveTeachers
    FROM user_information
    INNER JOIN user_credentials
    ON user_information.id = user_credentials.id AND user_credentials.usertype = 1 AND user_credentials.account_status = 1');
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc();
    
    $paginationCount = ceil($count['numberOfActiveTeachers'] / recordsPerPage);
    return $paginationCount;
}

function populatePaginationOfActiveTeachers($currentPage, $paginationCount) {

    # NEXT
    if ($currentPage != $paginationCount) {
        $activeAdviserPage = $currentPage;
        ?>
        <a class="control" href="?ap=<?php echo ++$activeAdviserPage; ?>">Next <i class="far fa-angle-right"></i> </a>
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
            <a class="active" href="?ap=<?php echo $i ?>"><?php echo $i; ?></a>
            <?php
        } else {
            ?>
            <a href="?ap=<?php echo $i ?>"><?php echo $i; ?></a>
            <?php
        }
    }
    # PREV
    if ($currentPage != 1) {
        $activeAdviserPage = $currentPage;
        ?>
        <a class="control" href="?ap=<?php echo $activeAdviserPage - 1; ?>"><i class="far fa-angle-left"></i> Prev</a>
        <?php
    } else {
        ?>
        <a href="javascript:void(0)"><i class="far fa-angle-left"></i> Prev</a>
        <?php
    }

}

function showActiveTeachersResult($recordsPerPage, $offset) {
    $con = connect();

    $sql = getSQL();
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ii', $offset, $recordsPerPage);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($tID, $tFirstname, $tLastname);

    while ($stmt->fetch()) {
        ?>
        <div class="active teacher-bar">
            <div class="user-profile-wrap">
                <div class="profile-container">
                    <img src="<?php echo fetchUserProfilePicture(encryptData($tID), '../'); ?>">
                </div>
            </div>

            <form class="teacher-control" method="post">
                <div class="teacher-info">
                    <?php
                        if (isset($_POST['teacher-edit']) && $_POST['teacher-id'] == encryptData($tID)) {
                            ?>
                            <input type="text" name="edited-teacher-firstname" value="<?php echo $tFirstname; ?>" placeholder="First name">
                            <input type="text" name="edited-teacher-lastname" value="<?php echo $tLastname; ?>" placeholder="Last name">
                            <?php
                        } else {
                            ?>
                            <h1><?php echo $tFirstname . ' ' . $tLastname; ?></h1>
                            <?php
                        }
                    ?>

                </div>
                <input type="hidden" name="teacher-id" value="<?php echo encryptData($tID); ?>">
                <input type="hidden" name="teacher-firstname" value="<?php echo encryptData($tFirstname); ?>">
                <input type="hidden" name="teacher-lastname" value="<?php echo encryptData($tLastname); ?>">

                <?php
                    if (isset($_POST['teacher-edit']) && $_POST['teacher-id'] == encryptData($tID)) {
                        ?>
                        <button name="teacher-edit-save" type="submit">
                            <p>Save Name</p>
                        </button>
                        <?php
                    } else {
                        ?>
                        <button name="teacher-edit" type="submit">
                            <i class="fad fa-pen"></i>
                        </button>
                        <?php
                    }
                ?>

                <?php
                    if (isset($_POST['teacher-edit']) && $_POST['teacher-id'] == encryptData($tID)) {
                        ?>
                        <button name="teacher-edit-cancel" type="submit">
                            <p>Cancel Edit</p>
                        </button>
                        <?php
                    } else {
                        ?>
                        <button name="teacher-ban" type="submit">
                            <i class="fad fa-ban"></i>
                        </button>
                        <?php
                    }
                ?>

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
    WHERE user_credentials.account_status = 1 ';
    
    if (isset($_GET['s'])) {
        $validSearch = validateInput($_GET['s']);
        $sql .= 'AND (user_information.lastname LIKE \'%'.$validSearch.'%\' OR user_information.firstname LIKE \'%'.$validSearch.'%\') AND user_credentials.usertype = 1 LIMIT ?,?';
    } else {
        $sql .= 'AND user_credentials.usertype = 1 LIMIT ?,?';
    }
    return $sql ;
}

#
# TEACHER BAN
#

if (isset($_POST['teacher-ban'])) {
    deactivateTeacher();
}

function deactivateTeacher() {
    $con = connect();

    $teacherIDToDeactivate = decryptData($_POST['teacher-id']);
    $stmt = $con->prepare('UPDATE user_credentials SET account_status = 0 WHERE id = ?');
    $stmt->bind_param('i', $teacherIDToDeactivate);
    
    if ($stmt->execute()) {
        $_SESSION['teacherDeactivated'] = decryptData($_POST['teacher-firstname']) . ' ' . decryptData($_POST['teacher-lastname']);
        $activityLog = new activityLog;
        $activityLog->recordActivityLog('deactivated ' . $_SESSION['teacherDeactivated'] . ' \'s account. (teacher)');
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    }
}

#
# TEACHER EDIT
#

if (isset($_POST['teacher-edit-save'])) {
    checkTeacherEditValidity();
}

function checkTeacherEditValidity() {
    if (isTeacherEditInputFieldValid()) {
        saveTeacherEdit();
    } else {
        $_SESSION['teacherEditInvalid'] = true;
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    }
}

function saveTeacherEdit() {
    $con = connect();

    $teacherIDToEdit = decryptData($_POST['teacher-id']);
    $teacherOldName = decryptData($_POST['teacher-firstname']) . " " . decryptData($_POST['teacher-lastname']);

    $teacherNewFirstName = titleCase(validateInput($_POST['edited-teacher-firstname']));
    $teacherNewLastName = titleCase(validateInput($_POST['edited-teacher-lastname']));
    $teacherNewFullName = $teacherNewFirstName . " " . $teacherNewLastName;

    $stmt = $con->prepare('UPDATE user_information SET firstname = ?, lastname = ? WHERE id = ?');
    $stmt->bind_param('ssi', $teacherNewFirstName, $teacherNewLastName, $teacherIDToEdit);
    if ($stmt->execute()) {
        $_SESSION['teacherEditOldName'] = $teacherOldName;
        $_SESSION['teacherEditNewName'] = $teacherNewFullName;
        $activityLog = new activityLog;
        $activityLog->recordActivityLog('changed ' . $_SESSION['teacherEditOldName'] . '\'s name into '. $_SESSION['teacherEditNewName']);
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['errorEditTeacher'] = true;
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    }
}


#
# SEARCH
#

if (isset($_POST['search'])) {
    header('location: ?s='. $_POST['searchTeachers']);
    exit(0);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../styles/query.css">
    <link rel="stylesheet" href="../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../styles/popup.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Manage Teachers</title>
</head>
<body>

    <section id="main-UI">
    <aside class="sidebar-pane">
            <div class="container-fluid">
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
                            <div class="sidebar-link active-link">
                                <a href="../manageteachers"><i class="fas fa-chalkboard-teacher"></i> <span>Manage Teachers</span></a>
                            </div>
                            <div class="sidebar-link ">
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
                            <h1>Manage Teachers</h1>
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

            <section id="search">
                <div class="container">
                    <div class="search-wrapper">
                        <form action="" method="post">
                            <input type="text" name="searchTeachers" placeholder="Search Active Teacher" autocomplete="off">
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
                            <div class="content active-indicator">
                                <div class="active-icon">
                                    <i class="fas fa-flag"></i>
                                </div>
                                <div class="text">
                                    <h3>ACTIVE TEACHERS ACCOUNT</h3>
                                </div>
                            </div>
                        </div>

                        <?php showActiveTeachersResult(recordsPerPage, $offset); ?>

                    </div>

                    <div class="pagination">
                        <?php populatePaginationOfActiveTeachers($currentPage, $paginationCount); ?>
                    </div>
                </div>
            </section>

            <section id="show-inactive-teachers-list">
                <div class="container">
                    <div class="wrapper">
                        <a href="inactive">
                            <i class="fad fa-user-slash"></i>
                            <h3>Show Inactive Teachers</h3>
                        </a>
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