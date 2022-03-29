<?php 

$activityLog = new activityLog;

if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'activityLogDeleted') {
    showSuccessPopup('Activity Log Has Been Deleted');
    unset($_SESSION['execution']);
}

if (isset($_SESSION['execution']) && $_SESSION['execution'] == 'allActivityLogDeleted') {
    showSuccessPopup('All of the Activity Log Has Been Deleted');
    unset($_SESSION['execution']);
}

class activityLog {

    function fetchAllActivityLog() {
        $con = connect();
      
        $stmt = $con->prepare('SELECT * FROM activity_log ORDER BY activity_time DESC');
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            $this->showNoActivityLogsToShow();
        }

        while ($row = $result->fetch_assoc()) {
            ?>
            <form method="post">
                <tr>
                    <input type="hidden" name="activity-id" value="<?php echo encryptData($row['activity_id']); ?>">
                    <td><?php echo $row['activity_doer']; ?></td>
                    <td><?php echo $row['activity_text']; ?></td>
                    <td><?php echo $this->fixActivityLogTime($row['activity_time']) ?></td>
                    <?php 

                        $user = new user;
                        if ($user->isFaculty()) {
                            ?>
                            <td><button name="delete-log" type="submit"> <i class="fad fa-trash"></i> </button></td>
                            <?php
                        }

                    ?>
                </tr>
            </form>
            <?php
        }

    }

    function recordActivityLog($activity) {
        $con = connect();
        $user = new user;

        $doer = $user->getFullName();
        $timestamp = date('Y-m-d H:i:s');
        $stmt = $con->prepare('INSERT INTO activity_log (activity_doer, activity_text, activity_time) 
        VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $doer, $activity, $timestamp);
        $stmt->execute();
    }

    function showNoActivityLogsToShow() {
        ?>
        <form action="">
        <tr>
            <td class="no-logs-row" colspan="4">
                <i class="no-logs-icon fad fa-wind"></i>
                <h1>It's so silent here.</h1>
            </td>
        </tr>
        </form>
        <?php
    }

    function deleteActivityLog($logID) {
        $con = connect();

        $logID = validateInput(decryptData($logID));
        $stmt = $con->prepare('DELETE FROM activity_log WHERE activity_id = ?');
        $stmt->bind_param('i', $logID);
        $stmt->execute();
        $stmt->store_result();
        $isDeleted = $stmt->affected_rows;

        if ($isDeleted > 0) { 
            $_SESSION['execution'] = 'activityLogDeleted';
            header('location:' . $_SERVER['PHP_SELF']);
            exit(0);
        }
    }

    function deleteAllActivityLogs() {
        $con = connect();
        $user = new user;

        if ($user->isFaculty()) {

            $this->deleteAllActivityLogsPermanently($con);

        } 
        else {
            showInvalidPopup("You are not a faculty admin.", "You are not authorized to use this command.");
        }
    }

    function deleteAllActivityLogsPermanently($con) {

        $stmt = $con->prepare('DELETE FROM activity_log');
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->execute()) { 
            $_SESSION['execution'] = 'allActivityLogDeleted';
            header('location:' . $_SERVER['PHP_SELF']);
            exit(0);
        }

    }

    function fixActivityLogTime($time) {
        $fixedTime = date(' F d, Y h:i A', strtotime($time));
        return $fixedTime;
    }

}

if (isset($_POST['delete-log'])) {
    $activityLog->deleteActivityLog($_POST['activity-id']);
}

if (isset($_POST['trash-all-logs'])) {
    $activityLog->deleteAllActivityLogs();
}

?>