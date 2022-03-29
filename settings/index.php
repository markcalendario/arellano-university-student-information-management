<?php 

session_start();
include '../php/inc.php';
include '../php/data-encryption.inc.php';

$user = new user;

if ($user->isTeacher()) {
    $teacher = new teacher;
} else if ($user->isStudent()) {
    $student = new student;
}

function sendToHomePage() {
    header('location: ..');
}

function restrict() {

    if (!isset($_SESSION['loggedin'])) {
        sendToHomePage();
    }
    
    if (isset($_SESSION['restriction'])) {
        header('location: ../restrict');
    }
}

function showPopups() {
    if (isset($_SESSION['successProfileChange'])) {
        showSuccessPopup('Your profile picture updated successfully.');
        unset($_SESSION['successProfileChange']);
    }

    if (isset($_SESSION['failedProfileChange'])) {
        showErrorPopup('An error occured.', 'An error occured while we are updating your profile.');
        unset($_SESSION['failedProfileChange']);
    }

    if (isset($_SESSION['successPasswordChange'])) {
        showSuccessPopup('You password has been changed.');
        unset($_SESSION['successPasswordChange']);
    }

    if (isset($_SESSION['failedPasswordChange'])) {
        showErrorPopup('An error occured.', '');
        unset($_SESSION['failedPasswordChange']);
    }
}

function page_init() {
    restrict();
    showPopups();
}

page_init();

### PROFILE PICTURE UPLOAD

if (isset($_POST['save-profile'])) {

    if (isset($_FILES['change-profile-image'])) {
        validateProfileImage();
    }
}

function validateProfileImage() {
    $isUploadOk = true;

    $imageName = $_FILES['change-profile-image']['name'];
    $imageType = $_FILES['change-profile-image']['type'];
    $imageExtensionGetter = explode('.', $imageName);
    $extension = end($imageExtensionGetter);
    $imageTmpName = $_FILES['change-profile-image']['tmp_name'];
    $imageSize = $_FILES['change-profile-image']['size'];
    
    if (!hasValidExtensions($imageType)) {
        $isUploadOk = false;
    }

    if (!isOnRequiredFileSize($imageSize)) {
        $isUploadOk = false;
    }

    ##################

    if ($isUploadOk) {
        uploadProfile($imageTmpName, $extension);
    } else {
        showErrorPopup('Your profile picture is not valid.', 'Make sure that it\'s square and a jpeg/jpg file format.');
    }
}

function isOnRequiredFileSize($imageSize) {

    define('THREE_MEGABYTES', 3145728);

    if ($imageSize <= THREE_MEGABYTES) {
        return 1;
    } else {
        return 0;
    }
}

function hasValidExtensions($imageType) {
    $extensions = array(
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif'
    );

    if (in_array($imageType, $extensions)) {
        return 1;
    } else {
        return 0;
    }

}

function uploadProfile($imageTmpName, $extension) {
    $user = new user;
    define('AVATAR_DIRECTORY', '../userfiles/profilePictures/');

    $newUserProfileName = $_SESSION['id'].'_AUSMS_Profile.jpg';
    $newProfileDirectory = AVATAR_DIRECTORY.$_SESSION['id'].'/'.$newUserProfileName;

    if (!file_exists(AVATAR_DIRECTORY.$_SESSION['id'])) {
        mkdir(AVATAR_DIRECTORY.$_SESSION['id']);
    }

    #$isUploaded = move_uploaded_file($imageTmpName, AVATAR_DIRECTORY.$_SESSION['id'].'/'.$newUserProfileName.'.'.$extension);

    # Change if not .jpg
    if ($extension == 'png') {
        $image = imagecreatefrompng($imageTmpName);
        $isUploaded = imagejpeg($image, $newProfileDirectory, 70);
        imagedestroy($image);
    }

    if ($extension == 'gif') {
        $image = imagecreatefromgif($imageTmpName);
        $isUploaded = imagejpeg($image, $newProfileDirectory, 70);
        imagedestroy($image);
    }

    if ($extension == 'jpeg') {
        $image = imagecreatefromjpeg($imageTmpName);
        $isUploaded = imagejpeg($image, $newProfileDirectory, 70);
        imagedestroy($image);
    }

    if ($extension == 'jpg') {
        $image = imagecreatefromjpeg($imageTmpName);
        $isUploaded = imagejpeg($image, $newProfileDirectory, 70);
        imagedestroy($image);
    }


    if ($isUploaded) {
        header('location: ../settings/');
        $_SESSION['successProfileChange'] = true;
        updateProfilePictureStatus();
        exit(0);
    } else {
        header('location: ../settings/');
        $_SESSION['failedProfileChange'] = true;
        exit(0);
    }
}

