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

    if (isset($_SESSION['successEditStrand'])) {
        showSuccessPopup('Strand Edited Successfully.');
        unset($_SESSION['successEditStrand']);
    }

    if (isset($_SESSION['errorEditStrand'])) {
        showErrorPopup('Error Editing A Strand', 'Internal error occured!');
        unset($_SESSION['errorEditStrand']);
    }

    if (isset($_SESSION['successCreateStrand'])) {
        showSuccessPopup('Strand Created Successfully.');
        unset($_SESSION['successCreateStrand']);
    }

    if (isset($_SESSION['errorCreateStrand'])) {
        showErrorPopup('Error Creating a Strand', 'Internal error occured!');
        unset($_SESSION['errorCreateStrand']);
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
# CHOOSE STRAND
#

if (isset($_POST['strand-option'])) {
    $_SESSION['strand'] = validateInput($_POST['strand-id']);
    header('location: ../managestrands');
}
function fetchStrands() {
    $con = connect();

    $stmt = $con->prepare('SELECT 
    strand_id, 
    GROUP_CONCAT("GRADE ", strand_grade, " ", strand_name)
    FROM strands
    GROUP BY strand_id 
    ORDER BY strand_name, strand_grade ASC');
    $stmt->execute();
    $stmt->bind_result($strandID, $strandDescription);

    while ($stmt->fetch()) {
        ?>
        <form method="post">
            <input type="hidden" name="strand-id" value="<?php echo encryptData($strandID); ?>">
            <button type="submit" name="strand-option"> <?php echo $strandDescription; ?> </button>
        </form>
        <?php
    }
}

class strand {
    public $strandName = 'Select Strand First';
    public $strandGrade = '-';
    public $strandGradeRaw = '';

    public function __construct() {
        $con = connect();

        if (isset($_SESSION['strand'])) {
            $strandID = validateInput(decryptData($_SESSION['strand']));
            
            $stmt = $con->prepare('SELECT 
            strand_grade,
            strand_name
            FROM strands
            WHERE strand_id = ?');
            $stmt->bind_param('i', $strandID);
            $stmt->execute();
            $stmt->bind_result($strandGrade, $strandName);
            $stmt->fetch();
    
            $this->strandName = $strandName;
            $this->strandGrade = 'Grade ' . $strandGrade;
            $this->strandGradeRaw = $strandGrade;
        }
    }
}

#
# SUBJECTS LIST
#

function fetchStrandSubjects() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = decryptData($_SESSION['strand']);
    }
    
    $stmt = $con->prepare('SELECT
    subject_1,
    subject_2,
    subject_3,
    subject_4,
    subject_5,
    subject_6,
    subject_7,
    subject_8,
    subject_9
    FROM strand_subjects
    WHERE strand_id = ?');

    $stmt->bind_param('i', $strandID);
    $stmt->execute();
    $stmt->bind_result($subject1, $subject2, $subject3, $subject4, $subject5, $subject6, $subject7, $subject8, $subject9);

    while ($stmt->fetch()) {

        $subjectIDs = array($subject1, $subject2, $subject3, $subject4, $subject5, $subject6, $subject7, $subject8, $subject9);

        for ($i = 0; $i < count($subjectIDs); $i++) { 
            if ($subjectIDs[$i] != 0) {
                ?>
                <tr>
                    <td class="enrolled"> <?php echo getSemester($subjectIDs[$i]); ?> </td>
                    <td class="enrolled"> <?php echo getSubjectType($subjectIDs[$i]); ?> </td>
                    <td class="enrolled"> <?php echo getSubjectName($subjectIDs[$i]); ?> </td>
                </tr>
                <?php

            } 
            else {
                ?>
                <tr>
                    <td class="not-enrolled"> <?php echo getSemester($subjectIDs[$i]); ?> </td>
                    <td class="not-enrolled"> - </td>
                    <td class="not-enrolled"> <?php echo getSubjectName($subjectIDs[$i]); ?> </td>                       
                </tr>
                <?php
            }
        }
    }
}

function getSemester($subjectID) {
    $con = connect();

    $stmt = $con->prepare('SELECT semester FROM subjects WHERE subject_id = ?');
    $stmt->bind_param('i', $subjectID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($semester);
    $stmt->fetch();

    if ($semester == 1) {
        return 'First Semester';
    } else if ($semester == 2) {
        return 'Second Semester';
    } else {
        return 'Not Yet Enrolled';
    }
}

function getSubjectName($subjectID) {
    $con = connect();

    $stmt = $con->prepare('SELECT subject_name FROM subjects WHERE subject_id = ?');
    $stmt->bind_param('i', $subjectID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($subjectName);
    $stmt->fetch();

    if ($subjectName != '') {
        return $subjectName;
    } else {
        return 'Not Yet Enrolled';
    }
}

function getSubjectType($subjectID) {
    $con = connect();

    $stmt = $con->prepare('SELECT subject_type FROM subjects WHERE subject_id = ?');
    $stmt->bind_param('i', $subjectID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($subjectType);
    $stmt->fetch();

    $type = '-';

    if ($subjectType == '0') {
        $type = 'Core';
    } else if ($subjectType == '1'){
        $type = 'Specialization';
    }
    
    return $type;
}

$strand = new strand;

#
# SUBJECT FIELD
#

function hasNoSubjectField() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = validateInput(decryptData($_SESSION['strand']));
    }

    $stmt = $con->prepare('SELECT strand_subjects_id FROM strand_subjects WHERE strand_id = ?');
    $stmt->bind_param('i', $strandID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        return 1;
    } else {
        return 0;
    }
}

if (isset($_POST['create-strand-subject-list'])) {
    createStrandSubjectList();
}

function createStrandSubjectList() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = validateInput(decryptData($_SESSION['strand']));
    }

    $stmt = $con->prepare('INSERT INTO strand_subjects (strand_id) VALUES (?)');
    $stmt->bind_param('i', $strandID);
    if ($stmt->execute()) {
        header('location: ../managestrands');
    }
}

#
# CHANGE SUBJECT
#

function showStrandSubjectsEditor() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = decryptData($_SESSION['strand']);
    }
    
    $stmt = $con->prepare('SELECT
    subject_1,
    subject_2,
    subject_3,
    subject_4,
    subject_5,
    subject_6,
    subject_7,
    subject_8,
    subject_9
    FROM strand_subjects
    WHERE strand_id = ?');

    $stmt->bind_param('i', $strandID);
    $stmt->execute();
    $stmt->bind_result($subject1, $subject2, $subject3, $subject4, $subject5, $subject6, $subject7, $subject8, $subject9);

    while ($stmt->fetch()) {

        $subjectIDs = array($subject1, $subject2, $subject3, $subject4, $subject5, $subject6, $subject7, $subject8, $subject9);

        for ($i = 0; $i < count($subjectIDs); $i++) { 

            ?>
            <tr>
                <td colspan="3"> 
                    <h1><?php echo ordinal($i + 1); ?> Subject  </h1>
                    <select name="new-subject-<?php echo $i; ?>">
                        <option class="current" value="<?php echo encryptData($subjectIDs[$i]); ?>"> <?php echo getSemester($subjectIDs[$i]) . ' - ' .  getSubjectType($subjectIDs[$i]) . ' - ' . getSubjectName($subjectIDs[$i]); ?> </option>
                        <?php 
                        fetchSubjectList();
                        ?>
                        <option class="set-to-none" value="<?php echo encryptData(0); ?>"> Set to None </option>
                    </select>
                </td>
            </tr>
            <?php
        } 
    }
}

