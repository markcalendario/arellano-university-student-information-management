<?php 

session_start();
include 'php/connection.php';
include 'php/input_validation.inc.php';
include 'php/users.php';
include 'php/modal.php';
include 'php/activity-log.php';


$user = new user;

#
# LANDING LINKS MANAGERS
#

if (isset($_SESSION['loggedin'])) {
    sendToRespectivePortals();
}

function sendToRespectivePortals() {
    $user = new user;

    if ($user->isStudent()) {
        header('location: student');
    } else if ($user->isTeacher()) {
        header('location: teacher');
    } else if ($user->isFaculty()) {
        header('location: faculty');
    }
}

#
# THE LOG IN FEATURE
#

if (isset($_POST['login'])) {
    prepareUserLogin();
}

function prepareUserLogin() {
    $con = connect();

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = validateInput($_POST['email']);
        $password = validateInput($_POST['password']);
    } else {
        failedLogin();
    }

    if (isUserEmailCorrect($email) && isUserPasswordCorrect($email, $password)) {
        loginUser();
        header('location: index.php');
        exit(0);
    } else {
        failedLogin();
    }
}

function failedLogin() {
    $_SESSION['execution'] = 'failedLogin';
    $_SESSION['loginTries']--;
    $_SESSION['lastAttempt'] = time();
    header('location: ?failedLogin');
    exit(0);
}

if (isset($_SESSION['lastAttempt']) && $_SESSION['lastAttempt'] + 60 <= time()) {
    $_SESSION['loginTries'] = 3;
    unset($_SESSION['lastAttempt']);
} 

if (!isset($_SESSION['loginTries'])) {
    $_SESSION['loginTries'] = 3;
}


function setAllSessions() {
    $con = connect();

    $email = $_POST['email'];

    $sql = "SELECT * FROM user_credentials WHERE email = ?";  
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    # ID SESSION SET
    $_SESSION['id'] = $row['id'];
    # CLEAR INVALID LOGIN SESSION
    if (isset($_SESSION['invalidLogin'])) {
        session_unset($_SESSION['invalidLogin']);  
    }  
}

function setUserStateToLogin() {
    # SET SESSION TO LOGGEDIN
    $_SESSION['loggedin'] = true;
}

function loginUser() {
    setAllSessions();
    setUserStateToLogin();
    
    $activityLog = new activityLog;
    $activityLog->recordActivityLog('has logged in.');
    $_SESSION['invalidLogin'] = false;
    $_SESSION['loginTries'] = 3;
}

function isUserPasswordCorrect($email, $password) {
    $con = connect();

    $sql = "SELECT * FROM user_credentials WHERE email = ?";  
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return password_verify($password, $row['password']);
}

function isUserEmailCorrect($email) {
    $con = connect();

    $sql = "SELECT * FROM user_credentials WHERE email = ?";  
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows;  
}

// showWelcomerModal('ALL RIGHTS RESERVED', '&#169; 2021 Mark Kenneth S. Calendario and Group 2. <br/><br/> This system cannot be reproduced, copied and even recorded in any form or by any means without the permission of the copyright holder.');

?>

<!DOCTYPE html>
<html lang="en" UTF="8">
<head>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="js/aos/dist/aos.css">
    <link rel="stylesheet" href="styles/query.css">
    <link rel="stylesheet" href="styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="styles/modal.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | AUSMS</title>
