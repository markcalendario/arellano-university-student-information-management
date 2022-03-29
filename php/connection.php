<?php

        
function connect() {
    $localhost = 'localhost';
    $password = '';
    $username = 'root';
    $db = 'au_student_management';

    $conn = new mysqli($localhost, $username, $password, $db);
    return $conn;
}

# CONFIGURATION




?>