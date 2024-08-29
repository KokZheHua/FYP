<?php
    session_start();
    
    include("connection.php");

    $id = $_SESSION['id'];

    $abbreviation = $_GET['classroom'];
    
    $_SESSION['abbreviation'] = $abbreviation;

    $query = "SELECT classroom_name FROM classroom_type WHERE abbreviation = '$abbreviation'";
    
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $classroom_name = $row['classroom_name'];
    } else {
        $classroom_name = "Unknown";
    }

    $student_classroom_ids = [];
    $ids = [];
    $teacher_notifications = [];

    $query2 = "SELECT student_classroom_id, id, teacher_notifications FROM student_classroom WHERE abbreviation = '$abbreviation' AND teacher_id = '$id'";
    $result2 = mysqli_query($con, $query2);

    if ($result2) {
        while ($row2 = mysqli_fetch_assoc($result2)) {
            $student_classroom_ids[] = $row2['student_classroom_id'];
            $ids[] = $row2['id'];
            $teacher_notifications[] = $row2['teacher_notifications'];
        }
    }

    $student_name_list = [];
    foreach ($ids as $student_id) {
        $query7 = "SELECT user_name FROM users WHERE id = '$student_id'";
        $result7 = mysqli_query($con, $query7);
    
        if ($result7) {
            $row = mysqli_fetch_assoc($result7);
            $student_name_list[] = $row['user_name'];
        }else {
            echo "Error: " . mysqli_error($con);
        }
    
        mysqli_free_result($result7);
    } 

    $query3 = "SELECT teacher_classroom_id FROM teacher_classroom WHERE teacher_id = '$id' AND abbreviation = '$abbreviation'";
    $result3 = mysqli_query($con, $query3);
    if (mysqli_num_rows($result3) == 1) {
        $row3 = mysqli_fetch_assoc($result3);
        $teacher_classroom_id = $row3['teacher_classroom_id'];
    }

    $quizz_average_list = [];
    $exam_average_list = [];
    foreach ($student_classroom_ids as $student_classroom_id) {
        $quizz_mark_total = 0;
        $quizz_average = 0;
        $query6 = "SELECT quizz_result_mark FROM quizz_result_data WHERE student_classroom_id = '$student_classroom_id'";
        $result6 = mysqli_query($con, $query6);
        $i = 0;
        if (mysqli_num_rows($result6) > 0) {
            while ($row6 = mysqli_fetch_assoc($result6)) {
                if($row6['quizz_result_mark'] !== null){
                    $quizz_mark_total += $row6['quizz_result_mark'];
                    $i++;
                }
            }
        }
        $quizz_average = $i > 0 ? number_format($quizz_mark_total / $i, 2) : 0;
        if($quizz_average > 100){
            $quizz_average = 100;
        }
        $quizz_average_list[] = $quizz_average;

        $exam_mark_total = 0;
        $exam_average = 0;
        $query4 = "SELECT exam_result_mark FROM exam_result_data WHERE student_classroom_id = '$student_classroom_id'";
        $result4 = mysqli_query($con, $query4);
        $i = 0;
        if (mysqli_num_rows($result4) > 0) {
            while ($row4 = mysqli_fetch_assoc($result4)) {
                if($row4['exam_result_mark'] !== null){
                    $exam_mark_total += $row4['exam_result_mark'];
                    $i++;
                }
            }
        }
        $exam_average = $i > 0 ? number_format($exam_mark_total / $i, 2) : 0;
        if($exam_average > 100){
            $exam_average = 100;
        }
        $exam_average_list[] = $exam_average;
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

    $query_AllStudent = "SELECT id, user_name FROM users WHERE role = 'student'";
    $result_AllStudent = mysqli_query($con, $query_AllStudent);

    $id_AllList = [];
    $user_name_AllList = [];

    if (mysqli_num_rows($result_AllStudent) > 0) {
        while ($row_AllStudent = mysqli_fetch_assoc($result_AllStudent)) {
            $student_id = $row_AllStudent['id'];
            $query_check = "SELECT * FROM student_classroom WHERE abbreviation = '$abbreviation' AND id = '$student_id'";
            $result_check = mysqli_query($con, $query_check);
            if (mysqli_num_rows($result_check) == 0) {
                $id_AllList[] = $student_id;
                $user_name_AllList[] = $row_AllStudent['user_name'];
            }
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
                <a href="teacher.php" class="w3-bar-item w3-button"><b>ALH</b> Accessible Learning Hub</a>
                <div class="w3-right">
                    <a href="consult_admin.php" class="w3-bar-item w3-button <?php echo ($unread_admin == 1 ? ' flag_css' : '') ?>">Consult Admin</a>
                    <a href="#" class="w3-bar-item w3-button" onclick="chooseColorTheme()">Color Theme</a>
                    <?php
                        if (isset($_SESSION['id'])) {
                            echo '<a href="profile.php?&previous_page=classroom_teacher&classroom_abbreviation='.$abbreviation.'" class="w3-bar-item w3-button">Profile</a>';
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
            <a href="#" onclick="showCourseResource();" id="course_resources" class="side_bar_item w3-bar-item w3-button w3-padding">Course Resources</a> 
            <a href="#" onclick="openStudentListModal();" id="cousult_teacher" class="side_bar_item w3-bar-item w3-button w3-padding">Consult Student</a>
            <a href="social_zone_teacher.php?&abbreviation=<?php echo $abbreviation; ?>&teacher_classroom_id=<?php echo $teacher_classroom_id; ?>" id="social_zone" class="side_bar_item w3-bar-item w3-button w3-padding">Social Zone</a> 
            <a href="#" onclick="openStudentListModal_summary()" id="course_summary" class="side_bar_item w3-bar-item w3-button w3-padding">Course Summary</a>
        </div>
        
        <div id="studentListModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeStudentListModal()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Student List:</label></h3>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput" placeholder="Search by ID..." oninput="searchById()">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput" placeholder="Search by name..." oninput="searchByName()">
                    <select id="studentListBox" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                        <?php
                             for ($i = 0; $i < count($ids); $i++) {
                                $class = ($teacher_notifications[$i] == 1) ? 'flag_css' : '';
                                if ($class === 'flag_css') {
                                    echo "<option value='" . $ids[$i] . "' class='" . $class . "'>" . $student_name_list[$i] . "</option>";
                                }
                            }
                            for ($i = 0; $i < count($ids); $i++) {
                                $class = ($teacher_notifications[$i] == 1) ? 'flag_css' : '';
                                if ($class !== 'flag_css') {
                                    echo "<option value='" . $ids[$i] . "' class='" . $class . "'>" . $student_name_list[$i] . "</option>";
                                }
                            }
                        ?>
                    </select>
                    <button onclick="consultStudent()" class="w3-button w3-card w3-black">Consult Student</button>
                </div>
            </div>
        </div>

        <div id="studentListModal_summary" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeStudentListModal_summary();" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Student List:</label></h3>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput_summary" placeholder="Search by ID..." oninput="searchById_summary()">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput_summary" placeholder="Search by name..." oninput="searchByName_summary()">
                    <select id="studentListBox_summary" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                        <?php
                            for ($i = 0; $i < count($ids); $i++) {
                                echo "<option value='" . $ids[$i] . "'>" . $student_name_list[$i] . "</option>";
                            }
                        ?>
                    </select>
                    <button onclick="summaryStudent()" class="w3-button w3-card w3-black">Show Summary</button>
                </div>
            </div>
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

                <div class="w3-section w3-bottombar w3-padding-16">
                    <button onclick="openStudentListModal_edit()" class="w3-button w3-card w3-black" id="">Edit Student List</button>
                </div>

                <div class="w3-row-padding">
                    <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                        <div class="w3-container item_list" id="lesson_list">
                            <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Lesson</b></h3>
                            <button onclick="openNewLessonModal()" class="w3-button w3-card w3-black">Create New</button>
                            <div class="w3-row-padding">
                                <?php
                                    $unique_lesson_ids = [];
                                    foreach ($student_classroom_ids as $student_classroom_id) {
                                        $query = "SELECT DISTINCT lesson_id FROM classroom_lesson WHERE student_classroom_id = '$student_classroom_id'";
                                        $result = mysqli_query($con, $query);
                                    
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $unique_lesson_ids[] = $row['lesson_id'];
                                        }
                                    }
                                    if(!empty($unique_lesson_ids)){
                                        $unique_lesson_ids = array_unique($unique_lesson_ids);
                                        foreach ($unique_lesson_ids as $lesson_id){
                                            $query = "SELECT lesson_title, lesson_description, lesson_file FROM lesson_data WHERE lesson_id = '$lesson_id'";
                                            $result = mysqli_query($con, $query);
                                            if (mysqli_num_rows($result) > 0) {
                                                $totalItems = count($unique_lesson_ids);
                                                $totalPages_lesson = ceil($totalItems / 6);
                                                $_SESSION['totalItem_lesson_' . $abbreviation] = $totalItems;
                                                $_SESSION['totalPages_lesson'] = $totalPages_lesson;
                                                $row = mysqli_fetch_assoc($result);
                                                $lesson_title = $row['lesson_title'];
                                                $lesson_description = $row['lesson_description'];
                                                $lesson_file = $row['lesson_file'];
                                                echo'<div class="w3-third w3-container w3-margin-bottom display_lesson">';
                                                    echo'<p><b>'. $lesson_title .'</b></p>';
                                                    echo'<p>'. $lesson_description .'</p>';
                                                    echo'<a href="'. $lesson_file .'" class="w3-button w3-padding-large w3-white w3-border download_btn w3-theme-l3">Download</a>';
                                                    echo'<a href="#" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;" onclick="openEditLessonModal('. $lesson_id .');">Edit Lesson</a>';
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
                </div>

                <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                    <div class="w3-container item_list" id="practice_list">
                        <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Practice</b></h3>
                        <button onclick="openNewPracticeModal()" class="w3-button w3-card w3-black">Create New</button>
                        <div class="w3-row-padding">
                            <?php
                                $unique_practice_ids = [];
                                foreach ($student_classroom_ids as $student_classroom_id) {
                                    $query = "SELECT DISTINCT practice_id FROM classroom_practice WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $unique_practice_ids[] = $row['practice_id'];
                                    }
                                }
                                if(!empty($unique_practice_ids)){
                                    $unique_practice_ids = array_unique($unique_practice_ids);
                                    foreach ($unique_practice_ids as $practice_id){
                                        $query = "SELECT practice_title, practice_description, practice_file FROM practice_data WHERE practice_id = '$practice_id'";
                                        $result = mysqli_query($con, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            $totalItems = count($unique_practice_ids);
                                            $totalPages_practice = ceil($totalItems / 6);
                                            $_SESSION['totalItem_practice_' . $abbreviation] = $totalItems;
                                            $_SESSION['totalPages_practice'] = $totalPages_practice;
                                            $row = mysqli_fetch_assoc($result);
                                            $practice_title = $row['practice_title'];
                                            $practice_description = $row['practice_description'];
                                            $practice_file = $row['practice_file'];
                                            echo'<div class="w3-third w3-container w3-margin-bottom display_practice">';
                                                echo'<p><b>'. $practice_title .'</b></p>';
                                                echo'<p>'. $practice_description .'</p>';
                                                echo'<a href="'. $practice_file .'" class="w3-button w3-padding-large w3-white w3-border download_btn w3-theme-l3">Download</a>';
                                                echo'<a onclick="openEditPracticeModal('.$practice_id.')" class="w3-button w3-padding-large w3-white w3-border edit_btn w3-theme-l3">Edit Practice</a>';
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
                        <button onclick="openQuizzModal()" class="w3-button w3-card w3-black">Create New</button>
                        <div class="w3-row-padding">
                            <?php
                                $unique_quizz_ids = [];
                                foreach ($student_classroom_ids as $student_classroom_id) {
                                    $query = "SELECT DISTINCT quizz_id FROM classroom_quizz WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $unique_quizz_ids[] = $row['quizz_id'];
                                    }
                                }
                                $mark_quizz_student_classroom_ids = [];
                                if(!empty($unique_quizz_ids)){
                                    $unique_quizz_ids = array_unique($unique_quizz_ids);
                                    foreach ($unique_quizz_ids as $quizz_id){
                                        $query = "SELECT quizz_title, quizz_description FROM quizz_data WHERE quizz_id = '$quizz_id'";
                                        $result = mysqli_query($con, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            $totalItems = count($unique_quizz_ids);
                                            $totalPages_quizz = ceil($totalItems / 6);
                                            $_SESSION['totalItem_quizz_' . $abbreviation] = $totalItems;
                                            $_SESSION['totalPages_quizz'] = $totalPages_quizz;
                                            $row = mysqli_fetch_assoc($result);
                                            $quizz_title = $row['quizz_title'];
                                            $quizz_description = $row['quizz_description'];
                                            echo'<div class="w3-third w3-container w3-margin-bottom display_quizz">';
                                                echo'<p><b>'. $quizz_title .'</b></p>';
                                                echo'<p>'. $quizz_description .'</p>';
                                                echo'<a href="editQuizz.php?quizz_id='. $quizz_id .'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Review quizz</a>';
                                                echo'<button onclick="openQuizzModal_edit('.$quizz_id.')" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Edit quizz</button>';
                                                $query_mark_quizz = "SELECT student_classroom_id FROM classroom_quizz WHERE quizz_state = 1 AND quizz_id = '$quizz_id'";
                                                $result_mark_quizz = mysqli_query($con, $query_mark_quizz);
                                                if (mysqli_num_rows($result_mark_quizz) > 0) {
                                                    while ($row_mark_quizz = mysqli_fetch_assoc($result_mark_quizz)) {
                                                        $mark_quizz_student_classroom_ids[$quizz_id][] = $row_mark_quizz['student_classroom_id'];
                                                    }
                                                    echo'<a href="#" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;" onclick="openMarkModal(' . $quizz_id . ');">Mark quizz</a>';
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
                        <button onclick="openExamModal()" class="w3-button w3-card w3-black">Create New</button>
                        <div class="w3-row-padding">
                            <?php
                                $unique_exam_ids = [];
                                foreach ($student_classroom_ids as $student_classroom_id) {
                                    $query = "SELECT DISTINCT exam_id FROM classroom_exam WHERE student_classroom_id = '$student_classroom_id'";
                                    $result = mysqli_query($con, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $unique_exam_ids[] = $row['exam_id'];
                                    }
                                }
                                $mark_exam_student_classroom_ids = [];
                                if(!empty($unique_exam_ids)){
                                    $unique_exam_ids = array_unique($unique_exam_ids);
                                    foreach ($unique_exam_ids as $exam_id){
                                        $query = "SELECT exam_title, exam_description FROM exam_data WHERE exam_id = '$exam_id'";
                                        $result = mysqli_query($con, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            $totalItems = count($unique_exam_ids);
                                            $totalPages_exam = ceil($totalItems / 6);
                                            $_SESSION['totalItem_exam_' . $abbreviation] = $totalItems;
                                            $_SESSION['totalPages_exam'] = $totalPages_exam;
                                            $row = mysqli_fetch_assoc($result);
                                            $exam_title = $row['exam_title'];
                                            $exam_description = $row['exam_description'];
                                            echo'<div class="w3-third w3-container w3-margin-bottom display_exam">';
                                                echo'<p><b>'. $exam_title .'</b></p>';
                                                echo'<p>'. $exam_description .'</p>';
                                                echo'<a href="editExam.php?exam_id='. $exam_id .'" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Review exam</a>';
                                                echo'<button onclick="openExamModal_edit('.$exam_id.')" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;">Edit exam</button>';
                                                $query_mark_exam = "SELECT student_classroom_id FROM classroom_exam WHERE exam_state = 1 AND exam_id = '$exam_id'";
                                                $result_mark_exam = mysqli_query($con, $query_mark_exam);
                                                if (mysqli_num_rows($result_mark_exam) > 0) {
                                                    while ($row_mark_exam = mysqli_fetch_assoc($result_mark_exam)) {
                                                        $mark_exam_student_classroom_ids[$exam_id][] = $row_mark_exam['student_classroom_id'];
                                                        echo'<a href="#" class="w3-button w3-padding-large w3-white w3-border w3-theme-l3" style="width:150px;" onclick="openMarkModal_ExamMark('. $exam_id .');">Mark exam</a>';
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
            <div id="courseSummary" style="display: none;">
                <div class="w3-container w3-theme-l5">
                    <div class="w3-card-4 w3-white w3-margin w3-theme-l4" style="padding: 20px;">
                        <h1 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Performance Analysis</b></h1>
                        <div class="w3-row-padding w3-margin-bottom">
                            <h2>Quizz Average Result</h2>
                            <div class="w3-grey w3-theme-l3">
                                <div class="w3-container w3-center w3-padding w3-green" id="quizz_average" style="width: %"></div>
                            </div>
                            <input type="hidden" id="selectedStudentId" />
                            <h2>Exam Average Result</h2>
                            <div class="w3-grey w3-theme-l3">
                                <div class="w3-container w3-center w3-padding w3-green" id="exam_average" style="width: %"></div>
                            </div>
                        </div>
                    </div>

                    <div class="w3-card-4 w3-white w3-margin w3-theme-l4" style="padding: 20px;">
                        <h2 class="w3-border-bottom w3-border-light-grey w3-padding-16"><b>Teacher Feedback</b></h2>
                        <div class="w3-row-padding w3-margin-bottom">
                            <h3 id="teacher_feedback"></h3>
                        </div>
                        <textarea id="teacherFeedback" rows="4" cols="50" class="w3-input w3-border w3-margin-bottom" placeholder="Enter feedback here..."></textarea>
                        <button onclick="submitFeedback()" class="w3-button w3-card w3-black">Edit Feedback</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="markModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeMarkModal()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Student List:</label></h3>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput_QuizzMark" placeholder="Search by ID..." oninput="searchById_QuizzMark()">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput_QuizzMark" placeholder="Search by name..." oninput="searchByName_QuizzMark()">
                    <input type="hidden" id="quizzIdInput" />
                    <select id="studentListBox_QuizzMark" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                        <?php
                            if (isset($_SESSION['set_quizz_id'])) {
                                $set_quizz_id = $_SESSION['set_quizz_id'];
                            }
                            if (!empty($mark_quizz_student_classroom_ids) && !empty($mark_quizz_student_classroom_ids[$set_quizz_id])) {
                                for ($i = 0; $i < count($mark_quizz_student_classroom_ids[$set_quizz_id]); $i++) {
                                    for($j = 0; $j < count($student_classroom_ids); $j++) {
                                        if($student_classroom_ids[$j] == $mark_quizz_student_classroom_ids[$set_quizz_id][$i]){
                                            echo "<option value='" . $mark_quizz_student_classroom_ids[$set_quizz_id][$i] . "'>" . $student_name_list[$j] . "</option>";
                                        }
                                    }
                                }
                            }
                        ?>
                    </select>
                    <button onclick="markQuizzStudent()" class="w3-button w3-card w3-black">Mark Student</button>
                </div>
            </div>
        </div>

        <div id="markModal_ExamMark" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeMarkModal_ExamMark()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Student List:</label></h3>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput_ExamMark" placeholder="Search by ID..." oninput="searchById_ExamMark()">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput_ExamMark" placeholder="Search by name..." oninput="searchByName_ExamMark()">
                    <input type="hidden" id="quizzIdInput_ExamMark" />
                    <select id="studentListBox_ExamMark" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                        <?php
                            if (isset($_SESSION['set_exam_id'])) {
                                $set_exam_id = $_SESSION['set_exam_id'];
                            }
                            if (!empty($mark_exam_student_classroom_ids) && !empty($mark_exam_student_classroom_ids[$set_exam_id])) {
                                for ($i = 0; $i < count($mark_exam_student_classroom_ids[$set_exam_id]); $i++) {
                                    for($j = 0; $j < count($student_classroom_ids); $j++) {
                                        if($student_classroom_ids[$j] == $mark_exam_student_classroom_ids[$set_exam_id][$i]){
                                            echo "<option value='" . $mark_exam_student_classroom_ids[$set_exam_id][$i] . "'>" . $student_name_list[$j] . "</option>";
                                        }
                                    }
                                }
                            }
                        ?>
                    </select>
                    <button onclick="markExamStudent()" class="w3-button w3-card w3-black">Mark Student</button>
                </div>
            </div>
        </div>
        
        <div id="newLessonModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeNewLessonModal()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >New Lesson:</label></h3>
                    <form id="lessonForm" action="submitLesson.php" method="post" enctype="multipart/form-data">
                        <label for="lessonTitle">Lesson Title:</label>
                        <input type="text" id="lessonTitle" name="lessonTitle" class="w3-input w3-border w3-margin-bottom" required>

                        <label for="lessonDescription">Lesson Description:</label>
                        <textarea id="lessonDescription" name="lessonDescription" class="w3-input w3-border w3-margin-bottom" required></textarea>

                        <label for="lessonFile">Lesson File:</label>
                        <input type="file" id="lessonFile" name="lessonFile" class="w3-input w3-border w3-margin-bottom" required accept=".pdf, .doc, .docx">

                        <input type="hidden" id="studentClassroomIds" name="studentClassroomIds" value="<?php echo htmlspecialchars(implode(',', array_map('strval', $student_classroom_ids))); ?>">

                        <button type="submit" class="w3-button w3-card w3-black">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="newPracticeModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeNewPracticeModal()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >New Practice:</label></h3>
                    <form id="practiceForm" action="submitPractice.php" method="post" enctype="multipart/form-data">
                        <label for="practiceTitle">Practice Title:</label>
                        <input type="text" id="practiceTitle" name="practiceTitle" class="w3-input w3-border w3-margin-bottom" required>

                        <label for="practiceDescription">Practice Description:</label>
                        <textarea id="practiceDescription" name="practiceDescription" class="w3-input w3-border w3-margin-bottom" required></textarea>

                        <label for="practiceFile">Practice File:</label>
                        <input type="file" id="practiceFile" name="practiceFile" class="w3-input w3-border w3-margin-bottom" required accept=".pdf, .doc, .docx">

                        <input type="hidden" id="studentClassroomIds" name="studentClassroomIds" value="<?php echo htmlspecialchars(implode(',', array_map('strval', $student_classroom_ids))); ?>">

                        <button type="submit" class="w3-button w3-card w3-black">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="editLessonModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeEditLessonModal()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Edit Lesson:</label></h3>
                    <form id="lessonForm_edit" action="editLesson.php" method="post" enctype="multipart/form-data">
                        <label for="lessonTitle_edit">Lesson Title:</label>
                        <input type="text" id="lessonTitle_edit" name="lessonTitle_edit" class="w3-input w3-border w3-margin-bottom" required>

                        <label for="lessonDescription_edit">Lesson Description:</label>
                        <textarea id="lessonDescription_edit" name="lessonDescription_edit" class="w3-input w3-border w3-margin-bottom" required></textarea>

                        <label for="lessonFile">Lesson File:</label>
                        <input type="file" id="lessonFile" name="lessonFile" class="w3-input w3-border w3-margin-bottom" required accept=".pdf, .doc, .docx">

                        <input type="hidden" id="lessonIdEdit" name="lessonIdEdit" />

                        <input type="hidden" id="studentClassroomIds" name="studentClassroomIds" value="<?php echo htmlspecialchars(implode(',', array_map('strval', $student_classroom_ids))); ?>">

                        <button type="submit" class="w3-button w3-card w3-black">Submit</button>
                        <button type="button" onclick="removeLesson()" class="w3-button w3-card w3-black">Remove Lesson</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="editPracticeModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeEditPracticeModal()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Edit Practice:</label></h3>
                    <form id="practiceForm_edit" action="editPractice.php" method="post" enctype="multipart/form-data">
                        <label for="practiceTitle_edit">Practice Title:</label>
                        <input type="text" id="practiceTitle_edit" name="practiceTitle_edit" class="w3-input w3-border w3-margin-bottom" required>

                        <label for="practiceDescription_edit">Practice Description:</label>
                        <textarea id="practiceDescription_edit" name="practiceDescription_edit" class="w3-input w3-border w3-margin-bottom" required></textarea>

                        <label for="practiceFile">Practice File:</label>
                        <input type="file" id="practiceFile" name="practiceFile" class="w3-input w3-border w3-margin-bottom" required accept=".pdf, .doc, .docx">

                        <input type="hidden" id="practiceIdEdit" name="practiceIdEdit" />

                        <input type="hidden" id="studentClassroomIds" name="studentClassroomIds" value="<?php echo htmlspecialchars(implode(',', array_map('strval', $student_classroom_ids))); ?>">

                        <button type="submit" class="w3-button w3-card w3-black">Submit</button>
                        <button type="button" onclick="removePractice()" class="w3-button w3-card w3-black">Remove Practice</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="editStudentModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeStudentListModal_edit()" class="w3-button w3-display-topright">&times;</span><br>
                    <button onclick="openStudentListModal_new()" class="w3-button w3-card w3-black">Insert New Student</button>
                    <h3><label >Student List:</label></h3>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput_editStudent" placeholder="Search by ID..." oninput="searchById_editStudent()">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput_editStudent" placeholder="Search by name..." oninput="searchByName_editStudent()">
                    <input type="hidden" id="quizzIdInput_editStudent" />
                    <select id="studentEditListBox" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                        <?php
                            for ($i = 0; $i < count($ids); $i++) {
                                echo "<option value='" . $ids[$i] . "'>" . $student_name_list[$i] . "</option>";
                            }
                        ?>
                    </select>
                    <button onclick="removeStudent()" class="w3-button w3-card w3-black">Remove Student</button>
                </div>
            </div>
        </div>

        <div id="newStudentModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeStudentListModal_new()" class="w3-button w3-display-topright">&times;</span>
                    <h3><label >Student List:</label></h3>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput_newStudent" placeholder="Search by ID..." oninput="searchById_newStudent()">
                    <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput_newStudent" placeholder="Search by name..." oninput="searchByName_newStudent()">
                    <select id="studentNewListBox" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                        <?php
                            for ($i = 0; $i < count($id_AllList); $i++) {
                                echo "<option value='" . $id_AllList[$i] . "'>" . $user_name_AllList[$i] . "</option>";
                            }
                        ?>
                    </select>
                    <button onclick="newStudent()" class="w3-button w3-card w3-black">Insert Student</button>
                </div>
            </div>
        </div>

        <div id="newQuizzModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeQuizzModal()" class="w3-button w3-display-topright">&times;</span><br>
                    <form id="quizzForm">
                        <label for="quizzTitle">Quizz Title:</label>
                        <input type="text" id="quizzTitle" name="quizzTitle" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="quizzDescription">Quizz Description:</label>
                        <textarea id="quizzDescription" name="quizzDescription" class="w3-input w3-border w3-margin-bottom" required></textarea>
                        
                        <label for="timeLimit">Time Limit (min) (optional):</label>
                        <input type="number" id="timeLimit" name="timeLimit" class="w3-input w3-border w3-margin-bottom">
                        
                        <label for="startTime">Start Time :</label>
                        <input type="datetime-local" id="startTime" name="startTime" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="endTime">End Time :</label>
                        <input type="datetime-local" id="endTime" name="endTime" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <button type="button" onclick="submitQuizz()" class="w3-button w3-card w3-black">Create Quizz</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="editQuizzModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeQuizzModal_edit()" class="w3-button w3-display-topright">&times;</span><br>
                    <form id="quizzForm">
                        <input type="hidden" id="quizzId" />
                        <label for="quizzTitle_edit">Quizz Title:</label>
                        <input type="text" id="quizzTitle_edit" name="quizzTitle_edit" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="quizzDescription_edit">Quizz Description:</label>
                        <textarea id="quizzDescription_edit" name="quizzDescription_edit" class="w3-input w3-border w3-margin-bottom" required></textarea>
                        
                        <label for="timeLimit_edit">Time Limit (min) (optional):</label>
                        <input type="number" id="timeLimit_edit" name="timeLimit_edit" class="w3-input w3-border w3-margin-bottom">
                        
                        <label for="startTime_edit">Start Time :</label>
                        <input type="datetime-local" id="startTime_edit" name="startTime_edit" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="endTime_edit">End Time :</label>
                        <input type="datetime-local" id="endTime_edit" name="endTime_edit" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <button type="button" onclick="editQuizz()" class="w3-button w3-card w3-black">Edit Quizz</button>
                        <button type="button" onclick="removeQuizz()" class="w3-button w3-card w3-black">Remove Quizz</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="newExamModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeExamModal()" class="w3-button w3-display-topright">&times;</span><br>
                    <form id="examForm">
                        <label for="examTitle">Exam Title:</label>
                        <input type="text" id="examTitle" name="examTitle" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="examDescription">Exam Description:</label>
                        <textarea id="examDescription" name="examDescription" class="w3-input w3-border w3-margin-bottom" required></textarea>
                        
                        <label for="timeLimit_exam">Time Limit (min) (optional):</label>
                        <input type="number" id="timeLimit_exam" name="timeLimit_exam" class="w3-input w3-border w3-margin-bottom">
                        
                        <label for="startTime_exam">Start Time :</label>
                        <input type="datetime-local" id="startTime_exam" name="startTime_exam" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="endTime_exam">End Time :</label>
                        <input type="datetime-local" id="endTime_exam" name="endTime_exam" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <button type="button" onclick="submitExam()" class="w3-button w3-card w3-black">Create Exam</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="editExamModal" class="w3-modal">
            <div class="w3-modal-content w3-animate-zoom">
                <div class="w3-container">
                    <span onclick="closeExamModal_edit()" class="w3-button w3-display-topright">&times;</span><br>
                    <form id="examForm">
                        <input type="hidden" id="examId" />
                        <label for="examTitle_edit">Exam Title:</label>
                        <input type="text" id="examTitle_edit" name="examTitle_edit" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="examDescription_edit">Exam Description:</label>
                        <textarea id="examDescription_edit" name="examDescription_edit" class="w3-input w3-border w3-margin-bottom" required></textarea>
                        
                        <label for="timeLimit_edit_exam">Time Limit (min) (optional):</label>
                        <input type="number" id="timeLimit_edit_exam" name="timeLimit_edit_exam" class="w3-input w3-border w3-margin-bottom">
                        
                        <label for="startTime_edit_exam">Start Time :</label>
                        <input type="datetime-local" id="startTime_edit_exam" name="startTime_edit_exam" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <label for="endTime_edit_exam">End Time :</label>
                        <input type="datetime-local" id="endTime_edit_exam" name="endTime_edit_exam" class="w3-input w3-border w3-margin-bottom" required>
                        
                        <button type="button" onclick="editExam()" class="w3-button w3-card w3-black">Edit Exam</button>
                        <button type="button" onclick="removeExam()" class="w3-button w3-card w3-black">Remove Exam</button>
                    </form>
                </div>
            </div>
        </div>
        <footer class="w3-center w3-padding-16"> 
            <p>Kok Zhe Hua TP064759</p>
        </footer>
        <script>
            window.addEventListener('resize', function() {
                if (window.innerWidth < 992) {
                    close_side_bar();
                }else{
                    open_side_bar();
                }
            });

            //quizzModal - Mark
            window.addEventListener('load', function() {
                var markModalDisplayed = sessionStorage.getItem('markModalDisplayed_quizz');
                if (markModalDisplayed === 'true') {
                    document.getElementById('markModal').style.display='block';
                    sessionStorage.removeItem('markModalDisplayed_quizz');
                }
            });

            function openMarkModal(quizz_id){
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'setQuizz_id.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        sessionStorage.setItem('quizz_id', quizz_id);
                        sessionStorage.setItem('markModalDisplayed_quizz', 'true');
                        document.getElementById('quizzIdInput').value = quizz_id;
                        document.getElementById('markModal').style.display='block';
                        location.reload();
                    }
                };
                xhr.send('quizz_id=' + encodeURIComponent(quizz_id)); 
            }
            
            function closeMarkModal(){
                document.getElementById('markModal').style.display='none';
            }

            function searchByName_QuizzMark() {
                var input = document.getElementById('searchNameInput_QuizzMark').value.toUpperCase();
                var select = document.getElementById('studentListBox_QuizzMark');
                var options = select.getElementsByTagName('option');
                for (var i = 0; i < options.length; i++) {
                    var txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }

            function searchById_QuizzMark() {
                var input = document.getElementById('searchIdInput_QuizzMark').value;
                var select = document.getElementById('studentListBox_QuizzMark');
                var options = select.getElementsByTagName('option');
                var found = false; 
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === input) { 
                        options[i].selected = true; 
                        found = true; 
                        break;
                    }
                }
                if (!found) {
                    select.selectedIndex = -1;
                }
            }

            function markQuizzStudent() {
                var quizz_id = sessionStorage.getItem('quizz_id');
                var select = document.getElementById('studentListBox_QuizzMark');
                var selected_mark_quizz_student_classroom_id = select.value;
                if (selected_mark_quizz_student_classroom_id) {
                    var url = 'markQuizz.php?quizz_id='+ quizz_id +'&student_classroom_id=' + selected_mark_quizz_student_classroom_id;
                    window.location.href = url;
                } else {
                    alert('Please select a student.');
                }
            }
            
            //examModal - Mark
            window.addEventListener('load', function() {
                var markModalDisplayed = sessionStorage.getItem('markModalDisplayed_exam');
                if (markModalDisplayed === 'true') {
                    document.getElementById('markModal_ExamMark').style.display='block';
                    sessionStorage.removeItem('markModalDisplayed_exam');
                }
            });

            function openMarkModal_ExamMark(exam_id){
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'setExam_id.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        sessionStorage.setItem('exam_id', exam_id);
                        sessionStorage.setItem('markModalDisplayed_exam', 'true');
                        document.getElementById('quizzIdInput_ExamMark').value = exam_id;
                        document.getElementById('markModal_ExamMark').style.display='block';
                        location.reload();
                    }
                };
                xhr.send('exam_id=' + encodeURIComponent(exam_id));   
            }
            
            function closeMarkModal_ExamMark(){
                document.getElementById('markModal_ExamMark').style.display='none';
            }

            function searchByName_ExamMark() {
                var input = document.getElementById('searchNameInput_ExamMark').value.toUpperCase();
                var select = document.getElementById('studentListBox_ExamMark');
                var options = select.getElementsByTagName('option');
                for (var i = 0; i < options.length; i++) {
                    var txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }

            function searchById_ExamMark() {
                var input = document.getElementById('searchIdInput_ExamMark').value;
                var select = document.getElementById('studentListBox_ExamMark');
                var options = select.getElementsByTagName('option');
                var found = false; 
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === input) { 
                        options[i].selected = true; 
                        found = true; 
                        break;
                    }
                }
                if (!found) {
                    select.selectedIndex = -1;
                }
            }

            function markExamStudent() {
                var exam_id = sessionStorage.getItem('exam_id');
                var select = document.getElementById('studentListBox_ExamMark');
                var selected_mark_exam_student_classroom_id = select.value;
                if (selected_mark_exam_student_classroom_id) {
                    var url = 'markExam.php?exam_id='+ exam_id +'&student_classroom_id=' + selected_mark_exam_student_classroom_id;
                    window.location.href = url;
                } else {
                    alert('Please select a student.');
                }
            }
            //examModal - Edit

            //consultStudentModal
            function openStudentListModal(){
                document.getElementById('studentListModal').style.display='block';
            }

            function closeStudentListModal(){
                document.getElementById('studentListModal').style.display='none';
            }

            function searchByName() {
                var input = document.getElementById('searchNameInput').value.toUpperCase();
                var select = document.getElementById('studentListBox');
                var options = select.getElementsByTagName('option');
                for (var i = 0; i < options.length; i++) {
                    var txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }

            function searchById() {
                var input = document.getElementById('searchIdInput').value;
                var select = document.getElementById('studentListBox');
                var options = select.getElementsByTagName('option');
                var found = false; 
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === input) { 
                        options[i].selected = true; 
                        found = true; 
                        break;
                    }
                }
                if (!found) {
                    select.selectedIndex = -1;
                }
            }

            function consultStudent() {
                var select = document.getElementById('studentListBox');
                var selectedStudentId = select.value;
                if (selectedStudentId) {
                    var url = 'consult_student.php?abbreviation=<?php echo $abbreviation; ?>&student_id=' + selectedStudentId;
                    window.location.href = url;
                } else {
                    alert('Please select a student.');
                }
            }

            //editStudentList

            function openStudentListModal_edit(){
                document.getElementById('editStudentModal').style.display = 'block';
            }
            
            function closeStudentListModal_edit(){
                document.getElementById('editStudentModal').style.display = 'none';
            }

            function searchByName_editStudent() {
                var input = document.getElementById('searchNameInput_editStudent').value.toUpperCase();
                var select = document.getElementById('studentEditListBox');
                var options = select.getElementsByTagName('option');
                for (var i = 0; i < options.length; i++) {
                    var txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }

            function searchById_editStudent() {
                var input = document.getElementById('searchIdInput_editStudent').value;
                var select = document.getElementById('studentEditListBox');
                var options = select.getElementsByTagName('option');
                var found = false; 
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === input) { 
                        options[i].selected = true; 
                        found = true; 
                        break;
                    }
                }
                if (!found) {
                    select.selectedIndex = -1;
                }
            }

            function openStudentListModal_new() {
                document.getElementById('newStudentModal').style.display = 'block';
                document.getElementById('editStudentModal').style.display = 'none';
            }

            function closeStudentListModal_new() {
                document.getElementById('newStudentModal').style.display = 'none';
                document.getElementById('editStudentModal').style.display = 'block';
            }

            function searchByName_newStudent() {
                var input = document.getElementById('searchNameInput_newStudent').value.toUpperCase();
                var select = document.getElementById('studentNewListBox');
                var options = select.getElementsByTagName('option');
                for (var i = 0; i < options.length; i++) {
                    var txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }

            function searchById_newStudent() {
                var input = document.getElementById('searchIdInput_newStudent').value;
                var select = document.getElementById('studentNewListBox');
                var options = select.getElementsByTagName('option');
                var found = false; 
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === input) { 
                        options[i].selected = true; 
                        found = true; 
                        break;
                    }
                }
                if (!found) {
                    select.selectedIndex = -1;
                }
            }

            function newStudent() {
                var select = document.getElementById('studentNewListBox');
                var selectedStudentId = select.value;
                if (selectedStudentId) {
                    var uniqueLessonIds = <?php echo json_encode($unique_lesson_ids); ?>;
                    var uniquePracticeIds = <?php echo json_encode($unique_practice_ids); ?>;
                    var uniqueQuizzIds = <?php echo json_encode($unique_quizz_ids); ?>;
                    var uniqueExamIds = <?php echo json_encode($unique_exam_ids); ?>;
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "insertStudentProcess.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    
                    var data = "student_id=" + encodeURIComponent(selectedStudentId) + 
                            "&unique_lesson_ids=" + encodeURIComponent(JSON.stringify(uniqueLessonIds)) +
                            "&unique_practice_ids=" + encodeURIComponent(JSON.stringify(uniquePracticeIds)) +
                            "&unique_quizz_ids=" + encodeURIComponent(JSON.stringify(uniqueQuizzIds)) +
                            "&unique_exam_ids=" + encodeURIComponent(JSON.stringify(uniqueExamIds));
                    
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var response = xhr.responseText;
                            if (response === "success") {
                                alert("Insert successful");
                                location.reload();
                            } else {
                                alert(response);
                            }
                        }
                    };
                    xhr.send(data);
                } else {
                    alert('Please select a student.');
                }
            }

            function removeStudent() {
                var select = document.getElementById('studentEditListBox');
                var selectedStudentId = select.value;
                if (selectedStudentId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "removeStudentProcess.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var response = xhr.responseText;
                            if (response === "success") {
                                alert("Remove successful");
                                location.reload();
                            }else {
                                alert("Remove failed: " + response);
                            }
                        }
                    };
                    xhr.send("student_id=" + encodeURIComponent(selectedStudentId));
                } else {
                    alert('Please select a student.');
                }
            }

            //courseSummaryModal
            function showCourseResource() {
                document.getElementById('courseResource').style.display = 'block';
                document.getElementById('courseSummary').style.display = 'none';
            }

            function showCourseSummary(selectedStudentId) {
                var xhr = new XMLHttpRequest();
                var url = 'getStudentData.php';
                url += '?student_id=' + selectedStudentId;
                url += '&quizz_average_list=' + encodeURIComponent(JSON.stringify(<?php echo json_encode($quizz_average_list); ?>));
                url += '&exam_average_list=' + encodeURIComponent(JSON.stringify(<?php echo json_encode($exam_average_list); ?>));
                url += '&ids=' + encodeURIComponent(JSON.stringify(<?php echo json_encode($ids); ?>));
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText);
                        var studentData = JSON.parse(xhr.responseText);
                        document.getElementById('selectedStudentId').value = selectedStudentId;
                        document.getElementById('quizz_average').innerText = studentData.quizz_average + "%";
                        document.getElementById('exam_average').innerText = studentData.exam_average + "%";
                        document.getElementById('quizz_average').style.width = studentData.quizz_average + '%';
                        document.getElementById('exam_average').style.width = studentData.exam_average + '%';
                        if(studentData.teacher_feedback !== null){
                            document.getElementById('teacher_feedback').innerText = studentData.teacher_feedback;
                        }else{
                            document.getElementById('teacher_feedback').innerText = 'There is no feedback yet.';
                        }
                        document.getElementById('courseResource').style.display = 'none';
                        closeStudentListModal_summary();
                        document.getElementById('courseSummary').style.display = 'block';
                    }
                };
                
                xhr.open('GET', url, true);
                xhr.send();
            }

            function openStudentListModal_summary(){
                document.getElementById('studentListModal_summary').style.display='block';
            }

            function closeStudentListModal_summary(){
                document.getElementById('studentListModal_summary').style.display='none';
            }

            function searchByName_summary() {
                var input = document.getElementById('searchNameInput_summary').value.toUpperCase();
                var select = document.getElementById('studentListBox_summary');
                var options = select.getElementsByTagName('option');
                for (var i = 0; i < options.length; i++) {
                    var txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        options[i].style.display = "";
                    } else {
                        options[i].style.display = "none";
                    }
                }
            }

            function searchById_summary() {
                var input = document.getElementById('searchIdInput_summary').value;
                var select = document.getElementById('studentListBox_summary');
                var options = select.getElementsByTagName('option');
                var found = false; 
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === input) { 
                        options[i].selected = true; 
                        found = true; 
                        break;
                    }
                }
                if (!found) {
                    select.selectedIndex = -1;
                }
            }

            function summaryStudent() {
                var select = document.getElementById('studentListBox_summary');
                var selectedStudentId = select.value;
                if (selectedStudentId) {
                    showCourseSummary(selectedStudentId);
                } else {
                    alert('Please select a student.');
                }
            }

            function submitFeedback() {
                const feedback = document.getElementById("teacherFeedback").value;
                var selectedStudentId_feedback = document.getElementById('selectedStudentId').value;
                if (feedback.trim() === "") {
                    return;
                }

                var dataToSend = {
                    feedback: feedback,
                    selectedStudentId: selectedStudentId_feedback
                };

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "feedbackProcess.php", true);
                xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert("Feedback updated");
                            location.reload();
                        } 
                    }
                };

                xhr.send(JSON.stringify(dataToSend));
            }
            
            // createNewLesson Modal
            function openNewLessonModal(){
                document.getElementById('newLessonModal').style.display='block';
            }

            function closeNewLessonModal(){
                document.getElementById('newLessonModal').style.display='none';
            }

            // createNewPractice Modal
            function openNewPracticeModal(){
                document.getElementById('newPracticeModal').style.display='block';
            }

            function closeNewPracticeModal(){
                document.getElementById('newPracticeModal').style.display='none';
            }

            // editLesson Modal
            function openEditLessonModal(lesson_id) {
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var lessonData = JSON.parse(xhr.responseText);
                        document.getElementById('lessonTitle_edit').value = lessonData.lesson_title;
                        document.getElementById('lessonDescription_edit').value = lessonData.lesson_description;
                        document.getElementById('lessonIdEdit').value = lesson_id;
                        document.getElementById('editLessonModal').style.display='block';
                    }
                };
                
                xhr.open('GET', 'getEditLesson.php?lesson_id=' + lesson_id, true);
                xhr.send();
            }

            function closeEditLessonModal(){
                document.getElementById('editLessonModal').style.display='none';
            }
            // editPractice Modal
            function openEditPracticeModal(practice_id){
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var practiceData = JSON.parse(xhr.responseText);
                        document.getElementById('practiceTitle_edit').value = practiceData.practice_title;
                        document.getElementById('practiceDescription_edit').value = practiceData.practice_description;
                        document.getElementById('practiceIdEdit').value = practice_id;
                        document.getElementById('editPracticeModal').style.display='block';
                    }
                };
                
                xhr.open('GET', 'getEditPractice.php?practice_id=' + practice_id, true);
                xhr.send();
            }

            function closeEditPracticeModal(){
                document.getElementById('editPracticeModal').style.display='none';
            }

            // createQuizzModal
            function openQuizzModal(){
                document.getElementById('newQuizzModal').style.display='block';
            }
            function closeQuizzModal(){
                document.getElementById('newQuizzModal').style.display='none';
            }

            function submitQuizz() {
                var quizzTitle = document.getElementById('quizzTitle').value.trim();
                var quizzDescription = document.getElementById('quizzDescription').value.trim();
                var timeLimit = document.getElementById('timeLimit').value.trim();
                var startTime = document.getElementById('startTime').value.trim();
                var endTime = document.getElementById('endTime').value.trim();

                if (quizzTitle === "" || quizzDescription === "" || startTime === "" || endTime === "") {
                    alert("Please fill in all the required information.");
                    return;
                }
                if (timeLimit === '') {
                    timeLimit = null;
                }
                if (startTime === '') {
                    startTime = null;
                }
                if (endTime === '') {
                    endTime = null;
                }

                var formData = new FormData();
                formData.append('quizzTitle', quizzTitle);
                formData.append('quizzDescription', quizzDescription);
                formData.append('timeLimit', timeLimit);
                formData.append('startTime', startTime);
                formData.append('endTime', endTime);

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        window.location.href = "createQuestion.php";
                    }
                };
                xhr.open('POST', 'createQuizzProcess.php', true);
                xhr.send(formData);
            }

            function removeLesson(){
                var lesson_id = document.getElementById('lessonIdEdit').value.trim();
                if (confirm("Are you sure you want to remove the lesson?")) {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert("Remove lesson successfully.");
                            location.reload();
                        }
                    };

                    xhr.open('POST', 'removeLessonProcess.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('lesson_id=' + encodeURIComponent(lesson_id));
                }
            }

            function removePractice(){
                var practice_id = document.getElementById('practiceIdEdit').value.trim();
                if (confirm("Are you sure you want to remove the practice?")) {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert("Remove practice successfully.");
                            location.reload();
                        }
                    };

                    xhr.open('POST', 'removePracticeProcess.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('practice_id=' + encodeURIComponent(practice_id));
                }
            }

            //Edit quizzmodal
            function openQuizzModal_edit(quizz_id){
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var quizzData = JSON.parse(xhr.responseText);
                        document.getElementById('quizzTitle_edit').value = quizzData.quizz_title;
                        document.getElementById('quizzDescription_edit').value = quizzData.quizz_description;
                        document.getElementById('timeLimit_edit').value = quizzData.time_limit_min;
                        document.getElementById('startTime_edit').value = quizzData.start_time;
                        document.getElementById('endTime_edit').value = quizzData.end_time;

                        document.getElementById('editQuizzModal').style.display='block';
                        document.getElementById("quizzId").value = quizz_id;
                    }
                };
                
                xhr.open('GET', 'getEditQuizz.php?quizz_id=' + quizz_id, true);
                xhr.send();
            }
            
            function closeQuizzModal_edit(){
                document.getElementById('editQuizzModal').style.display='none';
            }

            function removeQuizz(){
                var quizz_id = document.getElementById('quizzId').value.trim();
                if (confirm("Are you sure you want to remove the quizz?")) {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert("Remove quizz successfully.");
                            location.reload();
                        }
                    };

                    xhr.open('POST', 'removeQuizzProcess.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('quizz_id=' + encodeURIComponent(quizz_id));
                }
            }

            function editQuizz() {
                var quizzTitle = document.getElementById('quizzTitle_edit').value.trim();
                var quizzDescription = document.getElementById('quizzDescription_edit').value.trim();
                var timeLimit = document.getElementById('timeLimit_edit').value.trim();
                var startTime = document.getElementById('startTime_edit').value.trim();
                var endTime = document.getElementById('endTime_edit').value.trim();
                var quizz_id = document.getElementById('quizzId').value.trim();

                if (quizzTitle === "" || quizzDescription === "" || startTime === "" || endTime === "") {
                    alert("Please fill in all the required information.");
                    return;
                }

                if (timeLimit === '') {
                    timeLimit = null;
                }
                if (startTime === '') {
                    startTime = null;
                }
                if (endTime === '') {
                    endTime = null;
                }

                var formData = new FormData();
                formData.append('quizzTitle_edit', quizzTitle);
                formData.append('quizzDescription_edit', quizzDescription);
                formData.append('timeLimit_edit', timeLimit);
                formData.append('startTime_edit', startTime);
                formData.append('endTime_edit', endTime);
                formData.append('quizz_id_edit', quizz_id);

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Updated successfully!");
                        location.reload();
                    }
                };
                xhr.open('POST', 'editQuizzProcess.php', true);
                xhr.send(formData);
            }


            //exam modal
            function openExamModal(){
                document.getElementById('newExamModal').style.display='block';
            }
            function closeExamModal(){
                document.getElementById('newExamModal').style.display='none';
            }

            function submitExam() {
                var examTitle = document.getElementById('examTitle').value.trim();
                var examDescription = document.getElementById('examDescription').value.trim();
                var timeLimit = document.getElementById('timeLimit_exam').value.trim();
                var startTime = document.getElementById('startTime_exam').value.trim();
                var endTime = document.getElementById('endTime_exam').value.trim();

                if (quizzTitle === "" || quizzDescription === "" || startTime === "" || endTime === "") {
                    alert("Please fill in all the required information.");
                    return;
                }
                if (timeLimit === '') {
                    timeLimit = null;
                }
                if (startTime === '') {
                    startTime = null;
                }
                if (endTime === '') {
                    endTime = null;
                }

                var formData = new FormData();
                formData.append('examTitle', examTitle);
                formData.append('examDescription', examDescription);
                formData.append('timeLimit', timeLimit);
                formData.append('startTime', startTime);
                formData.append('endTime', endTime);

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        window.location.href = "createQuestion_exam.php";
                    }
                };
                xhr.open('POST', 'createExamProcess.php', true);
                xhr.send(formData);
            }

            //Edit exammodal
            function openExamModal_edit(exam_id){
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var examData = JSON.parse(xhr.responseText);
                        document.getElementById('examTitle_edit').value = examData.exam_title;
                        document.getElementById('examDescription_edit').value = examData.exam_description;
                        document.getElementById('timeLimit_edit_exam').value = examData.time_limit_min;
                        document.getElementById('startTime_edit_exam').value = examData.start_time;
                        document.getElementById('endTime_edit_exam').value = examData.end_time;

                        document.getElementById('editExamModal').style.display='block';
                        document.getElementById("examId").value = exam_id;
                    }
                };
                
                xhr.open('GET', 'getEditExam.php?exam_id=' + exam_id, true);
                xhr.send();
            }
            
            function closeExamModal_edit(){
                document.getElementById('editExamModal').style.display='none';
            }

            function removeExam(){
                var exam_id = document.getElementById('examId').value.trim();
                if (confirm("Are you sure you want to remove the exam?")) {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert("Remove exam successfully.");
                            location.reload();
                        }
                    };

                    xhr.open('POST', 'removeExamProcess.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('exam_id=' + encodeURIComponent(exam_id));
                }
            }

            function editExam() {
                var examTitle = document.getElementById('examTitle_edit').value.trim();
                var examDescription = document.getElementById('examDescription_edit').value.trim();
                var timeLimit = document.getElementById('timeLimit_edit_exam').value.trim();
                var startTime = document.getElementById('startTime_edit_exam').value.trim();
                var endTime = document.getElementById('endTime_edit_exam').value.trim();
                var exam_id = document.getElementById('examId').value.trim();

                if (quizzTitle === "" || quizzDescription === "" || startTime === "" || endTime === "") {
                    alert("Please fill in all the required information.");
                    return;
                }

                if (timeLimit === '') {
                    timeLimit = null;
                }
                if (startTime === '') {
                    startTime = null;
                }
                if (endTime === '') {
                    endTime = null;
                }
                console.log(startTime);
                console.log(endTime);

                var formData = new FormData();
                formData.append('examTitle_edit', examTitle);
                formData.append('examDescription_edit', examDescription);
                formData.append('timeLimit_edit_exam', timeLimit);
                formData.append('startTime_edit_exam', startTime);
                formData.append('endTime_edit_exam', endTime);
                formData.append('exam_id_edit', exam_id);

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Updated successfully!");
                        location.reload();
                    }
                };
                xhr.open('POST', 'editExamProcess.php', true);
                xhr.send(formData);
            }
        </script>
        <script src="settingProcess.js"></script>
    </body>
</html>
