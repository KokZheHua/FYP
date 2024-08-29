<?php 

    session_start();

    $_SESSION['quizz_id'] = $_GET['quizz_id'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Quizz Review</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <?php include("color_theme.php"); ?>
        <link rel="stylesheet" href="quizz.css">
    </head>
    <body class="w3-light-grey w3-content" style="max-width:100%;">
        <div class="w3-sidebar-quizz w3-card w3-collapse w3-white w3-animate-left w3-bar-block" style="display: block; width:300px;" id="mySidebar"><br>
            <button onclick="close_side_bar()" class="side_bar_close_btn w3-bar-item w3-button w3-hover-black">&#x2715; Close</button>
            <div id="question_sideBar"></div>
        </div>

        <button onclick="toggle_side_bar()" id = "side_bar" class="side_bar_open_btn w3-bar-item w3-white w3-button w3-black" style="display: none;">&#x2630; Question</button>
        <button onclick="exitConfirmation()" id = "exit_btn" class="w3-white w3-button w3-black" style="display: none;">&#x274C; Exit</button><br>
        
        <div class="w3-main small-padding" style="margin-left:300px; margin-top:60px">
            <div class="w3-row-padding">
                <div class="w3-card-4 w3-margin w3-white">
                    <h1 id='titleQuiz'></h1>
                    <div id="quiz" class="question_item"></div>
                    <div id="result" class="result"></div>
                    <button id="edit" class="button w3-button w3-padding-large w3-white w3-border question_btn">Edit Question</button>
                    <button id="remove" class="button w3-button w3-padding-large w3-white w3-border question_btn">Remove Question</button>
                    <button id="exit" class="button w3-button w3-padding-large w3-white w3-border question_btn">&#x274C; Exit Quizz</button>
                </div>
            </div>
        </div>
        <?php include('editFunctionQuizz.php');?>
    </body>
</html> 