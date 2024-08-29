<?php
    include("connection.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $classroom = $_POST["classroom"];
        $id = $_POST["id"];
        $action = $_POST["action"];

        $query = "";
        if ($action == "show") {
            $query = "UPDATE student_classroom SET show_classroom = 1 WHERE abbreviation = '$classroom' AND id = '$id'";
        } elseif ($action == "hide") {
            $query = "UPDATE student_classroom SET show_classroom = 0 WHERE abbreviation = '$classroom' AND id = '$id'";
        }

        mysqli_query($con, $query);

        mysqli_close($con);
    }
?>