</head>
<body>

    <div id="loader">
        <div class="container">
            <div class="wrapper">
                <img src="assets/images/AULOGO.png" alt="">
                <h4 class="loader-main-text">The System is Loading. <br> Please wait...</h4>
                <p class="loader-sub-text">Mark Kenneth Calendario</p>
                <i class="spinner fas fa-circle-notch fa-spin"></i>
            </div>
        </div>
    </div>
    
    <header>
        <nav>
            <div class="container">
                <div class="ausms-top">
                    <div class="au-logo-top">
                        <img src="assets/images/AULOGO.png" alt="Arellano Logo">
                    </div>
                    <div class="ausms-top-texter">
                        <h2>ARELLANO UNIVERSITY</h2>
                        <h4>Student Management System</h4>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section id = "loginUI">
        <div class="container">
            <div class="loginUI-wrapper">
                
                <div class="loginUI-left">
                    <div class="loginUI-left-image">
                        <img src="assets/images/safe.svg" alt="SAFE">
                    </div>
                    <div class="loginUI-left-text">
                        <h2> It's safe and secure! </h2>
                        <p> We do not share your info to other. </p>
                    </div>
                </div>

                <div class="loginUI-right">
                    <div class="loginUI-right-text">
                        <h2> Log in </h2>
                        <!-- <div>
                        <p>Faculty user test email: mark@aujrc.edu</p>
                        <p>Faculty user test password: markCalendario19</p>
                        </div> -->
                    </div>
                    <div class="loginUI-right-login-panel">
                        <div class="container">
                            <?php
                            
                            if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'failedLogin') {
                                ?>
                                    <h3 class="incorrect-creds"> <i class="fas fa-exclamation-circle"></i> Invalid credentials, try again.</h3>
                                    <p class="attempt-text"> Attempts Left: <?php echo $_SESSION['loginTries'] ?>  </p>
                                <?php
                                unset($_SESSION['execution']);
                            }

                            ?>
                            <form action="index.php" method="post">
                                <div class="input-contain">
                                    <label> Email </label>
                                    <div class="input-wrap">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="email">
                                    </div>
                                </div>
                                <div class="input-contain">
                                    <label> Password </label>
                                    <div class="input-wrap">
                                        <i class="fas fa-key"></i>
                                        <input id="password" type="password" name="password">
                                        <i id="showPasswordButton" class="fas fa-lock"></i>
                                    </div>
                                </div>
                                <div class="loginUI-buttons">
                                    <?php 
                                    
                                    if ($_SESSION['loginTries'] != 0) {
                                        ?>
                                            <input type="submit" name="login" value="LOGIN">
                                        <?php
                                    } else {
                                        ?>
                                            <button type="button" class="attempt-reach"> <i class="fas fa-ban"></i> Comeback again after 1 minute. </button>
                                        <?php
                                    }

                                    ?>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="home-of-the-chiefs">
        <div class="container">
            <div class="home-of-the-chiefs-wrapper">
                <div data-aos="fade-up" data-aos-duration="5000" class="home-of-the-chiefs-images">
                    <img src="assets/images/AUCHIEFS.png" alt="">
                </div>
                <div data-aos="fade-up" class="home-of-the-chiefs-texter">
                    <h1>ARELLANO UNIVERSITY</h1>
                    <h3>Home of the Chiefs</h3>
                </div>
            </div>
        </div>
    </section>

    <section id="about-us">
        <div class="container">
        
            <div class="about-us-texter">
                <h1>ARELLANO UNIVERSITY CAMPUSES</h1>
                <p>The Arellano University has 7 campuses across Metro Manila.</p>
            </div>
            <div class="about-us-wrapper">
                <div data-aos="fade-up" data-aos-duration="100" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/juansumulong.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Juan Sumulong Campus</h2>
                            <p>City of Manila</p>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="500" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/bonifacio.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Andres Bonifacio Campus</h2>
                            <p>Pasig City</p>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="700" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/abadsantos.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Jose Abad Santos Campus</h2>
                            <p>Pasay City</p>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="1200" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/esguerra.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Elisa Esguerra Campus</h2>
                            <p>Malabon City</p>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="1500" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/rizal.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Jose Rizal Campus</h2>
                            <p>Malabon City</p>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="1700" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/plaridel.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Plaridel Campus</h2>
                            <p>Mandaluyong City</p>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="2000" class="about-card">
                    <div class="about-card-top">
                        <img src="assets/images/campuses/mabini.jpg" alt="" srcset="">
                    </div>
                    <div class="about-card-content">
                        <div class="container">
                            <h2>Apolinario Mabini Campus</h2>
                            <p>Menlo, Pasay City</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="au-history">
        <div class="container">
            <div class="au-history-wrapper">
                <div class="au-history-title">
                    <h1>THE HISTORY OF ARELLANO UNIVERSITY</h1>
                </div>
                <div data-aos="fade-right" data-aos-duration="1000" class="au-history">
                    <div class="history-image">
                        <img src="assets/images/cayetanoarellano.jpg" alt="Cayetano Arellano">
                    </div>
                    <p class="history-text-content">
                        Arellano University began in 1938 by Florentino Cayco, Sr. It was established as a law school by Cayco, the first Filipino Undersecretary of Public Instruction and an educator. the school was named Arellano Law College which is derived from Cayetano Arellano, the first Filipino Supreme Court Chief Justice. The newly founded institution offers the regular four-year law course and was one of the well-known institutions at that time catering to numerous prolific people in the field of law and public service.
                    </p>
                </div>
                <div data-aos="fade-left" data-aos-duration="1000" class="au-history history-invert">
                    <div class="history-image">
                        <img src="assets/images/arellanoold.JPG" alt="Cayetano Arellano">
                    </div>
                    <p class="history-text-content">
                        On the onset of World War II, the school was closed from 1941 to 1945. It was reopened in April 1945 and renamed as the Arellano Colleges.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <section data-aos="flip-left" data-aos-duration="1300" id="au-tagline">
        <div class="container">
            <h1>FOR GOD AND COUNTRY</h1>
        </div>
    </section>
    
