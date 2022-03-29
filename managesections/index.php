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
    if (isset($_SESSION['execution']) && $_SESSION['execution'] == "success") {
        showSuccessPopup("Section created successfully");
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == "error") {
        showErrorPopup("Something went wrong", "This maybe a system problem, report this immediately.");
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == "invalid") {
        showInvalidPopup("Forms are invalid or empty.", "Make sure that the input are valid and not empty.");
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == "duplicationOccured") {
        showInvalidPopup("The section name is already registered.", "Make sure that the section name is authentic.");
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == "sectionEdited") {
        showSuccessPopup("The Section's Information Edited Successfully.");
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == "noSetSection") {
        showInvalidPopup("Please choose section first.", "");
    }

    unset($_SESSION['execution']);
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
# SELECT ADVISER POPULATE
#


function populateAddSectionAdvisersOption() {
    $con = connect();

    $adviserUserType = 1;
    $stmt = $con->prepare('SELECT user_credentials.id, firstname, lastname 
    FROM user_credentials 
    INNER JOIN user_information 
    ON user_information.id = user_credentials.id
    WHERE user_credentials.usertype = ? 
    AND user_credentials.account_status = 1');

    $stmt->bind_param('i', $adviserUserType);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $firstname, $lastname);

    while ($row = $stmt->fetch()) {
        if (adviserDoesntHoldingSection($id, $con)) {
            ?>
            <option value="<?php echo encryptData($id); ?>"><?php echo $firstname . " " . $lastname ?></option>
            <?php
        }
    }

}

function adviserDoesntHoldingSection($id, $con) {
    $stmt = $con->prepare('SELECT adviser_id FROM sections WHERE adviser_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return 1;
    } else {
        return 0;
    }
}

#
# SELECT STRAND POPULATION
#

function populateStrandSelectOption() {
    $con = connect();

    $stmt = $con->prepare('SELECT strand_id, strand_name, strand_grade FROM strands ORDER BY strand_grade');
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $strandName, $strandGrade);

    while ($row = $stmt->fetch()) {
        ?>
            <option value="<?php echo encryptData($id); ?>"><?php echo "[Grade $strandGrade]" . " " . $strandName ?></option>
        <?php
    }

}

#
# ADD NEW SECTION
#

if (isset($_POST['add-new-section-prompt'])) {

    if (createSectionInputAreValid()) {
        checkDuplicationSectionName();
    } else {
        $_SESSION['execution'] = "invalid";
        header('location: ?invalid');
        exit(0);
    }

}

if (isset($_POST['cancel-create-section'])) {
    header('location: ?cancelCreateSection');
}

function checkDuplicationSectionName() {
    
    if (hasNoDuplicateSectionName()) {

        showCreateNewSectionModal(
            upperCase(validateInput($_POST['create-section-name'])),
            validateInput($_POST['create-section-strand']),
            validateInput($_POST['create-section-adviser'])
        );

    } else {
        $_SESSION['execution'] = "duplicationOccured";
        header('location: ?duplicationOccured');
        exit(0);
    }
}


function hasNoDuplicateSectionName() {
    $con = connect();

    $sectionName = upperCase(validateInput($_POST['create-section-name']));
    $stmt = $con->prepare('SELECT section_name FROM sections WHERE section_name = ?');
    $stmt->bind_param('s', $sectionName);
    $stmt->execute();
    $stmt->store_result();
    $hasNoDuplication = $stmt->num_rows;

    if ($hasNoDuplication < 1) {
        return 1;
    } else {
        return 0;
    }
}


if (isset($_POST['create-section'])) {
    createSection();
}

