<?php
    $exam_id = $_GET['exam_id'];

    include("connection.php");

    $query = "SELECT exam_title, exam_description, time_limit_min, start_time, end_time FROM exam_data WHERE exam_id = '$exam_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        $examData = mysqli_fetch_assoc($result);
        
        echo json_encode($examData);
    } else {
        echo json_encode(['error' => 'Failed to retrieve practice data']);
    }

    mysqli_close($con);
?>