function ordinal($number) {
    if($number===0) return '0';
    else {
    $suffix = array('th','st','nd','rd','th','th','th','th','th','th');
     if ((($number % 100) >= 11) && (($number%100) <= 13))
      return $number. 'th';
       else
           return $number. $suffix[$number % 10];
    } 
}

function fetchSubjectList() {
    $con = connect();

    $stmt = $con->prepare('SELECT * FROM subjects');
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        ?>
        <option value="<?php echo encryptData($row['subject_id']) ?>"> <?php echo getSemester($row['subject_id']) . ' - ' . getSubjectType($row['subject_id']) . ' - ' . getSubjectName($row['subject_id']); ?>  </option>
        <?php
    }
}

if (isset($_POST['save-subjects-list'])) {
    saveSubjectsList();
}

function saveSubjectsList() {
    $con = connect();
    define('SUBJECT_COUNT', 9);

    if (isset($_SESSION['strand'])) {
        $strandID = validateInput(decryptData($_SESSION['strand']));
    }
    
    $newSubjectValues = array();

    for ($i = 0; $i < SUBJECT_COUNT; $i++) { 
        array_push($newSubjectValues, decryptData($_POST['new-subject-'.$i]));
    }

    $sql = 'UPDATE strand_subjects SET 
    subject_1 = ?,
    subject_2 = ?,
    subject_3 = ?,
    subject_4 = ?,
    subject_5 = ?,
    subject_6 = ?,
    subject_7 = ?,
    subject_8 = ?,
    subject_9 = ?
    WHERE strand_id = ?';

    $stmt = $con->prepare($sql);
    $stmt->bind_param('iiiiiiiiii', 
    $newSubjectValues[0], 
    $newSubjectValues[1], 
    $newSubjectValues[2], 
    $newSubjectValues[3], 
    $newSubjectValues[4], 
    $newSubjectValues[5], 
    $newSubjectValues[6],
    $newSubjectValues[7], 
    $newSubjectValues[8],
    $strandID);
    
    if ($stmt->execute()) {
        header('location: ../managestrands/');
    } else {
        session_destroy();
    }

    
}

