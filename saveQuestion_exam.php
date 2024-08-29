<?php
    session_start();

    include("connection.php");

    $id = $_SESSION['id'];
    $abbreviation = $_SESSION['abbreviation'];
    $questionType = $_POST['questionType'];
    $question = $_POST['question'];
    $mark = $_POST['mark'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $answer = isset($_POST['correctAnswer']) ? $_POST['correctAnswer'] : '';
    $noFirstSubmit = $_POST['noFirstSubmit']; 
    $examTitle = $_SESSION['examTitle'];
    $examDescription = $_SESSION['examDescription'];
    $timeLimit = $_SESSION['timeLimit'];
    $startTime = date("Y-m-d H:i:s", strtotime($_SESSION['startTime']));
    $endTime = date("Y-m-d H:i:s", strtotime($_SESSION['endTime']));
    $student_classroom_ids = [];

    $timeLimit = empty($timeLimit) ? 'NULL' : $timeLimit;
    $startTime = empty($startTime) ? 'NULL' : $startTime;
    $endTime = empty($endTime) ? 'NULL' : $endTime; 

    $query2 = "SELECT student_classroom_id FROM student_classroom WHERE abbreviation = '$abbreviation' AND teacher_id = '$id'";
    $result2 = mysqli_query($con, $query2);

    if ($result2) {
        while ($row2 = mysqli_fetch_assoc($result2)) {
            $student_classroom_ids[] = $row2['student_classroom_id'];
        }
    }

    if ($noFirstSubmit == 'no') {
        $_SESSION['checkfirstSubmit'] = true;
        $query = "INSERT INTO exam_data (exam_title, exam_description, teacher_id, time_limit_min, start_time, end_time)
            VALUES ('$examTitle', '$examDescription', '$id', " . ($timeLimit !== 'NULL' ? $timeLimit : 'NULL') . ", " . ($startTime !== 'NULL' ? "'$startTime'" : 'NULL') . ", " . ($endTime !== 'NULL' ? "'$endTime'" : 'NULL') . ")";

        if (mysqli_query($con, $query)) {
            $exam_id = mysqli_insert_id($con);
            $_SESSION['quizz_id_new'] = $exam_id;
        }

        foreach($student_classroom_ids as $student_classroom_id){
            $query_classroom_quizz = "INSERT INTO classroom_exam (student_classroom_id, exam_id)
                    VALUES ('$student_classroom_id', '$exam_id')";
            mysqli_query($con, $query_classroom_quizz);
        }
    }

    $quizz_id2 = $_SESSION['quizz_id_new'];

    $multipleQuestion = '';
    if (!empty($_POST['option1']) && !empty($_POST['option2']) && !empty($_POST['option3']) && !empty($_POST['option4'])) {
        $multipleQuestion = $option1 . '/~/' . $option2 . '/~/' . $option3 . '/~/' . $option4;
    }

    if ($multipleQuestion !== ''){
        $query_question = "INSERT INTO question_data (question_title, options, answer, mark) 
            VALUES ('$question', '$multipleQuestion', '$answer', '$mark')";
    }else {
        $query_question = "INSERT INTO question_data (question_title, options, answer, mark) 
            VALUES ('$question', null, null, '$mark')";
    }
    $result_question = mysqli_query($con, $query_question);
    $question_id = mysqli_insert_id($con);

    foreach($student_classroom_ids as $student_classroom_id){
        $query_quizz_question = "INSERT INTO exam_question (exam_id, question_id, student_classroom_id)
                VALUES ('$quizz_id2', '$question_id', '$student_classroom_id')";
        mysqli_query($con, $query_quizz_question);
    }

    if ($result_question) {
        echo "<script>
            alert('Question created successfully');
            window.location.href='createQuestion_exam.php';
        </script>";
        exit;
    }
?>