</body>

<footer>
    <div class="container">
        <div class="foot-title">
            <img src="assets/images/AU Logo Invert Full.png">
        </div>
        <div class="foot-wrapper">
            <div class="foot-left">
                <ul>
                    <h3>Developers</h3>
                    <li>Mark Kenneth S. Calendario</li>
                    <li>Arne Sonder Kiel Diestra</li>
                    <li>Carl Joseph Delos Reyes</li>
                    <li>Carl Joseph Sunga</li>
                    <li>Shawn Ashley Hernandez</li>

                </ul>
            </div>
            <div class="foot-middle">
                <ul>
                    <h3>Mentors</h3>
                    <li>Ms. Remelyn Bonillo</li>
                    <li>Ms. Epiphany Belbider</li>
                    <li>Ms. Reyann Habig Adzuara</li>
                    <li>Ms. Mary Grace Bayate</li>
                    <li>Mr. John Lloyd Cantos</li>
                    <li>Mr. Kim Symmund Dela Cruz</li>

                </ul>            
            </div>
            <div class="foot-right">
                <ul>
                    <h3>Languages</h3>
                    <li> <i class="fas fa-elephant"></i> <i class="fab fa-js"></i> <i class="fab fa-jquery"></i> </li>
                </ul>
                <ul>
                    <h3>System Design</h3>
                    <li> 
                        <i class="fab fa-figma"></i> 
                        <i class="fab fa-html5"></i> 
                        <i class="fab fa-sass"></i> 
                    </li>
                </ul>
                <ul>
                    <h3>Database</h3>
                    <li> 
                        <i class="fas fa-database"></i> MySQL 
                    </li>
                </ul>
                <ul>
                    <h3>System Info</h3>
                    <li> 
                        <i class="fas fa-code"></i> 19,000+ Lines 
                    </li>
                </ul>               
            </div>
        </div>
        <div class="foot-bottom">
            <p>2020 - 2021</p>
        </div>
    </div>
</footer>

</html>
<script src="js/aos/dist/aos.js"></script>
<script> AOS.init(); </script>
<script src="js/jquery-3.5.1.min.js"></script>
<script>

$(window).on('load', function() {
    $("#loader").fadeOut("slowest");;
});

$(document).ready(function () {

    $('#home-of-the-chiefs').ready(() => {

        var currentNumber = 0;

        let time = setInterval(() => {
            changeBackground();
        }, 3000);

        function changeBackground() {
            let images = [
                'volleyball_girls.jpg',
                'volleyball_boys.jpg',
                'volleyball_win.jpg',
                'basketball.jpg'
            ];

            if (currentNumber == images.length - 1) {
                currentNumber = 0;
            } else {
                currentNumber++;
            }

            let backgroundImage = images[currentNumber];

            $('#home-of-the-chiefs').css('background-image', `url(\'assets/images/home of the chiefs/${backgroundImage}\')`);
        }

    });


    $('#showPasswordButton').click(function() {
 
        if (isPasswordHidden()) {
            makePasswordVisible();

        } else {
            makePasswordHidden();
        }

        function makePasswordHidden() {
            $('#password').attr('type', 'password');

            $('#showPasswordButton')
                .removeClass('fa-unlock')
                .addClass('fa-lock')
                .css('color', 'red')
                .css('transform', 'rotate(-360deg)');
        }

        function makePasswordVisible() {
            $('#password').attr('type', 'text');

            $('#showPasswordButton')
                .removeClass('fa-lock')
                .addClass('fa-unlock')
                .css('color', 'green')
                .css('transform', 'rotate(360deg)');
        }

        function isPasswordHidden() {
            if ($('#password').attr('type') == 'password') {
                return true;
            } else {
                return false;
            }
        }
    });

    $('#iAgreePrepare').ready(function () {

        let countDownUntil = 1; // 7 Seconds
        const countDownTime = 1000; // Down every 1 second.

        let timer = setInterval(() => {
            
            if (countDownUntil >= 0) {

                $('#iAgreePrepare')
                    .html(countDownUntil)
                    .css('background-color', 'gray');
                countDownUntil--;

            } else {

                $('#iAgreePrepare')
                    .html('<i class="fad fa-handshake"></i> I Agree!')
                    .css('background-color', 'royalblue')
                    .prop('id', 'iAgree');

                clearInterval(timer);
            }

        }, countDownTime);

    });


    $('.modal-overlay').on('click', '#iAgree', function() {
        $('.modal-overlay').fadeOut('xslow');
    });

    
});

</script>
