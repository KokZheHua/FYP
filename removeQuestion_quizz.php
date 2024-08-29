<?php
    session_start();
    
    include("connection.php");
    
    $question_id = $_GET['question_id'];
    
    if (!empty($question_id)) {
        $query_exam_question = "DELETE FROM quizz_question WHERE question_id = '$question_id'";
        mysqli_query($con, $query_exam_question);
        
        $query_question_data = "DELETE FROM question_data WHERE question_id = '$question_id'";
        mysqli_query($con, $query_question_data);  
    } 

    mysqli_close($con);
?>

<script>
    window.location.href = 'editQuizz.php?quizz_id=<?php echo $_SESSION['quizz_id'] ?>';
</script>