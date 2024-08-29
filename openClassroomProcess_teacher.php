<?php
    session_start();
    include("connection.php");

    $classroom = $_GET['classroom'];

    $id = $_SESSION['id'];

    $update_classroom_query = "UPDATE teacher_classroom SET access_time = NOW() WHERE teacher_id = '$id' AND abbreviation = '$classroom'";
    mysqli_query($con, $update_classroom_query);

    mysqli_close($con);

    header("Location: classroom_teacher.php?classroom=$classroom");
    exit();
?>
