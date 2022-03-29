<?php

session_start();
include '../php/inc.php';
include '../php/data-encryption.inc.php';

$user = new user;

function restrict() {
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

function showPopups() {

    if (isset($_SESSION['successCreateSubject'])) {
        showSuccessPopup('Subject Created Successfully');
        unset($_SESSION['successCreateSubject']);
    }

    if (isset($_SESSION['errorCreateSubject'])) {
        showErrorPopup('Error Creating a New Subject', 'Internal error occured!');
        unset($_SESSION['errorCreateSubject']);
    }

    if (isset($_SESSION['successEditSubject'])) {
        showSuccessPopup('Subject Edited Successfully');
        unset($_SESSION['successEditSubject']);
    }

    if (isset($_SESSION['errorEditSubject'])) {
        showErrorPopup('Error Editing a Subject', 'Internal error occured!');
        unset($_SESSION['errorEditSubject']);
    }

    if (isset($_SESSION['successDeleteSubject'])) {
        showSuccessPopup('Subject Deleted Successfully');
        unset($_SESSION['successDeleteSubject']);
    }

    if (isset($_SESSION['errorDeleteSubject'])) {
        showErrorPopup('Error Deleting a Subject', 'Internal error occured!');
        unset($_SESSION['errorDeleteSubject']);
    }
    
}

#
# PAGE INITIALIZATION
#

function page_init() {
    showPopups();
    restrict();
}

page_init();

#
# Subject List
#

function getSql() {
    $sql = 'SELECT * FROM subjects  ';

    if (!isset($_GET['s'])) {
        $sql .= ' ORDER BY subject_name ASC';
    }

    if (isset($_GET['s'])) {
        $sql .= 'WHERE subject_name LIKE \'%'.validateInput(decryptData($_GET['s'])).'%\'; ';
    }

    return $sql;
}

function fetchSubjectList() {
    $con = connect();

    $sql = getSql();

    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        ?>
        <form method="post">
            <input type="hidden" name="subject-id" value="<?php echo encryptData($row['subject_id']); ?>">
            <input type="hidden" name="subject-name" value="<?php echo encryptData($row['subject_name']); ?>">
            <tr>
                <?php 
                if (isset($_POST['edit-subject']) && decryptData($_POST['subject-id']) == $row['subject_id']) {
                    ?>
                    <td> <input type="text" name="edit-subject-name" value="<?php echo $row['subject_name']; ?>"> </td>
                    <td> 

                        <select name="edit-subject-semester">
                            <option value="<?php echo encryptData(1); ?>" <?php if ($row['semester'] == 1) { echo 'selected'; } ?>>1st Semester</option>
                            <option value="<?php echo encryptData(2); ?>" <?php if ($row['semester'] == 2) { echo 'selected'; } ?>>2nd Semester</option>
                        </select>

                    </td>
                    <td> 

                        <select name="edit-subject-type">
                            <option value="<?php echo encryptData(1); ?>" <?php if ($row['subject_type'] == 1) { echo 'selected'; } ?>>Specialization</option>
                            <option value="<?php echo encryptData(0); ?>" <?php if ($row['subject_type'] == 0) { echo 'selected'; } ?>>Core</option>
                        </select>

                    </td>
                    
                    <td> <button name="save-subject" type="submit"> <i class='fas fa-save'></i> </button>  </td>
                    <td> <button name="delete-subject-prompt" type="submit"> <i class='fas fa-trash'></i> </button> </td>
                    <?php
                } else {
                    ?>
                    <td> <?php echo $row['subject_name']; ?> </td>
                    <td> <?php echo $row['semester']; ?> </td>
                    <td> <?php echo subjectType($row['subject_type']); ?> </td>
                    <td> <button name="edit-subject" type="submit"> <i class='fas fa-edit'></i> </button>  </td>
                    <?php
                }
            ?>
            </tr>
        </form>
        <?php
    }
}

function subjectType($subjectType) {
    
    if ($subjectType == 1) {
        return 'Specialization';
    } else {
        return 'Core';
    }

}

#
# CREATE SUBJECT
#

if (isset($_POST['add-subject-prompt'])) {
    showAddSubjectModal();
}

if (isset($_POST['create-subject-final'])) {
    if (createSubjectInputAreValid()) {
        createSubject();
    } else {
        showInvalidPopup('Invalid Input', 'Please make sure that all of the required fields are filled out.');
    }
}

if (isset($_POST['cancel'])) {
    header('location: ../managesubjects');
}

