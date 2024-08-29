<?php
    session_start();

    include("connection.php");

    $id = $_SESSION['id'];
    $exam_id = $_SESSION['exam_id'];
    $abbreviation = $_SESSION['abbreviation'];
    $question_id = $_SESSION['question_id_current'];
    $questionType = $_POST['questionType'];
    $question = $_POST['question'];
    $mark = $_POST['mark'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $answer = isset($_POST['correctAnswer']) ? $_POST['correctAnswer'] : '';

    $multipleQuestion = '';
    if (!empty($_POST['option1']) && !empty($_POST['option2']) && !empty($_POST['option3']) && !empty($_POST['option4'])) {
        $multipleQuestion = $option1 . '/~/' . $option2 . '/~/' . $option3 . '/~/' . $option4;
    }

    if ($multipleQuestion !== ''){
        $query_question = "UPDATE question_data 
                    SET question_title = '$question', 
                        options = '$multipleQuestion', 
                        answer = '$answer', 
                        mark = '$mark' 
                    WHERE question_id = $question_id";
    }else {
        $query_question = "UPDATE question_data 
                    SET question_title = '$question', 
                        mark = '$mark' 
                    WHERE question_id = $question_id";
    }
    $result_question = mysqli_query($con, $query_question);

    if ($result_question) {
        echo "<script>
            alert('Question created successfully');
            window.location.href='editExam.php?exam_id=".$exam_id."';
        </script>";
        exit;
    }
?>