<?php
    include("connection.php");

    if (!$con) {
        die(mysqli_connect_error());
    }

    $abbreviation = $_GET['abbreviation'];

    $query = "SELECT * FROM teacher_classroom WHERE abbreviation = '$abbreviation'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>alert("Teacher exists."); window.location.href = "editCourse.php";</script>';
    }else{
        echo '<script>window.location.href = "editCourse_input.php?abbreviation=' . $abbreviation . '";</script>';
    }

?>