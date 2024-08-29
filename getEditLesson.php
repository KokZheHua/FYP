<?php
    $lesson_id = $_GET['lesson_id'];

    include("connection.php");

    $query = "SELECT lesson_title, lesson_description FROM lesson_data WHERE lesson_id = '$lesson_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        $lessonData = mysqli_fetch_assoc($result);
        
        echo json_encode($lessonData);
    } else {
        echo json_encode(['error' => 'Failed to retrieve lesson data']);
    }

    mysqli_close($con);
?>