######

if (isset($_POST['savePassword'])) {
    isPasswordValid();
}

function isPasswordValid() {
    if (empty($_POST['old-password']) || empty($_POST['new-password']) || strlen(validateInput($_POST['new-password'])) < 6) {
        showInvalidPopup('Invalid Password', 'Empty fields or your password has less than 6 characters.');
    } else {
        isOldPasswordCorrect();
    }
}

function isOldPasswordCorrect() {
    $con = connect();

    $stmt = $con->prepare('SELECT password FROM user_credentials WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($oldPassword);
    $stmt->fetch();

    $isChallengeOk = password_verify(validateInput($_POST['old-password']), $oldPassword);

    if ($isChallengeOk) {
        saveNewPasswordOnDatabase($_POST['new-password']);
    } else {
        showInvalidPopup('Password mismatch.', 'Your old password is not correct.');
    }
}

function saveNewPasswordOnDatabase($newPassword) {

    $con = connect();

    $hashPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $stmt = $con->prepare('UPDATE user_credentials SET password = ? WHERE id = ?');
    $stmt->bind_param('si', $hashPassword, $_SESSION['id']);
    
    if ($stmt->execute()) {
        $_SESSION['successPasswordChange'] = true;
        header('location: ../settings/');
        exit(0);
    } else {
        $_SESSION['failedPasswordChange'] = true;
        header('location: ../settings/');
        exit(0);
    }
}

if (isset($_POST['new-username'])) {
    checkIfUsernameNotEmpty();
}

function checkIfUsernameNotEmpty() {
    
    if (empty($_POST['new-username'])) {
        showInvalidPopup('Please provide username.', '');
    } else {
        checkIfUsernameValid();
    }
}

function checkIfUsernameValid() {
    define('REQUIRED_EMAIL_ADDRESS', 'aujrc.edu');

    $newUsername = $_POST['new-username'];
    $userNameExplode = explode('@', $newUsername);
    
    if (end($userNameExplode) === REQUIRED_EMAIL_ADDRESS && $newUsername == filter_var($newUsername, FILTER_VALIDATE_EMAIL)) {
        changeUsername($newUsername);
    } else {
        showInvalidPopup('@aujrc.edu is required at the end of a new username', '');
    }
}

function changeUsername($newUsername) {
    $con = connect();

    $stmt = $con->prepare('UPDATE user_credentials SET email = ? WHERE id = ?');
    $stmt->bind_param('si', $newUsername, $_SESSION['id']);
    
    if ($stmt->execute()) {
        showSuccessPopup('Username changed successfully'); 
    } else {
        showErrorPopup('An internal error occured', 'Please try again');
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
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Settings</title>
</head>
<body>

    <section id="main-UI">
    <aside class="sidebar-pane">
            <div class="container-fluid">
                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="<?php echo fetchProfilePicture('../'); ?>">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4><?php echo $user->getFullName(); ?></h4>
                            <p> <?php echo $user->getUserTypeOnText(); ?> </p>
                        </div>
                    </div>

                    <?php 
                    
                    if ($user->isFaculty()) {
                        ?>
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
                                <div class="sidebar-link ">
                                    <a href="../inactivestudents"><i class="fas fa-ban"></i> <span>Inactive Students</span></a>
                                </div>

                                <h1 class="list-title">Personal Settings</h1>

                                <div class="sidebar-link active-link">
                                    <a href="../faculty"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
                                </div>
                                                            
                            </div>
                        </div>
                        <?php
                    }

                    if ($user->isTeacher()) {
                        ?>
                        <div class="sidebar-pane-lists">
                            <div class="container-fluid">
                                <h1 class="list-title">Administrator</h1>

                                <div class="sidebar-link">
                                    <a href="../teacher"><i class="fas fa-home"></i> <span>Teacher Portal</span></a>
                                </div>
                                <div class="sidebar-link ">
                                    <a href="../teacher/viewadvisory"><i class="fas fa-user-plus"></i> <span>Manage Advisory</span></a>
                                </div>
                                <div class="sidebar-link">
                                    <a href="../teacher/grademanage"><i class="fas fa-award"></i> <span>Manage Grades</span></a>
                                </div>

                                <h1 class="list-title">Personal Settings</h1>

                                <div class="sidebar-link active-link">
                                    <a href="../settings"><i class="fas fa-cog"></i> <span>Account Settings</span></a>
                                </div>
                                                            
                            </div>
                        </div>
                        <?php
                    }

                    if ($user->isStudent()) {
                        ?>
                        <div class="sidebar-pane-lists">
                            <div class="container-fluid">
                                <h1 class="list-title">Navigation</h1>

                                <div class="sidebar-link">
                                    <a href="../student/../"><i class="fas fa-home"></i> <span>Student Portal</span></a>
                                </div>
                                <div class="sidebar-link">
                                    <a href="../student/rankings"><i class="fas fa-trophy"></i> <span>View Rankings</span></a>
                                </div>
                                <div class="sidebar-link">
                                    <a href="../student/classmates"><i class="fas fa-users"></i> <span>View Classmates</span></a>
                                </div>

                                <h1 class="list-title">Information</h1>

                                <div class="sidebar-link">
                                    <a href="../student/me"><i class="fas fa-sticky-note"></i> <span>View Information</span></a>
                                </div>

                                <h1 class="list-title">Settings</h1>

                                <div class="sidebar-link active-link">
                                    <a href="../settings"><i class="fas fa-cog"></i> <span>Settings</span></a>
                                </div>
                                                            
                            </div>
                        </div>
                        <?php
                    }
                    
                    ?>
                </div>
            </div>
    </aside>
        <div class="main-pane">
            <nav>
                <div class="container">
                    <div class="main-pane-topper">

                        <div class="topper-left">
                            <h1>Settings</h1>
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

            <section id="settings-top">
                <div class="container">
                    <div class="wrapper">
                    
                        <div class="settings-my-info">
                            <div class="on-left">
                            
                                <div class="profile-container">
                                    <img id="profile-picture" src="<?php echo fetchProfilePicture('../'); ?>">
                                </div>
                                <form id="profile-change" method="post" enctype="multipart/form-data">
                                    <input type="file" name="change-profile-image" id="change-profile-picture"/>
                                    <label for="change-profile-picture">Change Identity Picture</label>
                                </form>

                            </div>
                            <div class="my-info">
                                <h1> <?php echo $user->getFullName(); ?> </h1>
                                <p> <?php echo $user->getGender(); ?> </p>
                                <p> <?php echo $user->getContact(); ?> </p>
                            </div>
                        </div>

                        <div id="settings-collection">

                            <div class="settings-handler">
                                <div class="settings-title">
                                    <h1>Login Credentials</h1>
                                </div>
                                <div class="settings-main">
                                    <div class="settings-button" id="changePassword">
                                        <p> <i class="fas fa-asterisk"></i> Change Password</p>
                                    </div>
                                    <form id="changePasswordForm" action="" method="post">
                                        <input type="password" name="old-password" placeholder="Old Password">
                                        <input type="password" name="new-password" placeholder="New Password">
                                        <button name="savePassword" type="submit"> Save </button>
                                    </form>
                                </div>
                                <div class="settings-main">
                                    <div class="settings-button" id="changeUsername">
                                        <p> <i class="fas fa-id-card-alt"></i> Change Username</p>
                                    </div>
                                    <form id="changeUsernameForm" action="" method="post">
                                        <input type="text" name="new-username" placeholder="@aujrc.edu is required.">
                                        <button name="saveUsername" type="submit"> Save </button>
                                    </form>
                                </div>
                            </div>


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
<script>AOS.init();</script>
<script src="../js/sidebar.js"></script>

<script>

    $(document).ready(function() {
        
        $('#change-profile-picture').on('change', function() {

            // Preview Profile Picture
            $('#profile-picture').attr(
                'src', 
                window.URL.createObjectURL(
                    this.files[0]
                )
            );

            // Show Save Changes Button
            if ($('#profile-save').length != 1) {

                let saveProfileButton = $('<input type="submit" id="profile-save" name="save-profile" value="Save Changes">');
                $('#profile-change').append(saveProfileButton);
            
            }

        });

        $('#changePassword').click(() => {
            
            if (isChangePasswordFormOpen()) {
                $('#changePasswordForm').css('display', 'flex');
            } else {
                $('#changePasswordForm').fadeOut(100);
            }

        });

        isChangePasswordFormOpen = () => {
            if ($('#changePasswordForm').css('display') == 'none') {
                return 1;
            } else {
                return 0;
            }
        }

        $('#changeUsername').click(() => {
            
            if (isChangeUsernameFormOpen()) {
                $('#changeUsernameForm').css('display', 'flex');
            } else {
                $('#changeUsernameForm').fadeOut(100);
            }

        });

        isChangeUsernameFormOpen = () => {
            if ($('#changeUsernameForm').css('display') == 'none') {
                return 1;
            } else {
                return 0;
            }
        }

    });

</script>
