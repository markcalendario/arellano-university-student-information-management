<?php 

session_start();

include '../../../php/inc.php';
include '../../../php/data-encryption.inc.php';
include '../../../php/view-student.php';

$user = new user;
$viewStudent = new viewStudent;

function page_init() {
    restrict();
    showPopups();
}

page_init();

function restrict() {
    
    $user = new user;

    if (isset($_SESSION['restriction'])) {
        header('location: ../../../restrict/');
    }
    
    if (!$user->isFaculty()) {
        header('location: ../../../');
    }

    if (!isset($_SESSION['viewStudent'])) {
        header('location: ..');
        exit(0);
    }
}

function showPopups() {
    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'cancelledUpdateStudent') {
        showInvalidPopup('The Student\'s Information Was Not Changed.', 'You have cancelled student update. No information was changed.');
    }
    
    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'invalidUpdateStudent') {
        showInvalidPopup('Some of the Fields are Empty.', 'Make sure that all of the form fields are valid.');
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'successUpdateStudent') {
        showSuccessPopup('The Student\'s Information Was Successfully Updated.');
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'errorUpdateStudent') {
        showErrorPopup('The Student\'s Information Was Not Changed.', 'This is an internal error. Report this immediately.');
    }

    unset($_SESSION['execution']);
}


function populateSectionSelection() {
    $con = connect();

    $stmt = $con->prepare(
        'SELECT * FROM sections
        INNER JOIN strands ON sections.strand_id = strands.strand_id'
    );
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        ?>
        <option value="<?php echo encryptData($row['id']); ?>"><?php echo $row['section_name'] . " - GRADE " . $row['strand_grade'] . " - " . $row['strand_name'] ?></option>
        <?php
    }
}

