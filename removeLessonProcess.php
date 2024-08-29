<?php
    session_start();

    include("connection.php");

    $lesson_id = $_POST['lesson_id'];

    if (!empty($lesson_id)) {
        $query = "DELETE FROM lesson_data WHERE lesson_id = '$lesson_id'";
        mysqli_query($con, $query);

        $query = "DELETE FROM classroom_lesson WHERE lesson_id = '$lesson_id'";
        mysqli_query($con, $query);
    }

    mysqli_close($con);
?>