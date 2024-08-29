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
        foreach ($data['answer'] as $index => $answerToSave) {
            $question_id = $data['question_id'][$index];
            if ($answerToSave == '') {
                $query = "UPDATE exam_question SET user_answer_exam = NULL WHERE question_id = '$question_id' AND exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'"; 
            }else{
                $query = "UPDATE exam_question SET user_answer_exam = '$answerToSave' WHERE question_id = '$question_id' AND exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'"; 
            }
                mysqli_query($con, $query);
        }
        foreach ($data['score'] as $index => $markToSave) {
            $question_id = $data['question_id'][$index];
            if($markToSave == ''){
                $query = "UPDATE exam_question SET score_mark = NULL WHERE question_id = '$question_id' AND exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'"; 
            }else{
                $query = "UPDATE exam_question SET score_mark = '$markToSave' WHERE question_id = '$question_id' AND exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'"; 
            }
            mysqli_query($con, $query);
        }

        $query_state = "UPDATE classroom_exam SET exam_state = 1 WHERE exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'";
        mysqli_query($con, $query_state);
    }

    mysqli_close($con);

    header('Content-Type: application/json');
    echo json_encode(["success" => true]);
    exit();
?>