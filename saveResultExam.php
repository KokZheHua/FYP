<?php
    
    session_start();
    
    $exam_id = $_SESSION['exam_id'];
    $student_classroom_id = $_SESSION['student_classroom_id'];

    include("connection.php");
    
    if (!$con) {
        die(mysqli_connect_error());
    }

    $jsonData = file_get_contents('php://input');

    $data = json_decode($jsonData, true);

    if(isset($_SESSION['exam_id'])){
        $totalResult = $data['totalResult'];
        $query = "INSERT INTO exam_result_data(exam_result_mark, exam_id, student_classroom_id) VALUES ('$totalResult', '$exam_id', '$student_classroom_id')";
        mysqli_query($con, $query);
        
        $query = "UPDATE classroom_exam SET exam_state = TRUE WHERE exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'";
        mysqli_query($con, $query);
    }

    mysqli_close($con);
?>