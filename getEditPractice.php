<?php
    $practice_id = $_GET['practice_id'];

    include("connection.php");

    $query = "SELECT practice_title, practice_description FROM practice_data WHERE practice_id = '$practice_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        $practiceData = mysqli_fetch_assoc($result);
        
        echo json_encode($practiceData);
    } else {
        echo json_encode(['error' => 'Failed to retrieve practice data']);
    }

    mysqli_close($con);
?>