function createSubject() {
    $con = connect();
    $activityLog = new activityLog;

    $subjectName = validateInput($_POST['create-subject-name']);
    $subjectType = validateInput(decryptData($_POST['create-subject-type']));
    $subjectSemester = validateInput(decryptData($_POST['create-subject-semester']));

    $stmt = $con->prepare('INSERT INTO subjects (subject_name, subject_type, semester) VALUES (?, ?, ?)');
    $stmt->bind_param('sii', $subjectName, $subjectType, $subjectSemester);

    if ($stmt->execute()) {
        $activityLog->recordActivityLog('created ' . $subjectName . ' subject.');
        $_SESSION['successCreateSubject'] = true;
        header('location: ../managesubjects');
        exit(0);
    } else {
        $_SESSION['errorCreateSubject'] = true;
        header('location: ../managesubjects');
        exit(0);
    }
}

#
# EDIT SUBJECT
#

if (isset($_POST['save-subject'])) {
    if (editSubjectInputAreValid()) {
        saveSubject();
    } else {
        showInvalidPopup('Invalid Input', 'Please make sure that all of the form fields are valid and not empty');
    }
}

function saveSubject() {
    $con = connect();
    $activityLog = new activityLog;

    $subjectID = validateInput(decryptData($_POST['subject-id']));
    $subjectName = validateInput($_POST['edit-subject-name']);
    $subjectSemester = validateInput(decryptData($_POST['edit-subject-semester']));
    $subjectType = validateInput(decryptData($_POST['edit-subject-type']));

    $stmt = $con->prepare('UPDATE subjects SET subject_name = ?, semester = ?, subject_type = ? WHERE subject_id = ?');
    $stmt->bind_param('siii', $subjectName, $subjectSemester, $subjectType, $subjectID);

    if ($stmt->execute()) {
        $activityLog->recordActivityLog('edited ' . $subjectName . ' subject.');
        $_SESSION['successEditSubject'] = true;
        header('location: ../managesubjects');
        exit(0);
    } else {
        $_SESSION['errorEditSubject'] = true;
        header('location: ../managesubjects');
        exit(0);
    }
}

#
# DELETE SUBJECT
#

if (isset($_POST['delete-subject-prompt'])) {
    showDeleteSubjectModal($_POST['subject-id'], $_POST['subject-name']);
}

if (isset($_POST['delete-subject-permanently'])) {
    deleteSubject();
}

function deleteSubject() {
    $con = connect();
    $activityLog = new activityLog;

    $subjectID = validateInput(decryptData($_POST['subject-to-delete-id']));
    $subjectName = decryptData($_POST['subject-to-delete-name']);

    $stmt = $con->prepare('DELETE FROM subjects WHERE subject_id = ?');
    $stmt->bind_param('i', $subjectID);

    if ($stmt->execute()) {
        $activityLog->recordActivityLog('deleted ' . $subjectName . ' subject.');
        $_SESSION['successDeleteSubject'] = true;
        header('location: ../managesubjects');
        exit(0);
    } else {
        $_SESSION['errorDeleteSubject'] = true;
        header('location: ../managesubjects');
        exit(0);
    }
}

#
# SEARCH
#
if (isset($_POST['search'])) {
    header('location: ?s=' . encryptData($_POST['searchSubject']));
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
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Manage Sections</title>
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
                            <div class="sidebar-link active-link">
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
                            <h1>Subjects List</h1>
                        </div>

                        <div class="topper-right">
                            <form method="post">
                                <button class="show-side-bar" type="button"><i class="fas fa-bars"></i></button>
                            </form>
                            <form method="post">
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
                            <input type="text" name="searchSubject" placeholder="Search subject name" value="<?php if (isset($_GET['q'])) { echo $_GET['q']; } ?>" autocomplete="off">
                            <div class="search-button-container">
                                <button type="submit" name="search"> <i class="fad fa-search"></i> </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section id="subjects-list">
                <div class="container">
                    <div class="wrapper">

                        <form id="table-control" method="POST">
                            
                            <button name="add-subject-prompt" type="submit"> <i class="fas fa-plus-circle"></i> Add Subject </button>

                            <?php 
                            
                            if (isset($_POST['edit-subject'])) {
                                ?>
                                <button type="submit" name="cancel"> <i class="fas fa-times"></i> Cancel </button>
                                <?php
                            }

                            if (isset($_GET['s'])) {
                                ?>
                                <button type="submit" name="cancel"> <i class="fas fa-times"></i> Clear Search </button>
                                <?php
                            }

                            ?>

                        </form>

                        <table>
                            <tr>
                                <th> Subject Name </th>
                                <th> Semester </th>
                                <th> Subject Type </th>
                                <th> Edit </th>
                                <?php 
                                    if (isset($_POST['edit-subject'])) {
                                        ?>
                                            <th> Delete </th>
                                        <?php
                                    }
                                ?>
                            </tr>
                            <?php fetchSubjectList(); ?>
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
