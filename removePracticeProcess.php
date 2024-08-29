<?php
    session_start();

    include("connection.php");

    $practice_id = $_POST['practice_id'];

    if (!empty($practice_id)) {
        $query = "DELETE FROM practice_data WHERE practice_id = '$practice_id'";
        mysqli_query($con, $query);

        $query = "DELETE FROM classroom_practice WHERE practice_id = '$practice_id'";
        mysqli_query($con, $query);
    }

    mysqli_close($con);
?>