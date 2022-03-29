<?php 

if (isset($_POST['signout'])) {
    $activityLog = new activityLog;
    $activityLog->recordActivityLog('has disconnected.');
    session_destroy();
    header('location: ?signout');
}

?>