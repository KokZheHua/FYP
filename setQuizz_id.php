<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $_SESSION['set_quizz_id'] = $_POST['quizz_id'];
    } 
?>