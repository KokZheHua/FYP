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
        foreach ($data['answer'] as $index => $answerToSave) {
            $question_id = $data['question_id'][$index];
            if ($answerToSave == '') {
                $query = "UPDATE quizz_question SET user_answer_quizz = NULL WHERE question_id = '$question_id' AND quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'"; 
            } else {
                $query = "UPDATE quizz_question SET user_answer_quizz = '$answerToSave' WHERE question_id = '$question_id' AND quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'"; 
            }
            mysqli_query($con, $query);
        }
        
        foreach ($data['score'] as $index => $markToSave) {
            $question_id = $data['question_id'][$index];
            if($markToSave == ''){
                $query = "UPDATE quizz_question SET score_mark = NULL WHERE question_id = '$question_id' AND quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'"; 
            }else{
                $query = "UPDATE quizz_question SET score_mark = '$markToSave' WHERE question_id = '$question_id' AND quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'"; 
            }
            mysqli_query($con, $query);
        }

        $query_state = "UPDATE classroom_quizz SET quizz_state = 1 WHERE quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'";
        mysqli_query($con, $query_state);
    }

    mysqli_close($con);

    header('Content-Type: application/json');
    echo json_encode(["success" => true]);
    exit();
?>