function populateCurrentSectionSelection() {
    $con = connect();
    
    $viewStudentID = decryptData($_SESSION['viewStudent']);

    $stmt = $con->prepare(
        'SELECT sections.id, sections.section_name, strands.strand_name, strands.strand_grade 
        FROM sections
        INNER JOIN strands ON sections.strand_id = strands.strand_id
        INNER JOIN user_school_info ON user_school_info.section = sections.id
        WHERE user_school_info.id = ?'
    );
    $stmt->bind_param('i', $viewStudentID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($sectionID, $sectionName, $sectionStrandName, $sectionGrade);
    
    while ($row = $stmt->fetch()) {
        ?>
        <option style="background-color: royalblue; color:white;" value="<?php echo encryptData($sectionID); ?>"><?php echo $sectionName . " - GRADE " . $sectionGrade . " - " . $sectionStrandName ?></option>
        <?php
    }
}

if (isset($_POST['edit-student'])) {
    showEditStudentModal();
}

if (isset($_POST['cancel-update-student'])) {
    $_SESSION['execution'] = 'cancelledUpdateStudent';
    header('location: '. $_SERVER['PHP_SELF']);
    exit(0);
}

if (isset($_POST['update-student-info'])) {
    checkBlankInputField();
}

function checkBlankInputField() {
    if (editStudentInputFieldAreNotEmpty()) {
        updateStudentInformation();
    } else {
        $_SESSION['execution'] = 'invalidUpdateStudent';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    }
}

function updateStudentInformation() {
    $con = connect();
    $eStudentID = validateInput(decryptData($_POST['edit-student-id']));
    $eFirstname = titleCase(validateInput($_POST['edit-student-firstname']));
    $eMiddlename = titleCase(validateInput($_POST['edit-student-middlename']));
    $eLastname = titleCase(validateInput($_POST['edit-student-lastname']));
    $eGender = titleCase(validateInput($_POST['edit-student-gender']));
    $eBirthday = titleCase(validateInput($_POST['edit-student-birthday']));
    $eReligion = titleCase(validateInput($_POST['edit-student-religion']));
    $eCountry = titleCase(validateInput($_POST['edit-student-country']));
    $eRegion = titleCase(validateInput($_POST['edit-student-region']));
    $eAddress = titleCase(validateInput($_POST['edit-student-address']));
    $eContact = titleCase(validateInput($_POST['edit-student-contact']));
    $eMotherName = titleCase(validateInput($_POST['edit-student-mother-name']));
    $eMotherWork = titleCase(validateInput($_POST['edit-student-mother-work']));
    $eMotherContact = titleCase(validateInput($_POST['edit-student-mother-contact']));
    $eFatherName = titleCase(validateInput($_POST['edit-student-father-name']));
    $eFatherWork = titleCase(validateInput($_POST['edit-student-father-work']));
    $eFatherContact = titleCase(validateInput($_POST['edit-student-father-contact']));
    $eGuardianName = titleCase(validateInput($_POST['edit-student-guardian-name']));
    $eGuardianContact = titleCase(validateInput($_POST['edit-student-guardian-contact']));
    $eRelationship = titleCase(validateInput($_POST['edit-student-relationship']));
    $eLRN = titleCase(validateInput($_POST['edit-student-lrn']));
    $eSection = titleCase(validateInput(decryptData($_POST['edit-student-section'])));

    $stmt = $con->prepare('UPDATE 
        user_information,
        user_family_background,
        user_school_info
        SET 
        user_information.firstname = ?,
        user_information.middlename = ?,
        user_information.lastname = ?,
        user_information.gender = ?,
        user_information.birthday = ?,
        user_information.religion = ?,
        user_information.country = ?,
        user_information.region = ?,
        user_information.address = ?,
        user_information.contact = ?, 
        user_family_background.mother_fullname = ?,
        user_family_background.mother_work = ?,
        user_family_background.mother_contact = ?,
        user_family_background.father_fullname = ?,
        user_family_background.father_work = ?,
        user_family_background.father_contact = ?,
        user_family_background.guardian_fullname = ?,
        user_family_background.guardian_contact = ?,
        user_family_background.relationship = ?,
        user_school_info.lrn = ?,
        user_school_info.section = ? 
        WHERE user_information.id = ? 
        AND user_family_background.id = ?
        AND user_school_info.id = ?'
    );

    $stmt->bind_param('sssssssssississisisiiiii', 
    $eFirstname, 
    $eMiddlename, 
    $eLastname,
    $eGender,
    $eBirthday,
    $eReligion,
    $eCountry,
    $eRegion,
    $eAddress,
    $eContact,
    $eMotherName,
    $eMotherWork,
    $eMotherContact,
    $eFatherName,
    $eFatherWork,
    $eFatherContact,
    $eGuardianName,
    $eGuardianContact,
    $eRelationship,
    $eLRN,
    $eSection,
    $eStudentID,
    $eStudentID,
    $eStudentID);

    if ($stmt->execute()) {
        $_SESSION['execution'] = 'successUpdateStudent';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['execution'] = 'errorUpdateStudent';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    }
    
}

#
# DEACTIVATE STUDENT
#

if (isset($_POST['deactivate-student'])) {
    deactivateStudent();
}

function deactivateStudent() {
    $con = connect();
    $activityLog = new activityLog;

    $deactivateStudentID = decryptData($_SESSION['viewStudent']);
    $deactivate = 0;

    $stmt = $con->prepare('UPDATE user_credentials SET account_status = ? WHERE id = ?');
    $stmt->bind_param('ii', $deactivate, $deactivateStudentID);
    if ($stmt->execute()) {
        $activityLog->recordActivityLog('deactivated a student\'s account');
        $_SESSION['execution'] = 'deactivatedStudentAccount';
        header('location: ..');
        exit(0);
    } else {
        showErrorPopup('An Error Occured', 'We could not deactivate the student\'s account.');
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../../styles/query.css">
    <link rel="stylesheet" href="../../../styles/popup.css">
    <link rel="stylesheet" href="../../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../../styles/modal.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Student Info</title>
</head>
<body>

    <section id="main-UI">
    <aside class="sidebar-pane">
            <div class="container-fluid">
                <button class="show-side-bar side-bar-close" type="button"><i class="fas fa-times"></i></button>
                
                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="<?php echo fetchProfilePicture('../../../'); ?>">
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
                                <a href="../../../faculty"><i class="fas fa-home"></i> <span>Faculty Portal</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../../addstudent"><i class="fas fa-user-plus"></i> <span>Add Students</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../../addpersonnel"><i class="fas fa-user-shield"></i> <span>Add Personnel</span></a>
                            </div>
                            <div class="sidebar-link active-link">
                                <a href="../../../managesections"><i class="fas fa-th-list"></i> <span>Manage Sections</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../../managesubjects"><i class="fas fa-book"></i> <span>Manage Subjects</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../../managestrands"><i class="fas fa-layer-group"></i> <span>Manage Strands</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../../manageteachers"><i class="fas fa-chalkboard-teacher"></i> <span>Manage Teachers</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../../inactivestudents"><i class="fas fa-ban"></i> <span>Inactive Students</span></a>
                            </div>

                            <h1 class="list-title">Personal Settings</h1>

                            <div class="sidebar-link">
                                <a href="../../../settings"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
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
                            <h1><?php echo $viewStudent->getStudentFirstName(); ?> Information</h1>
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

            <section id="profile-bar">
                <div class="container">
                    <div class="profile-bar-wrapper">
                        <div class="profile-image">
                            <img src="<?php echo fetchUserProfilePicture($_SESSION['viewStudent'], '../../../'); ?>" alt="" srcset="">
                        </div>
                        <div class="profile-basic-info">
                            <h1><?php echo $viewStudent->getStudentlastName() .  ", " . $viewStudent->getStudentFirstName() . " " . $viewStudent->getStudentMiddleName(); ?></h1>
                            <p>LRN: <span class="bold"><?php echo $viewStudent->getStudentLRN(); ?></span></p>
                            <p> Gender: <span class="bold"><?php echo $viewStudent->getStudentGender(); ?></span></p>
                            <p> Age: <span class="bold"><?php echo $viewStudent->getAge(); ?></span></p>
                            <p> Birthday: <span class="bold"><?php echo $viewStudent->getStudentBirthday(); ?></span></p>
                            <form action="" method="post">
                                <button type="submit" name="edit-student"><i class="fad fa-edit"></i> Edit Student</button>
                                <button type="submit" name="deactivate-student"><i class="fas fa-ban"></i> Deactivate Student</button>
                                <button type="button" class="print" onclick="window.print()"> <i class="fad fa-print"></i> Print</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section id="studentinfo">
                <div class="container">
                    <div class="studentinfo-wrapper">
                        <div class="basic-info">
                            <h1>Basic Information</h1>
                            <table>
                                <tr>
                                    <th>Country</th>
                                    <td><?php echo $viewStudent->getStudentCountry(); ?></td>
                                </tr>
                                <tr>
                                    <th>Region</th>
                                    <td><?php echo $viewStudent->getStudentRegion(); ?></td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><?php echo $viewStudent->getStudentAddress(); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact</th>
                                    <td><?php echo $viewStudent->getStudentContact(); ?></td>
                                </tr>
                            </table>
                            <div class="basic-info">
                            <h1>Family Background</h1>
                            <table>
                                <tr>
                                    <th>Mother's Maiden Name</th>
                                    <td><?php echo $viewStudent->getStudentMotherName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Mother's Nature of Work</th>
                                    <td><?php echo $viewStudent->getStudentMotherWork(); ?></td>
                                </tr>
                                <tr>
                                    <th>Mother's Contact</th>
                                    <td><?php echo $viewStudent->getStudentMotherContact(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Full Name</th>
                                    <td><?php echo $viewStudent->getStudentFatherName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Nature of Work</th>
                                    <td><?php echo $viewStudent->getStudentFatherContact(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Nature of Work</th>
                                    <td><?php echo $viewStudent->getStudentFatherWork(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Contact</th>
                                    <td><?php echo $viewStudent->getStudentFatherContact(); ?></td>
                                </tr>
                                <tr>
                                    <th>Guardian's Full Name</th>
                                    <td><?php echo $viewStudent->getStudentGuardianName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Guardian's Contact</th>
                                    <td><?php echo $viewStudent->getStudentGuardianContact(); ?></td>
                                </tr>
                            </table>
                            <div class="basic-info">
                            <h1>School Record</h1>
                            <table>
                                <tr>
                                    <th>Section</th>
                                    <td><?php echo $viewStudent->getStudentSectionName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Grade Level</th>
                                    <td><?php echo $viewStudent->getStudentGrade(); ?></td>
                                </tr>
                                <tr>
                                    <th>Strand</th>
                                    <td><?php echo $viewStudent->getStudentStrand(); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </section>


        </div>
    </section>
    

</body>
</html>

<script src="../../../js/jquery-3.5.1.min.js"></script>
<script src="../../../js/aos/dist/aos.js"></script>
<script>AOS.init();</script>
<script src="../../../js/sidebar.js"></script>