<?php
    include("connection.php");

    if (!$con) {
        die(mysqli_connect_error());
    }

    $abbreviation = $_GET['abbreviation'];
    $teacher_id = $_GET['teacher_id'];

    $query = "SELECT * FROM student_classroom WHERE abbreviation = '$abbreviation' AND teacher_id = '$teacher_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>alert("Student exists."); window.location.href = "editClassroom.php";</script>';
    }else{
        echo '<script>window.location.href = "editClassroom_select.php?abbreviation=' . $abbreviation . '&teacher_id=' . $teacher_id . '";</script>';
    }

?>