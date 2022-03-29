<?php

function fetchProfilePicture($dirOffset) {
    
    $user = new user;

    if ($user->getProfilePicStatus() === 1) {
        
        $profileDir = $dirOffset.'userfiles/profilePictures/'.$_SESSION['id'].'/'.$_SESSION['id'].'_AUSMS_Profile.jpg';
        return $profileDir;

    } else {
        return $dirOffset.'userfiles/profilePictures/default.jpg';
    }

}

function fetchUserProfilePicture($id, $dirOffset) {

    $con = connect();

    $uid = decryptData($id);

    $stmt = $con->prepare(
        'SELECT profile_status FROM user_credentials 
        WHERE id = ?'
    );
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $stmt->bind_result($userProfileStatus);
    $stmt->fetch();

    if ($userProfileStatus == 1) {
        
        $profileDir = $dirOffset.'userfiles/profilePictures/'.$uid.'/'.$uid.'_AUSMS_Profile.jpg';
        return $profileDir;

    } else {
        return $dirOffset.'userfiles/profilePictures/default.jpg';
    }

}

function updateProfilePictureStatus() {
    $con = connect();

    $sql = 'UPDATE user_credentials SET profile_status = 1 WHERE id = ?';
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $_SESSION["id"]);
    $stmt->execute();
}


?>