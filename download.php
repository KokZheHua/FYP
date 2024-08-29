<?php
    include("connection.php");

    if(isset($_GET['lesson_id'])){
        $lesson_id = $_GET['lesson_id'];

        $query = "SELECT lesson_title, lesson_file FROM lesson_data WHERE lesson_id = '$lesson_id'";
        $result = mysqli_query($con, $query);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $lesson_title = $row['lesson_title'];
                $lesson_file = $row['lesson_file'];
                
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"$lesson_title.pdf\"");
        
                echo $lesson_file;
                exit;
            } 
            else {
                echo "Lesson not found!";
            }
        }
        else {
            echo "Error: " . mysqli_error($con);
        }
    }
    else if(isset($_GET['practice_id'])){
        $practice_id = $_GET['practice_id'];

        $query = "SELECT practice_title, practice_file FROM practice_data WHERE practice_id = '$practice_id'";
        $result = mysqli_query($con, $query);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $practice_title = $row['practice_title'];
                $practice_file = $row['practice_file'];
                
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"$practice_title.pdf\"");
        
                echo $practice_file;
                exit;
            } 
            else {
                echo "Lesson not found!";
            }
        }
        else {
            echo "Error: " . mysqli_error($con);
        }
    }
    else {
        echo "Unknown!";
    }
?>