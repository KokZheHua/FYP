<?php
    session_start();

    include("connection.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $quizz_id = $_POST['quizz_id'];
        $question_id = $_POST['question_id'];
        $student_classroom_id = $_POST['student_classroom_id'];
        $mark = $_POST['mark'];

        $query = "UPDATE quizz_question SET score_mark = '$mark' WHERE quizz_id = '$quizz_id' AND question_id = '$question_id' AND student_classroom_id = '$student_classroom_id'";
        $result = mysqli_query($con, $query);

        if ($result) {
            echo "Mark updated successfully";
        } else {
            echo "Error updating mark: " . mysqli_error($con);
        }
    }
    mysqli_close($con);
?>