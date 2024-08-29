<?php
    session_start();

    include("connection.php");

    $quizzTitle = $_POST['quizzTitle_edit'];
    $quizzDescription = $_POST['quizzDescription_edit'];
    $timeLimit = $_POST['timeLimit_edit'];
    $startTime = date("Y-m-d H:i:s", strtotime($_POST['startTime_edit']));
    $endTime = date("Y-m-d H:i:s", strtotime($_POST['endTime_edit']));
    $quizz_id = $_POST['quizz_id_edit'];

    $id = $_SESSION['id'];

    $timeLimit = empty($timeLimit) ? 'NULL' : $timeLimit;
    $startTime = empty($startTime) ? 'NULL' : $startTime;
    $endTime = empty($endTime) ? 'NULL' : $endTime;

    $query = "UPDATE quizz_data 
          SET quizz_title = '$quizzTitle', 
              quizz_description = '$quizzDescription', 
              time_limit_min = " . ($timeLimit !== 'NULL' ? $timeLimit : 'NULL') . ", 
              start_time = " . ($startTime !== 'NULL' ? "'$startTime'" : 'NULL') . ", 
              end_time = " . ($endTime !== 'NULL' ? "'$endTime'" : 'NULL') . " 
          WHERE teacher_id = '$id' AND quizz_id = '$quizz_id'";

    mysqli_query($con, $query);    
    
    mysqli_close($con);

    exit;
?>