#
# FLUSH SESSION
#

if (isset($_POST['flush'])) {
    unset($_SESSION['strand']);
    header('location: ../managestrands');
}

#
# SECTIONS COUNTER
#

function countSection() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = validateInput(decryptData($_SESSION['strand']));
    }

    $stmt = $con->prepare('SELECT strand_id FROM sections WHERE strand_id = ?');
    $stmt->bind_param('i', $strandID);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows();
}

#
# STRAND EDIT
#

if (isset($_POST['edit-strand'])) {
    showStrandEditorModal();
}

if (isset($_POST['cancel'])) {
    header('location: ../managestrands');
    exit(0);
}

if (isset($_POST['save-strand'])) {
    if (strandEditValuesAreValid()) {
        saveStrand();
    } else {
        showInvalidPopup('Please Complete The Required Forms', '');
    }
}

function saveStrand() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = validateInput(decryptData($_SESSION['strand']));
    }

    $strandName = validateInput($_POST['new-strand-name']);
    $strandGrade = validateInput(decryptData($_POST['new-grade-level']));

    $stmt = $con->prepare('UPDATE strands SET strand_name = ?, strand_grade = ? WHERE strand_id = ?');
    $stmt->bind_param('sii', $strandName, $strandGrade, $strandID);

    if ($stmt->execute()) {
        $_SESSION['sucessEditStrand'] = true;
        header('location: ../managestrands');
        exit(0);
    } else {
        $_SESSION['errorEditStrand'] = true;
        header('location: ../managestrands');
        exit(0);
    }
}

#
# CREATE STRAND
#

if (isset($_POST['create-strand-final'])) {
    checkCreateStrandValidity();
}

function checkCreateStrandValidity() {
    if (createStrandInputsAreValid()) {
        createStrand();
    } else {
        showInvalidPopup('Invalid Input', 'Please make sure that all of the required input are valid and not empty.');
    }
}

