<?php 
session_start();
include '../php/inc.php';

$user = new user;

if (!isset($_SESSION['loggedin'])) {
    header('location: ../index.php');
}

if (!isset($_SESSION['restriction'])) {
    header('location: ../index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../styles/all.css">
    <link rel="stylesheet" href="../styles/query.css">
    <link rel="stylesheet" href="styles/restrict.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESTRICTED</title>
</head>
<body>

    <section id="restriction-page">
        <div class="container">
            <div class="wrapper">
                <div class="img-container">
                    <img src="assets/monitor.svg" alt="a">
                </div>
                <div class="text-container">

                <?php 
                    if (isset($_SESSION['restriction'])) {
                        ?>
                        <h1><?php echo $_SESSION['restriction']; ?></h1>
                        <?php         
                    }
                ?>

                <p>Try again sometimes.</p>
                </div>
                <form method="post">
                    <input type="submit" name="signout" value="I understand.">
                </form>
            </div>
        </div>
    </section>
    


</body>
</html>