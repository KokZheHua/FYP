<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['examTitle'] = $_POST['examTitle'];
        $_SESSION['examDescription'] = $_POST['examDescription'];
        $_SESSION['timeLimit'] = !empty($_POST['timeLimit']) ? $_POST['timeLimit'] : NULL;
        $_SESSION['startTime'] = !empty($_POST['startTime']) ? $_POST['startTime'] : NULL;
        $_SESSION['endTime'] = !empty($_POST['endTime']) ? $_POST['endTime'] : NULL;
        $_SESSION['checkfirstSubmit'] = false;
        $_SESSION['quizz_id_new'] = 0;
        exit;
    }
?>