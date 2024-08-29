<?php
    session_start();

    include("connection.php");

    $exam_id = $_POST['exam_id'];

    if (!empty($exam_id)) {
        $query = "DELETE FROM exam_data WHERE exam_id = '$exam_id'";
        mysqli_query($con, $query);

        $query = "DELETE FROM classroom_exam WHERE exam_id = '$exam_id'";
        mysqli_query($con, $query);

        $query = "DELETE FROM exam_question WHERE exam_id = '$exam_id'";
        mysqli_query($con, $query);
    }

    mysqli_close($con);
?>