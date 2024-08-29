<?php
    
    include("connection.php");

    if (!$con) {
        die(mysqli_connect_error());
    }

    function getClassroom() {
        $classroom = array();
        if (isset($_SESSION['id'])) {
            include("connection.php");
            if (!$con) {
                die(mysqli_connect_error());
            }
            $id = $_SESSION['id'];
            $query = "SELECT abbreviation FROM student_classroom WHERE id = $id AND show_classroom = 1 ORDER BY access_time DESC";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $classroom[] = $row['abbreviation'];
                }
            }
            mysqli_close($con);
        }         
        return $classroom;
    }

    function getClassroom_hide(){
        $classroom = array();
        if (isset($_SESSION['id'])) {
            include("connection.php");
            if (!$con) {
                die(mysqli_connect_error());
            }
            $id = $_SESSION['id'];
            $query = "SELECT abbreviation FROM student_classroom WHERE id = $id AND show_classroom = 0 ORDER BY access_time DESC";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $classroom[] = $row['abbreviation'];
                }
            }
            mysqli_close($con);
        }         
        return $classroom;
    }
?>