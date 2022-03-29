<?php 

define('TODO_ACTIVE', '0');
define('TODO_DONE', '-1');

if (isset($_POST['add-todo-activity'])) {
    verifyActivityFormFields();
}

function addTodoActivity() {
    $con = connect();

    $todoOwnerID = $_SESSION['id'];
    $todoStatus = TODO_ACTIVE;
    $todoText = encryptData(validateInput($_POST['todo-text']));

    $stmt = $con->prepare(
        'INSERT INTO todo (todo_owner_id, todo_text, todo_status)
        VALUES (?, ?, ?)'
    );
    $stmt->bind_param('isi', $todoOwnerID, $todoText, $todoStatus);
    
    if ($stmt->execute()) {
        $_SESSION['todo'] = 'uploaded';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    } else {
        showErrorPopup('An Error Occured', $con->error);
    }
}

if (isset($_SESSION['todo']) && $_SESSION['todo'] == 'uploaded') {
    showSuccessPopup('New Activity Posted');
    unset($_SESSION['todo']);
}

if (isset($_SESSION['todo']) && $_SESSION['todo'] == 'todoEmpty') {
    showInvalidPopup("Provide Todo Content", "This cannot be left empty");
    unset($_SESSION['todo']);
}

if (isset($_SESSION['todo']) && $_SESSION['todo'] == 'todoEmpty') {
    showErrorPopup("Too Much Text On Activity Content", "Please provide Todo Activity Content less than or equals to 150 characters.");
    unset($_SESSION['todo']);
}

if (isset($_SESSION['todo']) && $_SESSION['todo'] == 'deletedActivity') {
    showSuccessPopup('The Activity Successfully Deleted');
    unset($_SESSION['todo']);
}

if (isset($_SESSION['todo']) && $_SESSION['todo'] == 'changedActivityStatus') {
    showSuccessPopup('You have changed your activity status.');
    unset($_SESSION['todo']);
}

if (isset($_SESSION['todo']) && $_SESSION['todo'] == 'error') {
    showErrorPopup("An Internal Error Occured", "This may be because database crash, report this error immediately.");
    unset($_SESSION['todo']);
}

function populateTodoActivities() {
    $con = connect();

    $todoOwnerID = $_SESSION['id'];

    $stmt = $con->prepare(
        'SELECT * FROM todo WHERE todo_owner_id = ?'
    );

    $stmt->bind_param('i', $todoOwnerID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            ?>
            <form action="" method="post">
                <input type="hidden" name="todo-id" value="<?php echo encryptData($row['todo_id']); ?>">
                <div class="todo-list-bar">
                    <p class="<?php if ($row['todo_status'] == TODO_DONE) { echo "todo-done"; } ?>"><?php echo decryptData($row['todo_text']); ?></p>
                    <div class="todo-options">
                        <?php 
                        if ($row['todo_status'] != TODO_DONE) {
                            ?>
                            <button name="finish-todo"> <i class="fad fa-check"></i> </button>
                            <?php
                        }
    
                        if ($row['todo_status'] != TODO_ACTIVE) {
                            ?>
                            <button name="unfinish-todo"> <i class="fas fa-redo"></i> </button>
                            <?php
                        }                    
                        ?>
                        <button name="delete-todo"> <i class="fad fa-trash"></i> </button>
                    </div>
                </div>
            </form>
            <?php
        }

    }
    else {
        showBlankTodoActivity();
    }
    


}

function showBlankTodoActivity() {
    ?>
        <div class="no-acitivities">
            <i class="fad fa-snooze"></i>
            <h1>Great! No Activities To Show</h1>
            <p>Take a rest for now.</p>
        </div>
    <?php
}

if (isset($_POST['unfinish-todo'])) {
    setActivityStatus(TODO_ACTIVE);
}

if (isset($_POST['finish-todo'])) {
    setActivityStatus(TODO_DONE);
}

function setActivityStatus($todoStatus) {
    $con = connect();

    $todoID = decryptData($_POST['todo-id']);

    $stmt = $con->prepare(
        'UPDATE todo SET todo_status = ? WHERE todo_id = ?'
    );
    $stmt->bind_param('ii', $todoStatus, $todoID);
    if ($stmt->execute()) {
        $_SESSION['todo'] = 'changedActivityStatus';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['todo'] = 'error';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    }
}


if (isset($_POST['delete-todo'])) {
    deleteToDoActivity();
}

function deleteToDoActivity() {
    $con = connect();

    $todoID = decryptData($_POST['todo-id']);
    
    $stmt = $con->prepare(
        'DELETE FROM todo WHERE todo_id = ?'
    );
    $stmt->bind_param('i', $todoID);
    if ($stmt->execute()) {
        $_SESSION['todo'] = 'deletedActivity';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    } else {
        $_SESSION['todo'] = 'error';
        header('location: '. $_SERVER['PHP_SELF']);
        exit(0);
    }
}


?>