<?php

    session_start();

    $abbreviation = $_SESSION['abbreviation'];
    
    include("connection.php");
    
    if (!$con) {
        die(mysqli_connect_error());
    }

    $jsonData = file_get_contents('php://input');

    $data = json_decode($jsonData, true);

    if(isset($_SESSION['abbreviation'])){
        
        $query = "UPDATE student_classroom SET teacher_feedback = '{$data['feedback']}' WHERE id = {$data['selectedStudentId']} AND abbreviation = '$abbreviation';"; 
        
        mysqli_query($con, $query);
        
    }
        
    mysqli_close($con);

    header('Content-Type: application/json');
    echo json_encode(["success" => true]);
    exit();
?>