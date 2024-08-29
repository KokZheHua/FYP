<?php
    $quizz_id = $_GET['quizz_id'];

    include("connection.php");

    $query = "SELECT quizz_title, quizz_description, time_limit_min, start_time, end_time FROM quizz_data WHERE quizz_id = '$quizz_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        $quizzData = mysqli_fetch_assoc($result);
        
        echo json_encode($quizzData);
    } else {
        echo json_encode(['error' => 'Failed to retrieve practice data']);
    }

    mysqli_close($con);
?>