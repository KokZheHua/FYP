<?php

    session_start();

    $abbreviation = $_SESSION['abbreviation'];

    $student_id = $_POST['student_id'];

    $teacher_id = $_SESSION['id'];

    $unique_lesson_ids = json_decode($_POST['unique_lesson_ids']);
    $unique_practice_ids = json_decode($_POST['unique_practice_ids']);
    $unique_quizz_ids = json_decode($_POST['unique_quizz_ids']);
    $unique_exam_ids = json_decode($_POST['unique_exam_ids']);
    
    include("connection.php");
    
    if (!$con) {
        die(mysqli_connect_error());
    }

    $jsonData = file_get_contents('php://input');

    $data = json_decode($jsonData, true);

    if(isset($_SESSION['abbreviation'])){
        
        $query = "INSERT INTO student_classroom (id, abbreviation, teacher_id) VALUES ('$student_id', '$abbreviation', '$teacher_id')";
        if (mysqli_query($con, $query)) {
            $student_classroom_id = mysqli_insert_id($con);
            
            foreach ($unique_lesson_ids as $lesson_id) {
                $query_lesson = "INSERT INTO classroom_lesson (student_classroom_id, lesson_id) VALUES ('$student_classroom_id', '$lesson_id')";
                mysqli_query($con, $query_lesson);
            }
            foreach ($unique_practice_ids as $practice_id) {
                $query_practice = "INSERT INTO classroom_practice (student_classroom_id, practice_id) VALUES ('$student_classroom_id', '$practice_id')";
                mysqli_query($con, $query_practice);
            }
            foreach ($unique_quizz_ids as $quizz_id) {
                $query_quizz = "INSERT INTO classroom_quizz (student_classroom_id, quizz_id) VALUES ('$student_classroom_id', '$quizz_id')";
                mysqli_query($con, $query_quizz);
            }
            foreach ($unique_exam_ids as $exam_id) {
                $query_exam = "INSERT INTO classroom_exam (student_classroom_id, exam_id) VALUES ('$student_classroom_id', '$exam_id')";
                mysqli_query($con, $query_exam);
            }
            echo "success"; 
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
        
    mysqli_close($con);

    exit();
?>