function createStrand() {
    $con = connect();

    if (isset($_SESSION['strand'])) {
        $strandID = validateInput(decryptData($_SESSION['strand']));
    }

    $strandName = validateInput($_POST['strand-name']);
    $strandGrade = validateInput(decryptData($_POST['grade-level']));

    $stmt = $con->prepare('INSERT INTO strands (strand_name, strand_grade) VALUES (?,?)');
    $stmt->bind_param('si', $strandName, $strandGrade);

    if ($stmt->execute()) {
        $_SESSION['successCreateStrand'] = true;
        $_SESSION['strand'] = encryptData($con->insert_id);
        header('location: ../managestrands');
        exit(0);
    } else {
        $_SESSION['errorCreateStrand'] = true;
        header('location: ../managestrands');
        exit(0);
    }
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
    <title>AUSMS | Manage Strands</title>
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
                            <div class="sidebar-link active-link">
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
                            <h1>Strand Management</h1>
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

            <section id="strand-main">
                <div class="container">
                    <div class="wrapper">

                        <div class="strand-chooser">
                            
                            <div class="topper">
                                <h1> <i class="fas fa-layer-group"></i> Choose Strands</h1>
                                <?php
                                if (!isset($_POST['create-strand-prompt'])) {
                                    ?>
                                    <form method="post">
                                        <button name="create-strand-prompt"> <i class="fas fa-plus"></i> Create Strand</button>
                                    </form>
                                    <?php
                                } else {
                                    ?>
                                    <form method="post">
                                        <button name="cancel"> <i class="fas fa-times"></i> Cancel</button>
                                    </form>
                                    <?php
                                }
                                ?>
                            </div>

                            <div class="strand-chooser-wrapper">
                                <?php

                                if (isset($_POST['create-strand-prompt'])) {
                                    ?>
                                    <form id="strand-creator-form" method="post">
                                        <input type="text" name="strand-name" placeholder="Strand Name">
                                        <select name="grade-level">
                                            <option value="">Select Grade Level</option>
                                            <option value="<?php echo encryptData(11); ?>">Grade 11</option>
                                            <option value="<?php echo encryptData(12); ?>">Grade 12</option>
                                        </select>
                                        <button name="create-strand-final"> <i class="fas fa-check"></i> Create Strand </button>
                                    </form>
                                    <?php
                                }

                                ?>
                                <?php fetchStrands(); ?>
                            </div>
                        </div>

                        <div class="strand-info">
                            <div class="strand-info-wrapper">
                                <div class="topper">
                                    <h1>Strand Information</h1>
                                </div>
                                <div class="strand-info-bar">
                                    <p> <?php echo $strand->strandGrade; ?> </p>
                                    <?php
                                    
                                    if ($strand->strandName === 'Select Strand First') {
                                        ?>
                                        <h1 class="no-strand-info"> <?php echo $strand->strandName; ?> </h1>
                                        <?php
                                    } else {
                                        ?>
                                        <h1> <?php echo $strand->strandName; ?> </h1>
                                        <p class="desc"> <i class="fad fa-info-circle"></i> There are <?php echo countSection(); ?> section(s) enrolled in this strand.</p>
                                        <?php
                                    }

                                    ?>
                                    <div class="strand-controls">
                                        <?php
                                        if (isset($_SESSION['strand'])) {
                                            ?>
                                            <form method="post">
                                                <button type="submit" name="edit-strand"> <i class="fas fa-edit"></i> Edit Strand</button>
                                                <button type="submit" name="flush"> <i class="fas fa-broom"></i> Flush Session</button>
                                            </form>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section id="subject-strands">
                <div class="container">
                    <div class="wrapper">

                        <div class="topper">
                            <h1>Subjects List</h1>
                        
                            <?php
                            if (hasNoSubjectField() && isset($_SESSION['strand'])) {
                                ?>
                                <form method="post">
                                    <button type="submit" name="create-strand-subject-list"> <i class="fas fa-play"></i> Create Subject Field </button>
                                </form>
                                <?php
                            } 
                            
                            if (!hasNoSubjectField() && !isset($_POST['edit-subjects-list'])) {
                                ?>
                                <form method="post">
                                    <button type="submit" name="edit-subjects-list"> <i class="fas fa-edit"></i> Edit Subject Field </button>
                                </form>
                                <?php
                            }
                            ?>

                        </div>

                        <table>
                            <tr>
                                <th>Subject Semester</th>
                                <th>Subject Type</th>
                                <th>Subjects</th>
                            </tr>
                            <?php 
                            
                                if (isset($_POST['edit-subjects-list'])) {
                                    ?>
                                    <form method="post">
                                        <?php
                                        showStrandSubjectsEditor();
                                        
                                        ?>
                                        <button type="submit" name="save-subjects-list"> <i class="fas fa-save"></i> Save Subject Field </button>
                                    </form>
                                    <?php
                                } else {
                                    fetchStrandSubjects();
                                }

                            ?>
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
