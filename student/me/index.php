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



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../styles/query.css">
    <link rel="stylesheet" href="../../styles/popup.css">
    <link rel="stylesheet" href="../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../styles/modal.css">
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
                                <a href="../../"><i class="fas fa-home"></i> <span>Student Portal</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../rankings"><i class="fas fa-trophy"></i> <span>View Rankings</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../classmates"><i class="fas fa-users"></i> <span>View Classmates</span></a>
                            </div>

                            <h1 class="list-title">Information</h1>

                            <div class="sidebar-link active-link">
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
                            <h1>You</h1>
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
                            <img src="<?php echo fetchUserProfilePicture(encryptData($_SESSION['id']), '../../'); ?>" alt="" srcset="">
                        </div>
                        <div class="profile-basic-info">
                            <h1><?php echo $user->getFullName(); ?></h1>
                            <p>LRN: <span class="bold"> <?php echo $student->lrn; ?> </span></p>
                            <p> Gender: <span class="bold"><?php echo $user->getGender(); ?></span></p>
                            <p> Age: <span class="bold"><?php echo $user->getAge(); ?></span></p>
                            <p> Birthday: <span class="bold"><?php echo $user->getSentenceTypeBirthday(); ?></span></p>
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
                                    <td><?php echo $user->getCountry(); ?></td>
                                </tr>
                                <tr>
                                    <th>Region</th>
                                    <td><?php echo $user->getRegion(); ?></td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><?php echo $user->getAddress(); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact</th>
                                    <td><?php echo $user->getContact(); ?></td>
                                </tr>
                            </table>
                            <div class="basic-info">
                            <h1>Family Background</h1>
                            <table>
                                <tr>
                                    <th>Mother's Maiden Name</th>
                                    <td><?php echo $user->getMotherName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Mother's Nature of Work</th>
                                    <td><?php echo $user->getMotherWork(); ?></td>
                                </tr>
                                <tr>
                                    <th>Mother's Contact</th>
                                    <td><?php echo $user->getMotherContact(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Full Name</th>
                                    <td><?php echo $user->getFatherName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Nature of Work</th>
                                    <td><?php echo $user->getFatherWork(); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Contact</th>
                                    <td><?php echo $user->getFatherContact() ?></td>
                                </tr>
                                <tr>
                                    <th>Guardian's Full Name</th>
                                    <td><?php echo $user->getGuardianName(); ?></td>
                                </tr>
                                <tr>
                                    <th>Guardian's Contact</th>
                                    <td><?php echo $user->getGuardianContact(); ?></td>
                                </tr>
                            </table>
                            <div class="basic-info">
                            <h1>School Record</h1>
                            <table>
                                <tr>
                                    <th>Section</th>
                                    <td><?php echo $student->studentSectionName; ?></td>
                                </tr>
                                <tr>
                                    <th>Grade Level</th>
                                    <td><?php echo 'Grade ' . $student->studentStrandGrade; ?></td>
                                </tr>
                                <tr>
                                    <th>Strand</th>
                                    <td><?php echo $student->studentStrandName; ?></td>
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

<script src="../../js/jquery-3.5.1.min.js"></script>
<script src="../../js/aos/dist/aos.js"></script>
<script>AOS.init();</script>
<script src="../../js/sidebar.js"></script>