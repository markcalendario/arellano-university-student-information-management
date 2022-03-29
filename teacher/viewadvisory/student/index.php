<?php 

session_start();

include '../../../php/inc.php';
include '../../../php/data-encryption.inc.php';
include '../../../php/view-student.php';

$user = new user;

if (!isset($_SESSION['loggedin'])) {
    header('location: ../../../');
}

if (!$user->isTeacher()) {
    header('location: ../../../');
}

$viewStudent = new viewStudent;
$teacher = new teacher;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../../js/aos/dist/aos.css">
    <link rel="stylesheet" href="../../../styles/query.css">
    <link rel="stylesheet" href="../../../styles/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../../managesections/viewstudent/student/styles/index.css">
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
                            <img src="<?php echo fetchProfilePicture('../../../'); ?>" alt="AU">
                        </div>
                        <div class="sidebar-pane-userinfo-name">
                            <h4><?php echo $user->getFullName(); ?></h4>
                            <p><?php  echo $teacher->getSectionAndGradeLevel() . " Adviser"; ?></p>
                        </div>
                    </div>

                    <div class="sidebar-pane-lists">
                        <div class="container-fluid">
                            <h1 class="list-title">Administrator</h1>

                            <div class="sidebar-link">
                                <a href="../../../faculty"><i class="fas fa-home"></i> <span>Teacher Portal</span></a>
                            </div>
                            <div class="sidebar-link active-link">
                                <a href="../../viewadvisory"><i class="fas fa-user-plus"></i> <span>Manage Advisory</span></a>
                            </div>
                            <div class="sidebar-link">
                                <a href="../../grademanage"><i class="fas fa-award"></i> <span>Manage Grades</span></a>
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
                            <p> Gender: <span class="bold"><?php echo $viewStudent->getStudentGender(); ?></span></p>
                            <p> Age: <span class="bold"><?php echo $viewStudent->getAge(); ?></span></p>
                            <p> Birthday: <span class="bold"><?php echo $viewStudent->getStudentBirthday(); ?></span></p>
                            <form action="" method="post">
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
                                    <td>(+63) <?php echo $viewStudent->getStudentContact(); ?></td>
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