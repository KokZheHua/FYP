<?php
    session_start();

    include("connection.php");

    $quizz_id = $_POST['quizz_id'];

    if (!empty($quizz_id)) {
        $query = "DELETE FROM quizz_data WHERE quizz_id = '$quizz_id'";
        mysqli_query($con, $query);

        $query = "DELETE FROM classroom_quizz WHERE quizz_id = '$quizz_id'";
        mysqli_query($con, $query);

        $query = "DELETE FROM quizz_question WHERE quizz_id = '$quizz_id'";
        mysqli_query($con, $query);
    }

    mysqli_close($con);
?>