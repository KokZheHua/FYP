<?php

    session_start();

    include("connection.php");

    $abbreviation = $_SESSION['abbreviation'];

    $selectedStudentId = $_GET['student_id'];
    $quizz_average_list = json_decode($_GET['quizz_average_list'], true);
    $exam_average_list = json_decode($_GET['exam_average_list'], true);
    $ids = json_decode($_GET['ids'], true);

    $data = array();
    for ($i = 0; $i < count($ids); $i++) {
        if ($ids[$i] == $selectedStudentId) {
            $data['quizz_average'] = $quizz_average_list[$i];
            $data['exam_average'] = $exam_average_list[$i];
            break;
        }
    }

    $query = "SELECT teacher_feedback FROM student_classroom WHERE abbreviation = '$abbreviation' AND id = '$selectedStudentId'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $data['teacher_feedback'] = $row['teacher_feedback'];
    }

    echo json_encode($data);
?>