<?php
    session_start();
    
    include("connection.php");

    $id = $_SESSION['id'];

    $abbreviation = $_GET['classroom'];

    $query = "SELECT classroom_name FROM classroom_type WHERE abbreviation = '$abbreviation'";
    
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $classroom_name = $row['classroom_name'];
    } else {
        $classroom_name = "Unknown";
    }

    $query2 = "SELECT student_classroom_id, teacher_id, teacher_feedback, unread_forum, unread_teacher FROM student_classroom WHERE abbreviation = '$abbreviation' AND id = '$id'";

    $result2 = mysqli_query($con, $query2);
    if (mysqli_num_rows($result2) == 1) {
        $row2 = mysqli_fetch_assoc($result2);
        $_SESSION['student_classroom_id'] = $row2['student_classroom_id'];
        $_SESSION['teacher_id'] = $row2['teacher_id'];
        $unread_forum = $row2['unread_forum'];
        $unread_teacher = $row2['unread_teacher'];
        $teacher_feedback = $row2['teacher_feedback'];
        $student_classroom_id = $_SESSION['student_classroom_id'];
    } else {
        $student_classroom_id = "Unknown";
    }

    $quizz_mark_total = 0;
    $quizz_average = 0;
    $query3 = "SELECT quizz_result_mark FROM quizz_result_data WHERE student_classroom_id = '$student_classroom_id'";
    $result3 = mysqli_query($con, $query3);
    if (mysqli_num_rows($result3) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_assoc($result3)) {
            if($row['quizz_result_mark'] !== null){
                $quizz_mark_total += $row['quizz_result_mark'];
                $i++;
            }
        }
        $quizz_average = $i > 0 ? number_format($quizz_mark_total / $i, 2) : 0;
        if($quizz_average > 100){
            $quizz_average = 100;
        }
    }

    $exam_mark_total = 0;
    $exam_average = 0;
    $query4 = "SELECT exam_result_mark FROM exam_result_data WHERE student_classroom_id = '$student_classroom_id'";
    $result4 = mysqli_query($con, $query4);
    if (mysqli_num_rows($result4) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_assoc($result4)) {
            if($row['exam_result_mark'] !== null){
                $exam_mark_total += $row['exam_result_mark'];
                $i++;
            }
        }
        $exam_average = $i > 0 ? number_format($exam_mark_total / $i, 2) : 0;
        if($exam_average > 100){
            $exam_average = 100;
        }
    }

    $query5 = "SELECT color_theme, unread_admin FROM users WHERE id = '$id'";
    $result5 = mysqli_query($con, $query5);
    if ($result5) {
        $num_rows = mysqli_num_rows($result5);
        if ($num_rows > 0) {
            $row = mysqli_fetch_assoc($result5);
            $color_theme = $row['color_theme'];
            $unread_admin = $row['unread_admin'];
            echo '<script>';
            echo 'const selectedTheme = "' . $color_theme . '";';
            echo 'const xhr = new XMLHttpRequest();';
            echo 'xhr.open("POST", "update_theme.php", true);';
            echo 'xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");';
            echo 'xhr.send("theme=" + encodeURIComponent(selectedTheme));';
            echo '</script>';
        }
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Classroom</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <?php include("color_theme.php"); ?>
        <link rel="stylesheet" href="classroom.css">
        <script src="classroom_function.js"></script>
        <script src="logoutConfirmation.js"></script>
    </head>
    
    <body class="w3-light-grey w3-content w3-theme-l5" style="max-width:100%;">
        <div class="w3-top">
            <div class="w3-bar w3-white w3-wide w3-padding w3-card w3-theme-l4">
                <button onclick="toggle_side_bar()" id = "side_bar" class="side_bar_open_btn w3-bar-item w3-white w3-button w3-hover-black w3-black">&#x2630; Side Bar</button>
                <a href="index.php" class="w3-bar-item w3-button"><b>ALH</b> Accessible Learning Hub</a>
                <button class="w3-bar-item w3-white w3-border w3-button w3-hover-black" onclick="adjustFontSize(16)">Small</button>
                <button class="w3-bar-item w3-white w3-border w3-button w3-hover-black" onclick="adjustFontSize(20)">Medium</button>
                <button class="w3-bar-item w3-white w3-border w3-button w3-hover-black" onclick="adjustFontSize(24)">Large</button>
                <div class="w3-right">
                    <a href="consult_admin.php" class="w3-bar-item w3-button <?php echo ($unread_admin == 1 ? ' flag_css' : '') ?>">Consult Admin</a>
                    <a href="#" class="w3-bar-item w3-button" onclick="chooseColorTheme()">Color Theme</a>
                    <?php
                        if (isset($_SESSION['id'])) {
                            echo '<a href="profile.php?&previous_page=classroom&classroom_abbreviation='.$abbreviation.'" class="w3-bar-item w3-button">Profile</a>';
                            echo '<a href="#" onclick="confirmLogout()" class="w3-bar-item w3-button">Logout</a>';
                        } else {
                            echo '<a href="registration.php" class="w3-bar-item w3-button">Sign Up</a>';
                            echo '<a href="login.php" class="w3-bar-item w3-button">Login</a>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div id="colorThemeModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeColorThemeModal()" class="w3-button w3-display-topright">&times;</span>
                    <h2>Theme Color Selection</h2>
                    <form id="themeForm" class="w3-container">
                        <h3><label for="themeSelect">Select Theme Color:</label></h3>
                        <select id="themeSelect" class="w3-select">
                            <option value="default">Default</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-indigo.css">Indigo</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-teal.css">Teal</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-deep-orange.css">Deep Orange</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-pink.css">Pink</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-deep-purple.css">Deep Purple</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-cyan.css">Cyan</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-green.css">Green</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-blue.css">Blue</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-blue-grey.css">Blue Grey</option>
                            <option value="https://www.w3schools.com/lib/w3-theme-grey.css">Grey</option>
                        </select>
                        <button type="submit" class="w3-margin w3-button w3-black w3-border">Apply</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="w3-sidebar-classroom w3-card w3-collapse w3-white w3-animate-left w3-bar-block w3-theme-l4" style="display: block; width:300px;" id="mySidebar"><br>
            <button onclick="close_side_bar()" class="side_bar_close_btn w3-bar-item w3-button w3-hover-black">&#x2715; Close</button>
            <a href="#" onclick="setActive('course_resources'); showCourseResource()" id="course_resources" class="side_bar_item w3-bar-item w3-button w3-padding w3-text-teal">Course Resources</a> 
            <a href="consult_teacher.php?&abbreviation=<?php echo $abbreviation; ?>&student_classroom_id=<?php echo $student_classroom_id; ?>" onclick="setActive('cousult_teacher')" id="cousult_teacher" class="side_bar_item w3-bar-item w3-button w3-padding">Consult teacher</a>
            <a href="social_zone.php?&abbreviation=<?php echo $abbreviation; ?>&student_classroom_id=<?php echo $student_classroom_id; ?>" onclick="setActive('social_zone')" id="social_zone" class="side_bar_item w3-bar-item w3-button w3-padding">Social Zone</a> 
            <a href="#" onclick="setActive('course_summary'); showCourseSummary()" id="course_summary" class="side_bar_item w3-bar-item w3-button w3-padding">Course Summary</a>
        </div>

        <div class="w3-main small-padding" style="margin-left:300px; margin-top:60px">
            <div id="courseResource" class="w3-container">
                <h1><b><?php echo $classroom_name ?></b></h1>
                <div class="w3-section w3-bottombar w3-padding-16">
                    <span class="w3-margin-right">Filter:</span> 
                    <button onclick="filterBtn('all_btn'); displayList(null)" class="w3-button w3-card w3-black filter_btn" id="all_btn">ALL</button>
                    <button onclick="filterBtn('lesson_btn'); displayList('lesson_list')" class="w3-button w3-card w3-white filter_btn" id="lesson_btn">Lesson</button>
                    <button onclick="filterBtn('practice_btn'); displayList('practice_list')" class="w3-button w3-card w3-white filter_btn" id="practice_btn">Practice</button>
                    <button onclick="filterBtn('quizz_btn'); displayList('quizz_list')" class="w3-button w3-card w3-white filter_btn" id="quizz_btn">Quizz</button>
                    <button onclick="filterBtn('exam_btn'); displayList('exam_list')" class="w3-button w3-card w3-white filter_btn" id="exam_btn">Exam</button>
                </div>
            
                <div class="w3-row-padding">
                    <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                        <div class="w3-container item_list" id="lesson_list">
                            <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Lesson</b></h3>
                            <div class="w3-row-padding">
                                <?php
                                    $query = "SELECT lesson_id FROM classroom_lesson WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        $lesson_id_list = [];
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $lesson_id_list[] = $row['lesson_id'];
                                        }
                                        foreach ($lesson_id_list as $lesson_id){
                                            $query = "SELECT lesson_title, lesson_description, lesson_file FROM lesson_data WHERE lesson_id = '$lesson_id'";
                                            $result = mysqli_query($con, $query);
                                            if (mysqli_num_rows($result) > 0) {
                                                $totalItems = count($lesson_id_list);
                                                $totalPages_lesson = ceil($totalItems / 6);
                                                $_SESSION['totalItem_lesson_' . $student_classroom_id] = $totalItems;
                                                $_SESSION['totalPages_lesson'] = $totalPages_lesson;
                                                $row = mysqli_fetch_assoc($result);
                                                $lesson_title = $row['lesson_title'];
                                                $lesson_description = $row['lesson_description'];
                                                $lesson_file = $row['lesson_file'];
                                                echo'<div class="w3-third w3-container w3-margin-bottom display_lesson">';
                                                    echo'<p><b>'. $lesson_title .'</b></p>';
                                                    echo'<p>'. $lesson_description .'</p>';
                                                    echo'<a href="'. $lesson_file .'" class="w3-button w3-padding-large w3-white w3-border download_btn w3-theme-l3">Download Lesson</a>';
                                                    echo '<button class="w3-bar-item w3-white w3-button w3-hover-black" onclick="speak(\'' . $lesson_title . '\', \'' . $lesson_description . '\')"><b>&#128264; Hear</b></button></p>';
                                                echo'</div>';
                                            }
                                        }
                                        echo '<div class="w3-bar w3-text-theme">';
                                            echo '<button class="prev-display_lesson w3-bar-item w3-button w3-hover-black">«</button>';
                                            for ($i = 1; $i <= $totalPages_lesson; $i++) {
                                                echo '<button class="display_lesson_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                            }
                                            echo '<button class="next-display_lesson w3-bar-item w3-button w3-hover-black">»</button>';
                                        echo '</div>';
                                        include('display_lesson_btn.php');
                                    }
                                    else{
                                        echo '<div class="w3-third w3-container w3-margin-bottom">';
                                            echo '<p><b>No Lesson</b></p>';
                                        echo '</div>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                        <div class="w3-container item_list" id="practice_list">
                            <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Practice</b></h3>
                            <div class="w3-row-padding">
                                <?php
                                    $query = "SELECT practice_id FROM classroom_practice WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        $practice_id_list = [];
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $practice_id_list[] = $row['practice_id'];
                                        }
                                        foreach ($practice_id_list as $practice_id){
                                            $query = "SELECT practice_title, practice_description, practice_file FROM practice_data WHERE practice_id = '$practice_id'";
                                            $result = mysqli_query($con, $query);
                                            if (mysqli_num_rows($result) > 0) {
                                                $totalItems = count($practice_id_list);
                                                $totalPages_practice = ceil($totalItems / 6);
                                                $_SESSION['totalItem_practice_' . $student_classroom_id] = $totalItems;
                                                $_SESSION['totalPages_practice'] = $totalPages_practice;
                                                $row = mysqli_fetch_assoc($result);
                                                $practice_title = $row['practice_title'];
                                                $practice_description = $row['practice_description'];
                                                $practice_file = $row['practice_file'];
                                                echo'<div class="w3-third w3-container w3-margin-bottom display_practice">';
                                                    echo'<p><b>'. $practice_title .'</b></p>';
                                                    echo'<p>'. $practice_description .'</p>';
                                                    echo'<a href="'. $practice_file .'" class="w3-button w3-padding-large w3-white w3-border download_btn w3-theme-l3">Download Practice</a>';
                                                    echo '<button class="w3-bar-item w3-white w3-button w3-hover-black" onclick="speak(\'' . $practice_title . '\', \'' . $practice_description . '\')"><b>&#128264; Hear</b></button></p>';
                                                echo'</div>';
                                            }
                                        }
                                        echo '<div class="w3-bar w3-text-theme">';
                                            echo '<button class="prev-display_practice w3-bar-item w3-button w3-hover-black">«</button>';
                                            for ($i = 1; $i <= $totalPages_practice; $i++) {
                                                echo '<button class="display_practice_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                            }
                                            echo '<button class="next-display_practice w3-bar-item w3-button w3-hover-black">»</button>';
                                        echo '</div>';
                                        include('display_practice_btn.php');
                                    }
                                    else{
                                        echo '<div class="w3-third w3-container w3-margin-bottom">';
                                            echo '<p><b>No practice</b></p>';
                                        echo '</div>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                        <div class="w3-container item_list" id="quizz_list">
                            <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Quizz</b></h3>
                            <div class="w3-row-padding">
                                <?php
                                    $query = "SELECT quizz_id FROM classroom_quizz WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        $quizz_id_list = [];
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $quizz_id_list[] = $row['quizz_id'];
                                        }
                                        foreach ($quizz_id_list as $quizz_id){
                                            $query = "SELECT quizz_title, quizz_description FROM quizz_data WHERE quizz_id = '$quizz_id'";
                                            $result = mysqli_query($con, $query);
                                            if (mysqli_num_rows($result) > 0) {
                                                $totalItems = count($quizz_id_list);
                                                $totalPages_quizz = ceil($totalItems / 6);
                                                $_SESSION['totalItem_quizz_' . $student_classroom_id] = $totalItems;
                                                $_SESSION['totalPages_quizz'] = $totalPages_quizz;
                                                $row = mysqli_fetch_assoc($result);
                                                $quizz_title = $row['quizz_title'];
                                                $quizz_description = $row['quizz_description'];
                                                echo'<div class="w3-third w3-container w3-margin-bottom display_quizz">';
                                                    echo'<p><b>'. $quizz_title .'</b></p>';
                                                    echo'<p>'. $quizz_description .'</p>';
                                                    echo '<button class="w3-bar-item w3-white w3-button w3-hover-black" onclick="speak(\'' . $quizz_title . '\', \'' . $quizz_description . '\')"><b>&#128264; Hear</b></button></p>';
                                                    $query4 = "SELECT start_time, end_time FROM quizz_data WHERE quizz_id = '$quizz_id'";
                                                    $result4 = mysqli_query($con, $query4);
                                                    if ($result4 && mysqli_num_rows($result4) > 0) {
                                                        $row = mysqli_fetch_assoc($result4);
                                                        $start_time = $row['start_time'];
                                                        $end_time = $row['end_time'];

                                                        if ($start_time === null && $end_time === null) {
                                                            echo'<a href="quizz.php?quizz_id='. $quizz_id .'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Start quizz</a><br>';
                                                        } else {
                                                            $start_timestamp = strtotime($start_time);
                                                            $end_timestamp = strtotime($end_time);
                                                            $current_time = time();
                                                            
                                                            if ($current_time >= $start_timestamp && $current_time <= $end_timestamp) {
                                                                echo'<a id="' . $quizz_id . 'quizz_link" href="quizz.php?quizz_id='. $quizz_id .'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Start quizz</a><br>';
                                                                $remaining_time = $end_timestamp - $current_time;
                                                                echo '<div id="' . $quizz_id . 'quizz_link_countdown"></div>';
                                                                echo '<script>startCountdown(' . $remaining_time . ', "' . $quizz_id . 'quizz_link");</script>';
                                                            } else {
                                                                echo 'Cannot access the quizz.';
                                                            }
                                                        }
                                                    }
                                                    $query = "SELECT user_answer_quizz FROM quizz_question WHERE quizz_id = '$quizz_id'";
                                                    $result = mysqli_query($con, $query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $user_answer_quizz = $row['user_answer_quizz'];
                                                        if ($user_answer_quizz !== null) {
                                                            echo'<br><a href="reviewQuizz.php?quizz_id='. $quizz_id .'&student_classroom_id='.$student_classroom_id.'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Review quizz</a>';
                                                            break;
                                                        }
                                                    }
                                                echo'</div>';
                                            }
                                        }
                                        echo '<div class="w3-bar w3-text-theme">';
                                            echo '<button class="prev-display_quizz w3-bar-item w3-button w3-hover-black">«</button>';
                                            for ($i = 1; $i <= $totalPages_quizz; $i++) {
                                                echo '<button class="display_quizz_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                            }
                                            echo '<button class="next-display_quizz w3-bar-item w3-button w3-hover-black">»</button>';
                                        echo '</div>';
                                        include('display_quizz_btn.php');
                                    }
                                    else{
                                        echo '<div class="w3-third w3-container w3-margin-bottom">';
                                            echo '<p><b>No Quizz</b></p>';
                                        echo '</div>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                        <div class="w3-container item_list" id="exam_list">
                            <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Exam</b></h3>
                            <div class="w3-row-padding">
                                <?php
                                    $query = "SELECT exam_id FROM classroom_exam WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        $exam_id_list = [];
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $exam_id_list[] = $row['exam_id'];
                                        }
                                        foreach ($exam_id_list as $exam_id){
                                            $query2 = "SELECT exam_title, exam_description FROM exam_data WHERE exam_id = '$exam_id'";
                                            $result2 = mysqli_query($con, $query2);
                                            if (mysqli_num_rows($result2) > 0) {
                                                $totalItems = count($exam_id_list);
                                                $totalPages_exam = ceil($totalItems / 6);
                                                $_SESSION['totalItem_exam_' . $student_classroom_id] = $totalItems;
                                                $_SESSION['totalPages_exam'] = $totalPages_exam;
                                                $row = mysqli_fetch_assoc($result2);
                                                $exam_title = $row['exam_title'];
                                                $exam_description = $row['exam_description'];
                                                echo'<div class="w3-third w3-container w3-margin-bottom display_exam">';
                                                    echo'<p><b>'. $exam_title .'</b></p>';
                                                    echo'<p>'. $exam_description .'</p>';
                                                    echo '<button class="w3-bar-item w3-white w3-button w3-hover-black" onclick="speak(\'' . $exam_title . '\', \'' . $exam_description . '\')"><b>&#128264; Hear</b></button></p>';
                                                    $query3 = "SELECT exam_state FROM classroom_exam WHERE exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'";
                                                    $result3 = mysqli_query($con, $query3);
                                                    if ($result3) {
                                                        $row = mysqli_fetch_assoc($result3);
                                                        $exam_state = $row['exam_state'];
                                                        if ($exam_state == false) {
                                                            $query4 = "SELECT start_time, end_time FROM exam_data WHERE exam_id = '$exam_id'";
                                                            $result4 = mysqli_query($con, $query4);
                                                            if ($result4 && mysqli_num_rows($result4) > 0) {
                                                                $row = mysqli_fetch_assoc($result4);
                                                                $start_time = $row['start_time'];
                                                                $end_time = $row['end_time'];

                                                                if ($start_time === null && $end_time === null) {
                                                                    echo '<a href="exam.php?exam_id='. $exam_id .'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Start exam</a><br>';
                                                                } else {
                                                                    $start_timestamp = strtotime($start_time);
                                                                    $end_timestamp = strtotime($end_time);
                                                                    $current_time = time();
                                                                    if ($current_time >= $start_timestamp && $current_time <= $end_timestamp) {
                                                                        echo '<a id="' . $exam_id . 'exam_link" href="exam.php?exam_id='. $exam_id .'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Start exam</a><br>';
                                                                        $remaining_time = $end_timestamp - $current_time;
                                                                        echo '<div id="' . $exam_id . 'exam_link_countdown"></div>';
                                                                        echo '<script>startCountdown(' . $remaining_time . ', "' . $exam_id . 'exam_link");</script>';
                                                                    } else {
                                                                        echo 'Cannot access the exam.';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $query = "SELECT user_answer_exam FROM exam_question WHERE exam_id = '$exam_id'";
                                                    $result = mysqli_query($con, $query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $user_answer_exam = $row['user_answer_exam'];
                                                        if ($user_answer_exam !== null) {
                                                            echo'<br><a href="reviewExam.php?exam_id='. $exam_id .'&student_classroom_id='.$student_classroom_id.'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Review exam</a>';
                                                            break;
                                                        }
                                                    }
                                                echo'</div>';
                                            }
                                        }
                                        echo '<div class="w3-bar w3-text-theme">';
                                            echo '<button class="prev-display_exam w3-bar-item w3-button w3-hover-black">«</button>';
                                            for ($i = 1; $i <= $totalPages_exam; $i++) {
                                                echo '<button class="display_exam_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                            }
                                            echo '<button class="next-display_exam w3-bar-item w3-button w3-hover-black">»</button>';
                                        echo '</div>';
                                        include('display_exam_btn.php');
                                    }
                                    else{
                                        echo '<div class="w3-third w3-container w3-margin-bottom">';
                                            echo '<p><b>No Exam</b></p>';
                                        echo '</div>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="courseSummary" style="display: none;">
                <div class="w3-container w3-theme-l5">
                    <div class="w3-card-4 w3-margin w3-white w3-theme-l4" style="padding: 20px;">
                        <h1 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Dashboard</b></h1>
                        <div class="w3-row-padding w3-margin-bottom ">
                            <div class="w3-quarter ">
                                <div class="w3-container w3-purple w3-padding-16">
                                    <div class="w3-left"><span style="font-size: 35px;">&#127891;</span></div>
                                    <div class="w3-right ">
                                        <h3><?php echo isset($_SESSION['totalItem_lesson_' . $student_classroom_id]) ? $_SESSION['totalItem_lesson_' . $student_classroom_id] : 0; ?></h3>
                                    </div>
                                    <div class="w3-clear"></div>
                                    <h4>Lesson</h4>
                                </div>
                            </div>
                            <div class="w3-quarter">
                                <div class="w3-container w3-blue w3-padding-16">
                                    <div class="w3-left"><span style="font-size: 35px;">&#9997;</span></div>
                                    <div class="w3-right">
                                        <h3><?php echo isset($_SESSION['totalItem_practice_' . $student_classroom_id]) ? $_SESSION['totalItem_practice_' . $student_classroom_id] : 0; ?></h3>
                                    </div>
                                    <div class="w3-clear"></div>
                                    <h4>Practice</h4>
                                </div>
                            </div>
                            <div class="w3-quarter">
                                <div class="w3-container w3-teal w3-padding-16">
                                    <div class="w3-left"><span style="font-size: 35px;">&#128220;</span></div>
                                    <div class="w3-right">
                                        <h3><?php echo isset($_SESSION['totalItem_quizz_' . $student_classroom_id]) ? $_SESSION['totalItem_quizz_' . $student_classroom_id] : 0; ?></h3>
                                    </div>
                                    <div class="w3-clear"></div>
                                    <h4>Quizz</h4>
                                </div>
                            </div>
                            <div class="w3-quarter">
                                <div class="w3-container w3-orange w3-text-white w3-padding-16">
                                    <div class="w3-left"><span style="font-size: 35px;">&#128221;</span></div>
                                    <div class="w3-right">
                                        <h3><?php echo isset($_SESSION['totalItem_exam_' . $student_classroom_id]) ? $_SESSION['totalItem_exam_' . $student_classroom_id] : 0; ?></h3>
                                    </div>
                                    <div class="w3-clear"></div>
                                    <h4>Exam</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="w3-card-4 w3-white w3-margin w3-theme-l4" style="padding: 20px;">
                        <h1 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Performance Analysis</b></h1>
                        <div class="w3-row-padding w3-margin-bottom">
                            <h2>Quizz Average Result</h2>
                            <div class="w3-grey w3-theme-l3">
                                <div class="w3-container w3-center w3-padding w3-green" style="width: <?php echo $quizz_average; ?>%"><?php echo $quizz_average; ?>%</div>
                            </div>

                            <h2>Exam Average Result</h2>
                            <div class="w3-grey w3-theme-l3">
                                <div class="w3-container w3-center w3-padding w3-green" style="width: <?php echo $exam_average; ?>%"><?php echo $exam_average; ?>%</div>
                            </div>
                        </div>
                    </div>

                    <div class="w3-card-4 w3-white w3-margin w3-theme-l4" style="padding: 20px;">
                        <h2 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Teacher Feedback</b></h2>
                        <div id="feedback" class="w3-row-padding w3-margin-bottom">
                            <?php if ($teacher_feedback === null) : ?>
                                <h3>There is no feedback yet.</h3>
                            <?php else : ?>
                                <h3><?php echo $teacher_feedback; ?></h3>
                            <?php endif; ?>
                        </div>
                        <button class="w3-border w3-button w3-black" onclick="speakFeedback()">&#128264; Hear</button>
                    </div>
                </div>
            </div>
        </div>
        <footer class="w3-center w3-padding-16"> 
            <p>Kok Zhe Hua TP064759</p>
        </footer>
        <script>
            if (localStorage.getItem('fontSize')) {
                adjustFontSize(localStorage.getItem('fontSize'));
            }
            function adjustFontSize(size) {
                document.body.style.fontSize = size + 'px';
                localStorage.setItem('fontSize', size);
            }
        </script>
        <script>
            function speakFeedback() {
                var feedbackElement = document.getElementById('feedback');
                var feedbackContent = feedbackElement.textContent.trim();

                if (feedbackContent !== '') {
                    const speech = new SpeechSynthesisUtterance(feedbackContent);
                    speech.lang = 'en-US';
                    window.speechSynthesis.speak(speech);
                } else {
                    console.log('Feedback is empty.');
                }
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth < 992) {
                    close_side_bar();
                }else{
                    open_side_bar();
                }
            });

            function setFlag_forum() {
                const social_zone = document.getElementById('social_zone');
                const unread_forum = <?php echo $unread_forum; ?>;
                if (unread_forum == 1) {
                    social_zone.classList.add('flag_css');
                }
            }

            function setFlag_teacher() {
                const cousult_teacher = document.getElementById('cousult_teacher');
                const unread_teacher = <?php echo $unread_teacher; ?>;
                if (unread_teacher == 1) {
                    cousult_teacher.classList.add('flag_css');
                }
            }

            function showCourseResource() {
                document.getElementById('courseResource').style.display = 'block';
                document.getElementById('courseSummary').style.display = 'none';
            }

            function showCourseSummary() {
                document.getElementById('courseResource').style.display = 'none';
                document.getElementById('courseSummary').style.display = 'block';
            }

            function speak(title, description) {
                const speech = new SpeechSynthesisUtterance('Title' + title + ' ' + 'Description' + description);
                speech.lang = 'en-US';
                window.speechSynthesis.speak(speech);
            }

            setFlag_forum();
            setFlag_teacher();
        </script>
        <script src="settingProcess.js"></script>
    </body>
</html>
