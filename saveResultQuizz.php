<?php

    session_start();

    $quizz_id = $_SESSION['quizz_id'];
    $student_classroom_id = $_SESSION['student_classroom_id'];

    include("connection.php");
    
    if (!$con) {
        die(mysqli_connect_error());
    }

    $jsonData = file_get_contents('php://input');

    $data = json_decode($jsonData, true);

    if(isset($_SESSION['quizz_id'])){
        $totalResult = $data['totalResult'];
        $query = "SELECT COUNT(*) AS count FROM quizz_result_data WHERE quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];

        if ($count > 0) {
            $query = "UPDATE quizz_result_data SET quizz_result_mark = '$totalResult' WHERE quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'";
        } else {
            $query = "INSERT INTO quizz_result_data(quizz_result_mark, quizz_id, student_classroom_id) VALUES ('$totalResult', '$quizz_id', '$student_classroom_id')";
        }
        mysqli_query($con, $query);
    }

    mysqli_close($con);
?>