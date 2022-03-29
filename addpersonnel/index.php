<?php

session_start();
include '../php/inc.php';
include '../php/data-encryption.inc.php';

$user = new user;
$activityLog = new activityLog;

#
# PREVENT USERS FROM ACCESSING THIS PAGE
#

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

#
# POPUPS
#

function showPopups() {
    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'success') {
        showSuccessPopup('The Personnel Added Successfully');
    }

    if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'error') {
        showErrorPopup("We have encountered an error!", "Error occured! Report this Immediately."); 
    }

    unset($_SESSION['execution']);
}

#
# Page Initialization
#

function page_init() {
    restrict();
    showPopups();
}
 
page_init();



#
# ADD Personnel
#

if (isset($_POST['add-personnel'])) {
    if (addPersonnelInputAreValid()) {
        checkPersonnelDuplication();
    } else {
        showInvalidPopup('Please complete all forms.', 'The form cannot be submitted, thank you!');
    }
}

function checkPersonnelDuplication() {

    $con = connect();
    $duplicationResult = null;

    $firstName = titleCase(validateInput($_POST['firstname']));
    $lastName = titleCase(validateInput($_POST['lastname']));

    $stmt = $con->prepare('SELECT * FROM user_information WHERE firstname = ? AND lastname = ?');
    $stmt->bind_param('ss', $firstName, $lastName);
    $stmt->execute();
    $stmt->store_result();
    $duplicationResult = $stmt->num_rows;

    if ($duplicationResult >= 1) {
        showInvalidPopup("The personnel is already registered in the database.", "TIP: If the personnel have the same first name and last name to another personnel, try to put some characters in the first name.");
    } else {
        addPersonnel();
    }
}

function addPersonnel() {
    $con = connect();
    $activityLog = new activityLog;

    $isAddPersonnelCredentialsSuccess = addPersonnelCredentials($con);
    $insertedId = $con->insert_id;

    if ($isAddPersonnelCredentialsSuccess 
    && addPersonnelBasicInformation($insertedId, $con) 
    && addBlankUserFamilyRow($insertedId, $con)) {
        $activityLog->recordActivityLog('added a new personnel');
        $_SESSION['execution'] = 'success';
        header('location: ?success');
        exit(0);
    }
    else {
        $_SESSION['execution'] = 'error';
        header('location: ?error');
        exit(0); 
    }
}

function addBlankUserFamilyRow($insertedId, $con) {
    $allFamValue = NULL;

    $stmt = $con->prepare('INSERT INTO user_family_background 
            (id, mother_fullname, mother_work, mother_contact, father_fullname, father_work, father_contact, guardian_fullname, guardian_contact, relationship) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssssssss', $insertedId, $allFamValue, $allFamValue, $allFamValue, $allFamValue, $allFamValue, $allFamValue, $allFamValue, $allFamValue, $allFamValue);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }
}


function addPersonnelBasicInformation($insertedId, $con) {

    $firstname = titleCase(validateInput($_POST['firstname']));
    $middlename = titleCase(validateInput($_POST['middlename'])); 
    $lastname = titleCase(validateInput($_POST['lastname'])); 
    $gender = titleCase(validateInput($_POST['gender'])); 
    $birthday = titleCase(validateInput($_POST['birthday'])); 
    $religion = titleCase(validateInput($_POST['religion'])); 
    $country = titleCase(validateInput($_POST['country'])); 
    $region = titleCase(validateInput($_POST['region'])); 
    $address = titleCase(validateInput($_POST['address'])); 
    $contact = titleCase(validateInput($_POST['contact']));

    $stmt = $con->prepare('INSERT INTO user_information 
            (id, firstname, middlename, lastname, gender, birthday, religion, country, region, address, contact) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssssssssi', $insertedId, $firstname, $middlename, $lastname, $gender, $birthday, $religion, $country, $region, $address, $contact);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
    }

}

function addPersonnelCredentials($con) {

    $firstname = titleCase(validateInput($_POST['firstname']));
    $lastname = titleCase(validateInput($_POST['lastname']));
    $personnelType = titleCase(validateInput(decryptData($_POST['personnelType'])));
    
    $email = generatePersonnelEmail($firstname, $lastname);
    $password = password_hash(generatePersonnelPassword($firstname, $lastname), PASSWORD_BCRYPT);

    $stmt = $con->prepare('INSERT INTO user_credentials 
            (email, password, usertype) 
            VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $email, $password, $personnelType);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0; 
    }

}

function generatePersonnelEmail($firstname, $lastname) {
    $year = date('Y');
    return strtolower(str_replace(' ', '', $firstname . "." . $lastname . "@aujrc.edu"));
}

