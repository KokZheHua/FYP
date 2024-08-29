<?php
    session_start();

    include("connection.php");

    $teacher_id = $_GET['teacher_id'];
    $abbreviation = $_GET['classroom'];
    
    $query_check = "SELECT abbreviation, teacher_id FROM teacher_classroom WHERE abbreviation = '$abbreviation' AND teacher_id = '$teacher_id'";
    $result_check = mysqli_query($con, $query_check);
    if(mysqli_num_rows($result_check) > 0) {
        echo '<script type="text/javascript">
        alert("This classroom assignment already exists.");
        window.location.href = "createClassroom.php?";
        </script>';
    }else{
        $query = "INSERT INTO teacher_classroom (teacher_id, abbreviation) VALUES ('$teacher_id', '$abbreviation')";

        mysqli_query($con, $query);

        echo '<script type="text/javascript">
        alert("Successful creation of classroom");
        window.location.href = "createClassroom.php?";
        </script>';
    }
?>
    