function createSection() {
    $con = connect();
    $activityLog = new activityLog;

    $adviserID = validateInput(decryptData($_POST['create-section-adviser']));
    $strandID = validateInput(decryptData($_POST['create-section-strand']));

    $stmt = $con->prepare('INSERT INTO sections 
            (section_name, strand_id, adviser_id)
            VALUES (?, ?, ?)');
    $stmt->bind_param('sii', $_POST['create-section-name'], $strandID, $adviserID);
    
    if ($stmt->execute()) {
        $activityLog->recordActivityLog('created a new section.');
        $_SESSION['execution'] = "success";
        header('location: ?success');
        exit(0);
    } else {
        $_SESSION['execution'] = "error";
        header('location: ?error');
        exit(0);
    }
}


#
# POPULATE SECTION CARDS
#

function populateSectionCards() {
    $con = connect();
    $sql = '';

    if (!isset($_GET['q'])) {
        $sql = getSQLWithOrder();
    } else {
        $sql = getSQLWithSearch();
    }

    $stmt = $con->prepare($sql);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($sectionID, $sectionName, $sectionStrandID, $adviserID, $sectionStatus, $strandName, $strandGrade, $firstname, $lastname);

    while ($stmt->fetch()) {
        ?>
        <div class="section-card <?php if ($sectionStatus == 1) { echo "section-active"; } else { echo "section-inactive"; } {
            # code...
        } ?>">
            <h5><?php echo "GRADE " . $strandGrade ?></h5>
            <h1><?php echo $sectionName; ?></h1>
            <div class="section-info">
                <p><?php echo $strandName; ?></p>
                <p><?php echo $firstname . " " . $lastname; ?></p>
            </div>
            <form action="" method="post">
                <input type="hidden" name="section-id" value="<?php echo encryptData($sectionID); ?>">
                <button name="section-view" type="submit">
                    <i class="fad fa-eye"></i>
                </button>
                <button name="section-edit" type="submit">
                    <i class="fad fa-pen"></i>
                </button>
            </form>
        </div>
        <?php
    }
}

function getSqlWithOrder() {

    $sql = 'SELECT sections.id, section_name, sections.strand_id, adviser_id, section_status, strand_name, strand_grade, firstname, lastname 
    FROM sections 
    INNER JOIN strands
    ON strands.strand_id = sections.strand_id
    INNER JOIN user_information
    ON user_information.id = sections.adviser_id ';

    if (!isset($_GET['order'])) {
        $sql .= 'ORDER BY id';
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'strandName') {
        $sql .= 'ORDER BY strand_name';
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'adviserName') {
        $sql .= 'ORDER BY firstname';
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'sectionName') {
        $sql .= 'ORDER BY section_name';
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'id') {
        $sql .= 'ORDER BY id';
    }
    else if (isset($_GET['order']) && $_GET['order'] == 'gradeLevel') {
        $sql .= 'ORDER BY strand_grade';
    }

    else if (isset($_GET['order']) && $_GET['order'] == 'sectionStatus') {
        $sql .= 'ORDER BY section_status DESC';
    }

    else {
        $sql .= 'ORDER BY id';
    }

    return $sql;
}

function getSqlWithSearch() {

    $sql = 'SELECT sections.id, section_name, sections.strand_id, adviser_id, section_status, strand_name, strand_grade, firstname, lastname 
    FROM sections 
    INNER JOIN strands
    ON strands.strand_id = sections.strand_id
    INNER JOIN user_information
    ON user_information.id = sections.adviser_id ';

    if (isset($_GET['q'])) {
        $sql .= ' WHERE section_name LIKE \'%'.validateInput($_GET['q']).'%\'';
    }
    
    return $sql;
}

if (isset($_POST['section-delete'])) {
    showDeleteSectionModal();
}

if (isset($_POST['cancel-delete'])) {
    header('location: ?cancelDelete');
}

if (isset($_POST['search'])) {
    header('location: ?q=' . upperCase(titleCase($_POST['searchSection'])));
}

#
# EDIT SECTION
#

if (isset($_POST['section-edit'])) {
    fetchAllSectionInformation();
}

if (isset($_POST['cancel-edit-section'])) {
    header('location: ?cancelSectionEdit');
}

