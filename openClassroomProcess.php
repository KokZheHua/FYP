<?php
    session_start();
    include("connection.php");

    $classroom = $_GET['classroom'];

    $id = $_SESSION['id'];

    $update_classroom_query = "UPDATE student_classroom SET access_time = NOW() WHERE id = '$id' AND abbreviation = '$classroom'";
    mysqli_query($con, $update_classroom_query);

    mysqli_close($con);

    header("Location: classroom.php?classroom=$classroom");
    exit();
?>