function generatePersonnelPassword($firstname, $lastname) {
    $year = date('Y');
    return strtolower(str_replace(' ', '', $firstname.".faculty.".$year));
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
    <title>AUSMS | Add Personnel</title>
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
                            <div class="sidebar-link active-link">
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
                            <h1>Add Personnel</h1>
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

            <section id="guidelines">
                <div class="container">
                    <div class="wrapper">
                        <h1>Guidelines</h1>
                        <div class="guidelines-content">
                            <p>1. Complete the data of the personnel before submitting. All of them must be valid and accurate.</p>
                            <p>2. You can submit the form even if you don't provide a section.</p>
                            <p>3. The personnel's ID is auto generated. You can see it after a successful submission.</p>
                            <p>4. The family background must be provided by the personnel after the first login.</p>
                            <p>5. The email and password of the personnel are auto generated.</p>
                            <p><span class="bold">Default email: </span>[firstname].[lastname].@aujrc.edu (Ex. markkenneth@aujrc.edu)</p>
                            <p><span class="bold">Default password: </span>[firstname].faculty.[year] (Ex. markkenneth.jrc.faculty.2021)</p>
                            <p class="note">Note: Remind the personnel that they must change their password after the first login.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="dots-indicator">
                <div class="container">
                    <div class="wrapper">
                        <i class="fas indicator fa-circle"></i>
                        <i class="fas indicator fa-circle"></i>
                    </div>
                </div>
            </section>

            <form id="add-personnel-form" action="" method="post">
                <div class="container">
                    <!-- BASIC INFORMATION -->
                    <div class="information-page">
                        <h2> <i class="fad fa-user"></i> BASIC INFORMATION</h2>
                        <fieldset>
                            <label>Given Name</label>
                            <input type="text" name="firstname">
                        </fieldset>

                        <fieldset>
                            <label>Middle Name</label>
                            <input type="text" name="middlename">
                        </fieldset>

                        <fieldset>
                            <label>Last Name</label>
                            <input type="text" name="lastname">
                        </fieldset>

                        <fieldset>
                            <label>Gender</label>
                            <div class="linear-input">
                            <fieldset>
                                <label>Male</label>
                                <input type="radio" value="Male" name="gender">
                            </fieldset>
                            
                            <fieldset>
                                <label>Female</label>
                                <input type="radio" value="Female" name="gender">
                            </fieldset>
                            </div>
                        </fieldset>

                        <div class="linear-input">
                            <fieldset>
                                <label>Birthday</label>
                                <input type="date" name="birthday" id="birthday">
                            </fieldset>
                            
                            <fieldset>
                                <label>Age</label>
                                <input type="number" min="16" max="60" name="age" id="age" placeholder="Provide birthday first." disabled>
                            </fieldset>
                        </div>

                        <fieldset>
                            <label>Religion</label>
                            <select name="religion">
                                <option value="">Select religion</option>
                                <option value="Catholic">Catholic</option>
                                <option value="Iglesia Ni Cristo">Iglesia ni Cristo</option>
                                <option value="Born Again">Born Again</option>
                                <option value="Members Church of God International">Members of Church of God International</option>
                                <option value="Catholic">Church of Christ 4th Watch</option>
                                <option value="Mormon">Mormon</option>
                                <option value="Jehovah's Witness">Jehovah's Witness</option>
                                <option value="Other">Other</option>
                            </select>
                        </fieldset>

                        <div class="linear-input">
                            <fieldset>
                                <label>Country</label>
                            <input type="text" name="country" placeholder="Country" value="<?php if (isset($_POST['country'])) { echo $_POST['country']; } ?>">
                            </fieldset>

                            <fieldset>
                                <label>Region</label>
                                <input type="text" name="region" placeholder="Region" value="<?php if (isset($_POST['region'])) { echo $_POST['region']; } ?>">
                            </fieldset>
                        </div>


                        <fieldset>
                            <label>Address</label>
                            <input type="text" name="address">
                        </fieldset>

                        <fieldset>
                            <label>Contact</label>
                            <input type="number" name="contact">
                        </fieldset>

                        <div class="button-container">
                            <button type="button" class="next-button">Next <i class="fad fa-arrow-right"></i></button>
                        </div>

                    </div>

                    <div class="information-page">
                        <h2> <i class="fad fa-school"></i> SCHOOL'S DOCUMENT</h2>

                        <fieldset>
                            <label>Personnel Type</label>
                            <select name="personnelType">
                                <option value="<?php echo encryptData('1'); ?>">Teacher</option>
                                <option value="<?php echo encryptData('2'); ?>">Faculty Admin</option>
                            </select>
                        </fieldset>

                        <div class="button-container">
                            <button type="button" class="prev-button">Prev <i class="fad fa-arrow-left"></i></button>
                            <button type="submit" name="add-personnel" class="submit-button">Submit <i class="fas fa-user"></i></button>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </section>
    

</body>
</html>

<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../js/aos/dist/aos.js"></script>
<script src="js/addpersonnelportal.js"></script>
<script>AOS.init();</script>
<script src="../js/sidebar.js"></script>

