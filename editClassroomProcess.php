<?php
    session_start();

    include("connection.php");

    if (!$con) {
        die(mysqli_connect_error());
    }

    $abbreviation = $_GET['abbreviation'];
    $teacher_id = $_GET['teacher_id'];

    $current_teacher_id = $_SESSION['current_teacher_id'];
    $current_abbreviation = $_SESSION['current_abbreviation'];

    $query = "SELECT * FROM teacher_classroom WHERE abbreviation  = '$abbreviation' AND teacher_id = '$teacher_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 0) {

        $query = "UPDATE teacher_classroom SET abbreviation = '$abbreviation', teacher_id = '$teacher_id' WHERE abbreviation = '$current_abbreviation' AND teacher_id = '$current_teacher_id'";
        
        if (mysqli_query($con, $query)) {
            echo '<script type="text/javascript">
            alert("Classroom updated successfully.");
            window.location.href = "editClassroom.php?";
            </script>';
        } else {
            echo "Error deleting course: " . mysqli_error($con);
        }
    }else{
        echo '<script type="text/javascript">
            alert("Record with abbreviation \''.$abbreviation.'\' and teacher ID \''.$teacher_id.'\' already exists.");
            window.location.href = "editClassroom_select.php?teacher_id='.$current_teacher_id.'&abbreviation='.$current_abbreviation.'";
            </script>';
    }

    mysqli_close($con);
?>