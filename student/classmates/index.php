<?php

session_start();

include '../../php/inc.php';
include '../../php/data-encryption.inc.php';

$user = new user;
$student = new student;

function page_init() {
    restrict();
}

page_init();

function restrict() {
    
    $user = new user;

    if (isset($_SESSION['restriction'])) {
        header('location: ../../restrict/');
    }
    
    if (!$user->isStudent()) {
        header('location: ../../student');
    }
}

#######

function populateClassmatesList() {
    $con = connect();
    $student = new student;

    $stmt = $con->prepare(
        'SELECT firstname, lastname, middlename
        FROM user_information
        LEFT JOIN user_school_info
        ON user_school_info.id = user_information.id
        WHERE section = ?'
    );

    $stmt->bind_param('i', $student->studentSectionID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($firstname, $lastname, $middlename);

    while ($stmt->fetch()) {
        ?>
        <tr>
            <td> <?php echo $lastname; ?> </td>
            <td> <?php echo $firstname; ?>  </td>
            <td> <?php echo $middlename; ?> </td>
        </tr>
        <?php
    }


}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../styles/query.css">
    <link rel="stylesheet" href="../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUSMS | Student</title>
</head>
<body>

    <section id="main-UI">
        <aside class="sidebar-pane">
            <div class="container-fluid">
                <button class="show-side-bar side-bar-close" type="button"><i class="fas fa-times"></i></button>

                <div class="sidebar-pane-wrapper">

                    <div data-aos="fade-left" data-aos-duration="0900" class="sidebar-pane-userinfo">
                        <div class="sidebar-pane-userinfo-profile-container">
                            <img src="<?php echo fetchProfilePicture('../../'); ?>" alt="AU">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4> <?php echo $user->getFullName(); ?> </h4>
                            <p> Student <?php echo $student->studentFullSectionDescription; ?> </p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Navigation</h1>

                            <div class="sidebar-link">
                                <a href="../../student"><i class="fas fa-home"></i> <span>Student Portal</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../rankings"><i class="fas fa-trophy"></i> <span>View Rankings</span></a>
                            </div>
                            <div class="sidebar-link active-link"">
                                <a href="../classmates"><i class="fas fa-users"></i> <span>View Classmates</span></a>
                            </div>

                            <h1 class="list-title">Information</h1>

                            <div class="sidebar-link">
                                <a href="../me"><i class="fas fa-sticky-note"></i> <span>View Information</span></a>
                            </div>

                            <h1 class="list-title">Settings</h1>

                            <div class="sidebar-link">
                                <a href="../../settings"><i class="fas fa-cog"></i> <span>Settings</span></a>
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
                            <h1>Your Classmates</h1>
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

            <section id="classmates">
                <div class="container">
                    <div class="wrapper">
                        <table>
                            <tr>
                                <th>Surname</th>
                                <th>Given Name</th>
                                <th>Middle Name</th>
                            </tr>
                            <?php populateClassmatesList() ?>

                        </table>
                    </div>
                </div>
            </section>

        </div>
    </section>

</body>
</html>

<script src="../../js/jquery-3.5.1.min.js"></script>
<script src="../../js/aos/dist/aos.js"></script>
<script src="js/todo.js"></script>
<script>AOS.init();</script>
<script src="../../js/sidebar.js"></script>
