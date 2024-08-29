<?php
    session_start();

    include("connection.php");

    $examTitle = $_POST['examTitle_edit'];
    $examDescription = $_POST['examDescription_edit'];
    $timeLimit = $_POST['timeLimit_edit_exam'];
    $startTime = date("Y-m-d H:i:s", strtotime($_POST['startTime_edit_exam']));
    $endTime = date("Y-m-d H:i:s", strtotime($_POST['endTime_edit_exam']));
    $exam_id = $_POST['exam_id_edit'];

    $id = $_SESSION['id'];
    
    $timeLimit = empty($timeLimit) ? 'NULL' : $timeLimit;
    $startTime = empty($startTime) ? 'NULL' : $startTime;
    $endTime = empty($endTime) ? 'NULL' : $endTime;

    $query = "UPDATE exam_data 
          SET exam_title = '$examTitle', 
              exam_description = '$examDescription', 
              time_limit_min = " . ($timeLimit !== 'NULL' ? $timeLimit : 'NULL') . ", 
              start_time = " . ($startTime !== 'NULL' ? "'$startTime'" : 'NULL') . ", 
              end_time = " . ($endTime !== 'NULL' ? "'$endTime'" : 'NULL') . " 
          WHERE teacher_id = '$id' AND exam_id = '$exam_id'";

    mysqli_query($con, $query);    
    
    mysqli_close($con);

    exit;
?>