function fetchAllSectionInformation() {
    $con = connect();

    $sectionID = validateInput(decryptData($_POST['section-id']));
    $stmt = $con->prepare('SELECT * FROM sections WHERE id = ?');
    $stmt->bind_param('i', $sectionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    showSectionInformationEditorModal(encryptData($row['id']), $row['section_name'], $row['strand_id'], $row['adviser_id'], $row['section_status']);
}

function getCurrentStrandSelectOption($sectionStrandID) {
    $con = connect();

    $stmt = $con->prepare('SELECT * FROM strands WHERE strand_id = ?');
    $stmt->bind_param('i', $sectionStrandID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    ?>
    <option class='highlight' value="<?php echo encryptData($row['strand_id']); ?>"><?php echo "[Grade " . $row['strand_grade']. "] " . $row['strand_name']; ?></option>
    <?php

}

function getCurrentSectionAdviserSelectOption($adviserID) {
    $con = connect();

    $stmt = $con->prepare('SELECT firstname, lastname FROM user_information WHERE id = ?');
    $stmt->bind_param('i', $adviserID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($firstname, $lastname);
    $stmt->fetch();
    ?>
    <option class='highlight' value="<?php echo encryptData($adviserID); ?>"><?php echo $firstname . " " . $lastname; ?></option>
    <?php

}

if (isset($_POST['edit-section'])) {
    verifyUpdateForm();
}

function verifyUpdateForm() {
    if (editSectionFormsAreValid()) {
        editSection();
    } else {
        showInvalidPopup('Section Was Not Able To Update', 'Please make sure that the forms are valid and not empty.');
    }
}

function editSection() {

    $con = connect();
    $activityLog = new activityLog;

    $sectionIdToEdit = validateInput(decryptData($_POST['edit-section-id']));
    $editedSectionName = upperCase(validateInput($_POST['edit-section-name']));
    $editedSectionStrandID = validateInput(decryptData($_POST['edit-section-strand']));
    $editedSectionAdviserID = validateInput(decryptData($_POST['edit-section-adviser']));
    $editedSectionStatus = validateInput(decryptData($_POST['edit-section-status']));

    $stmt = $con->prepare('UPDATE sections 
    SET section_name = ?, 
    strand_id = ?, 
    adviser_id = ?,
    section_status = ?
    WHERE id = ?');

    $stmt->bind_param('siiii', $editedSectionName, $editedSectionStrandID, $editedSectionAdviserID, $editedSectionStatus, $sectionIdToEdit);
    if ($stmt->execute()) {
        $activityLog->recordActivityLog('edited a section information.');
        $_SESSION['execution'] = 'sectionEdited';
        header('location: ?sectionEdited');
        exit(0);
    } else {
        showErrorPopup('An Error Occured', 'There was an error processing your request.');
    }
    

} 

#
# VIEW SECTIONS
#

if (isset($_POST['section-view'])) {
    $_SESSION['viewSection'] = $_POST['section-id'];
    header('location: viewstudent');
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
                            <div class="sidebar-link active-link">
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
                            <h1>Manage Sections</h1>
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
                        <h1>Sort sections by</h1>
                        <div data-aos="fade-down" class="filters-content">
                            <a href="?order=id">ID</a>
                            <a href="?order=sectionName">Section Name</a>
                            <a href="?order=adviserName">Adviser Name</a>
                            <a href="?order=strandName">Strand</a>
                            <a href="?order=gradeLevel">Grade Level</a>
                            <a href="?order=sectionStatus">Status</a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="search">
                <div class="container">
                    <div class="search-wrapper">
                        <form action="" method="post">
                            <input type="text" name="searchSection" placeholder="Search section name" value="<?php if (isset($_GET['q'])) { echo $_GET['q']; } ?>" autocomplete="off">
                            <div class="search-button-container">
                                <button type="submit" name="search"> <i class="fad fa-search"></i> </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section id="sections-list">
                <div class="container">
                    <div class="sections-list-wrapper">

                        <div class="section-card">
                            <div class="container">
                                <form id="add-section-form" method="post">
                                    <button name='add-new-section-prompt' type="submit"><i class="add-icon fad fa-plus"></i></button>
                                    <h1>Create Section</h1>
                                    <input type="text" name="create-section-name" placeholder="Section Name" value="<?php if (isset($_POST['add-section-name'])) { echo $_POST['add-section-name']; } ?>">
                                    <select name="create-section-strand" id="">
                                        <option value="">Select Strand</option>
                                        <?php populateStrandSelectOption() ?>
                                    </select>
                                    <select name="create-section-adviser" id="">
                                        <option value="">Select Adviser</option>
                                        <?php populateAddSectionAdvisersOption(); ?>
                                    </select>
                                </form>
                            </div>
                        </div>           
                        <?php populateSectionCards(); ?> 
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
