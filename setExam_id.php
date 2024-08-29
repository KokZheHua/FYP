<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $_SESSION['set_exam_id'] = $_POST['exam_id'];
    } 
?>