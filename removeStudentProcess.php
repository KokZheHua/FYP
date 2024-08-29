<?php

    session_start();

    $abbreviation = $_SESSION['abbreviation'];

    $student_id_remove = $_POST['student_id'];
    
    include("connection.php");
    
    if (!$con) {
        die(mysqli_connect_error());
    }

    if(isset($_SESSION['abbreviation'])){
        
        $query = "DELETE FROM student_classroom WHERE abbreviation = '$abbreviation' AND id = '$student_id_remove'";

        if (mysqli_query($con, $query)) {
            echo "success"; 
        } else {
            echo "failed: " . mysqli_error($con); ;
        }
    }
        
    mysqli_close($con);

    exit();
?>