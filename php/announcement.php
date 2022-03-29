<?php 

if (isset($_POST['announce-trigger'])) {
    showPostAnnouncementModal();
}

if (isset($_POST['cancel-announce'])) {
    header('location: '. $_SERVER['PHP_SELF']);
}

if (isset($_POST['post-announcement'])) {
    verifyAnnouncementFormFields();
}

function verifyAnnouncementFormFields() {

    if (postAnnouncementFormsAreValid()) {
        postAnnouncement();
    } else {
        $_SESSION['execution'] = 'invalidPostAnnouncement';
        header('location: '.$_SERVER['PHP_SELF']);
        exit(0);
    }
}

function postAnnouncement() {
    $con = connect();

    $announcementTitle = validateInput($_POST['post-announcement-title']);
    $announcementContent = validateInput($_POST['post-announcement-content']);
    $announcementPosterID = $_SESSION['id'];

    $stmt = $con->prepare(
    'INSERT INTO 
        announcements (
            announcement_title, 
            announcement_content, 
            announcement_poster_id
        )
    VALUES (?, ?, ?)
    ');

    $stmt->bind_param('ssi',
        $announcementTitle,
        $announcementContent,
        $announcementPosterID
    );

    if ($stmt->execute()) {
        $_SESSION['execution'] = 'successPostAnnouncement';
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['execution'] = 'failedPostAnnouncement';
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    }
    
}

function populateAnnouncementsBoard() {
    $con = connect();

    $stmt = $con->prepare(
        'SELECT announcements.id, 
        announcement_title, 
        announcement_content, 
        announcement_poster_id, 
        announcement_date,
        firstname,
        lastname  
        FROM announcements
        INNER JOIN user_information 
        ON user_information.id = announcements.announcement_poster_id
        ORDER BY announcement_date DESC'
    );

    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result(
        $announcementID,
        $announcementTitle,
        $announcementContent,
        $announcementPosterID,
        $announcementDate,
        $firstName,
        $lastName
    );



    if ($stmt->num_rows > 0)  {
        while ($stmt->fetch()) {
            ?>
            <form action="" method="post">
                <div class="announcement-bar">
                    <h3><?php echo $announcementTitle; ?></h3>
                    <h5><?php echo $announcementContent; ?></h5>
                    <p><?php echo $firstName . " " . $lastName . " on " . getDateFromTimeStamp($announcementDate); ?></p>
                    <?php
                        $user = new user;
                        if ($user->isFaculty()) {
                            ?>
                            <input type="hidden" name="announcement-id" value="<?php echo encryptData($announcementID); ?>">
                            <button name='delete-announcement' type="submit">Delete</button>
                            <?php
                        }
                    ?>
                </div>
            </form>
            <?php
        }
    } else {
        showNoAnnouncementsPosted();
    }
}

function showNoAnnouncementsPosted() {
    ?>
    <div class="no-posted-announcement">
        <div class="container">
            <div class="wrapper">
                <i class="fad fa-clipboard-check"></i>
                <h1>No Announcements</h1>
                <h4>No announcement to show here.</h4>
            </div>
        </div>
    </div>
    <?php
}

function getDateFromTimeStamp($timestamp) {
    $timestamp = new DateTime($timestamp);
    return $timestamp->format('F d, Y') . " at " . $timestamp->format('h:i A');
}

if (isset($_POST['delete-announcement'])) {
    showDeleteAnnouncementModal($_POST['announcement-id']);
}

if (isset($_POST['delete-announcement-final'])) {
    deleteAnnouncementPermanently();
}

function deleteAnnouncementPermanently() {
    $con = connect();
    
    $announcementID = validateInput(decryptData($_POST['announcement-to-delete']));

    $stmt = $con->prepare(
        'DELETE FROM announcements WHERE id = ?'
    );
    $stmt->bind_param('i', $announcementID);
    if ($stmt->execute()) {
        $_SESSION['execution'] = 'successDeleteAnnouncement';
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['execution'] = 'failedDeleteAnnouncement';
        header('location:'.$_SERVER['PHP_SELF']);
        exit(0);
    }
}


?>