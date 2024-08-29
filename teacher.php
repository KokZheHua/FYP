<?php
    session_start();
    include("connection.php");
    include("display_classroom_teacher.php");

    $id = $_SESSION['id'];
    $teacher_classroom = getClassroom();
    $teacher_classroom_hide = getClassroom_hide();

    $query = "SELECT color_theme, unread_admin FROM users WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $num_rows = mysqli_num_rows($result);
        if ($num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
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
        <title>Accessible Learning Hub</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <?php include("color_theme.php"); ?>
        <link rel="stylesheet" href="index.css">
        <script src="logoutConfirmation.js"></script>
    </head>
    <body class="w3-theme-l5">
        <div class="w3-top">
            <div class="w3-bar w3-white w3-wide w3-padding w3-card w3-theme-l4">
                <a href="teacher.php" class="w3-bar-item w3-button"><b>ALH</b> Accessible Learning Hub</a>
                <div class="w3-right">
                    <a href="consult_admin.php" class="w3-bar-item w3-button <?php echo ($unread_admin == 1 ? ' flag_css' : '') ?>">Consult Admin</a>
                    <a href="#" class="w3-bar-item w3-button" onclick="chooseColorTheme()">Color Theme</a>
                    <?php
                        if (isset($_SESSION['id'])) {
                            echo '<a href="profile.php?&previous_page=teacher" class="w3-bar-item w3-button">Profile</a>';
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

        <div class="w3-row">
            <div class="w3-col l8 s12 small-padding" style="padding-top: 60px">
                <div class="w3-card-4 w3-margin w3-white w3-theme-l4">
                    <div class="w3-container">
                        <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16">Recently Accessed Classroom</h3>
                        <div class="w3-row-padding">
                            <?php
                                if (!empty($teacher_classroom)) {
                                    foreach ($teacher_classroom as $classroom) {
                                        $query = "SELECT classroom_name, classroom_img FROM classroom_type WHERE abbreviation = '$classroom'";
                                        $result = mysqli_query($con, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            $totalItems = count($teacher_classroom);
                                            $totalPages_recently = ceil($totalItems / 3);
                                            $_SESSION['totalPages_recently'] = $totalPages_recently;
                                            $row = mysqli_fetch_assoc($result);
                                            $classroom_name = $row['classroom_name'];
                                            $classroom_img = $row['classroom_img'];
                                            echo '<div class="w3-third w3-container w3-margin-bottom recently_accessed" style="text-align: center;">';
                                            echo '<img src="' . $classroom_img . '" alt="' . $classroom_name . ' classroom" style="width:100%" class="w3-hover-opacity">';
                                                echo'<p><a href="openClassroomProcess_teacher.php?&classroom='. $classroom .'" class="w3-theme-l3 w3-button w3-padding-large w3-white w3-border classroom_btn"><b>'. $classroom_name .' »</b></a></p>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '<div class="w3-bar w3-text-theme">';
                                        echo '<button class="prev-recently w3-bar-item w3-button w3-hover-black">«</button>';
                                        for ($i = 1; $i <= $totalPages_recently; $i++) {
                                            echo '<button class="recently_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                        }
                                        echo '<button class="next-recently w3-bar-item w3-button w3-hover-black">»</button>';
                                    echo '</div>';
                                    include('recently_btn.php');
                                }
                                else{
                                    echo '<div class="w3-third w3-container w3-margin-bottom">';
                                        echo '<p><b>No recently accessed classrooms</b></p>';
                                    echo '</div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="w3-card-4 w3-margin w3-white">
                    <div class="w3-container w3-theme-l4">
                        <h3 class="w3-border-bottom w3-border-light-grey w3-padding-16">Classrooms</h3>
                        <div class="w3-section w3-bottombar w3-padding-16 ">
                            <span class="w3-margin-right">Filter:</span> 
                            <button onclick="filterBtn('show_btn'); displayList('show_list')" class="w3-button w3-card w3-black filter_btn" id="show_btn">Show Tag</button>
                            <button onclick="filterBtn('hide_btn'); displayList('hide_list')" class="w3-button w3-card w3-white filter_btn" id="hide_btn">Hide Tag</button>
                        </div>
                        <div class="w3-row-padding w3-margin">
                            <div class="w3-half">
                                <input id="searchInput" class="w3-input w3-border" type="text" placeholder="Search by classroom name">
                            </div>
                            <div class="w3-half">
                                <button class="w3-button w3-black w3-border" onclick="searchFunction()">Search</button>
                            </div>
                        </div>

                        <div class="w3-row-padding classroom" id="classroom">
                            <?php
                                if (!empty($teacher_classroom)) {
                                    foreach ($teacher_classroom as $classroom) {
                                        $query = "SELECT classroom_name, classroom_img FROM classroom_type WHERE abbreviation = '$classroom'";
                                        $result = mysqli_query($con, $query);
                                        $count = mysqli_num_rows($result);
                                        if ($count > 0) {
                                            $totalItems = count($teacher_classroom);
                                            $totalPages_classroom = ceil($totalItems / 9);
                                            $_SESSION['totalPages_classroom'] = $totalPages_classroom;
                                            $row = mysqli_fetch_assoc($result);
                                            $classroom_name = $row['classroom_name'];
                                            $classroom_img = $row['classroom_img'];
                                            echo '<div class="w3-third w3-container w3-margin-bottom display_classroom" style="text-align: center;">';
                                            echo '<img src="' . $classroom_img . '" alt="' . $classroom_name . ' classroom" style="width:100%" class="w3-hover-opacity">';
                                            echo'<div class="w3-container w3-margin" style="display: flex;">
                                                <a href="openClassroomProcess_teacher.php?&classroom='. $classroom .'" class="w3-theme-l3 w3-button w3-padding-large w3-white w3-border classroom_btn search_classroom"><b>'. $classroom_name .' »</b></a>
                                                <div class="w3-dropdown-click w3-theme-l3">
                                                    <button class="w3-button w3-padding-large" onclick="toggleMenu(\'' . $classroom . '\')">&#x2630;</button>
                                                    <div id="menu_' . $classroom . '" class="menu w3-dropdown-content w3-bar-block w3-card-4 w3-border w3-theme-l4" style="display:none;">
                                                        <a href="#" class="w3-theme-l3 w3-bar-item w3-button" onclick="toggleSidebar(event, \'' . $classroom . '\', \'' . $id . '\', \'show\')">Show</a>
                                                        <a href="#" class="w3-theme-l3 w3-bar-item w3-button" onclick="toggleSidebar(event, \'' . $classroom . '\', \'' . $id . '\', \'hide\')">Hide</a>
                                                    </div>
                                                </div>
                                            </div>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '<div class="w3-bar w3-text-theme">';
                                        echo '<button class="prev-display_classroom w3-bar-item w3-button w3-hover-black">«</button>';
                                        for ($i = 1; $i <= $totalPages_classroom; $i++) {
                                            echo '<button class="display_classroom_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                        }
                                        echo '<button class="next-display_classroom w3-bar-item w3-button w3-hover-black">»</button>';
                                    echo '</div>';
                                    include('display_classroom_btn.php');
                                }
                                else{
                                    echo '<div class="w3-third w3-container w3-margin-bottom">';
                                        echo '<p><b>No classrooms</b></p>';
                                    echo '</div>';
                                }
                            ?>
                        </div>
                        <div class="w3-row-padding classroom_hide" id="classroom_hide" style="display:none;">
                            <?php
                                if (!empty($teacher_classroom_hide)) {
                                    foreach ($teacher_classroom_hide as $classroom) {
                                        $query = "SELECT classroom_name, classroom_img FROM classroom_type WHERE abbreviation = '$classroom'";
                                        $result = mysqli_query($con, $query);
                                        $count = mysqli_num_rows($result);
                                        if ($count > 0) {
                                            $totalItems = count($teacher_classroom_hide);
                                            $totalPages_classroom_hide = ceil($totalItems / 9);
                                            $_SESSION['totalPages_classroom_hide'] = $totalPages_classroom_hide;
                                            $row = mysqli_fetch_assoc($result);
                                            $classroom_name = $row['classroom_name'];
                                            $classroom_img = $row['classroom_img'];
                                            echo '<div class="w3-third w3-container w3-margin-bottom display_classroom_hide" style="text-align: center;">';
                                            echo '<img src="' . $classroom_img . '" alt="' . $classroom_name . ' classroom" style="width:100%" class="w3-hover-opacity">';
                                            echo'<div class="w3-container w3-margin" style="display: flex;">
                                                <a href="openClassroomProcess_teacher.php?&classroom='. $classroom .'" class="w3-button w3-padding-large w3-white w3-border classroom_btn search_classroom_hide"><b>'. $classroom_name .' »</b></a>
                                                <div class="w3-dropdown-click">
                                                    <button class="w3-button w3-padding-large w3-white" onclick="toggleMenu(\'' . $classroom . '\')">&#x2630;</button>
                                                    <div id="menu_' . $classroom . '" class="menu w3-dropdown-content w3-bar-block w3-card-4 w3-border" style="display:none;">
                                                        <a href="#" class="w3-bar-item w3-button" onclick="toggleSidebar(event, \'' . $classroom . '\', \'' . $id . '\', \'show\')">Show</a>
                                                        <a href="#" class="w3-bar-item w3-button" onclick="toggleSidebar(event, \'' . $classroom . '\', \'' . $id . '\', \'hide\')">Hide</a>
                                                    </div>
                                                </div>
                                            </div>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '<div class="w3-bar">';
                                        echo '<button class="prev-display_classroom_hide w3-bar-item w3-button w3-hover-black">«</button>';
                                        for ($i = 1; $i <= $totalPages_classroom_hide; $i++) {
                                            echo '<button class="display_classroom_hide_btn w3-bar-item w3-button w3-hover-black" style="display: block;">' . $i . '</button>';
                                        }
                                        echo '<button class="next-display_classroom_hide w3-bar-item w3-button w3-hover-black">»</button>';
                                    echo '</div>';
                                    include('display_classroom_hide_btn.php');
                                }
                                else{
                                    echo '<div class="w3-third w3-container w3-margin-bottom">';
                                        echo '<p><b>No classrooms</b></p>';
                                    echo '</div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
          
            <div class="w3-col l4 " style="padding-top: 60px">
                <div class="w3-card w3-margin w3-margin-top">
                    <div class="w3-container w3-white w3-theme-l4">
                        <?php 
                            if (isset($_SESSION['id'])) {
                                $username = $_SESSION['user_name'];
                                $role = $_SESSION['role'];
                                $id = $_SESSION['id'];
                                echo '<table style="font-size: 20px;">';
                                    echo '<tr><td><b>Role</b></td><td style="padding-right:10px;">:</td><td><b>' . $role . '</b></td></tr>';
                                    echo '<tr><td><b>Username</b></td><td style="padding-right:10px;">:</td><td><b>' . $username . '</b></td></tr>';
                                    echo '<tr><td><b>Teacher ID</b></td><td style="padding-right:10px;">:</td><td><b>' . $id . '</b></td></tr>';
                                echo '</table>';
                            }
                            else{
                                echo 'Please login first!';
                            }
                        ?>
                    </div>
                </div>
                <hr>
            
                <div class="w3-card w3-margin">
                    <div class="w3-theme-l3 w3-container w3-padding" style="background-color: rgb(240, 239, 239);">
                        <h4>Recently Social Zone</h4>
                    </div>
                    <ul class="w3-ul w3-white">
                        <?php
                            if (!empty($teacher_classroom)) {
                                $query = "SELECT abbreviation, teacher_classroom_id FROM teacher_classroom WHERE teacher_id = '$id' ORDER BY access_time_forum DESC LIMIT 5";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $abbreviation = $row['abbreviation'];
                                        $query2 = "SELECT classroom_name FROM classroom_type WHERE abbreviation = '$abbreviation'";
                                        $result2 = mysqli_query($con, $query2);
                                        $row2 = mysqli_fetch_assoc($result2);
                                        $classroom_name = $row2['classroom_name'];
                                        echo '<li class="w3-padding-16  w3-theme-l4">';
                                        echo '<b><span class="w3-large ">'. $classroom_name .'</span></b><br>';
                                        $teacher_classroom_id = $row['teacher_classroom_id']; 
                                        echo '<p><a href="social_zone_teacher.php?&abbreviation='. $abbreviation .'&teacher_classroom_id=' . $teacher_classroom_id . '" class="w3-theme-l3 w3-button w3-padding-large w3-white w3-border" style="width:100%"><b>Social Zone »</b></a></p>';
                                        echo '</li>';
                                    }
                                }                                    
                            }else{
                                echo '<li class="w3-padding-16">';
                                echo '<p><b>No recently accessed forum</b></p>';
                                echo '</li>';
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    
        <footer class="w3-center w3-padding-16"> 
            <p>Kok Zhe Hua TP064759</p>
        </footer>
        <script>
            function toggleMenu(classroom) {
                const abbreviation = classroom;
                var menu = document.getElementById("menu_" + abbreviation);
                menu.style.display = (menu.style.display === "block") ? "none" : "block";
                var allMenus = document.querySelectorAll('.menu'); 
                allMenus.forEach(function(item) {
                    if (item !== menu) {
                        item.style.display = 'none';
                    }
                });
            }

            function toggleSidebar(event, classroom, id, action) {
                event.preventDefault();

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "showAndHide_teacher.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send("classroom=" + classroom + "&id=" + id + "&action=" + action);
            }

            function filterBtn(btnSelected){
                document.querySelectorAll('.filter_btn').forEach(item => {
                    if (item.classList.contains('w3-black')) {
                        item.classList.remove('w3-black');
                        item.classList.add('w3-white');
                    }
                });
                
                document.querySelectorAll('.filter_btn').forEach(item => {
                    if (item.id === btnSelected) {
                        item.classList.remove('w3-white');
                        item.classList.add('w3-black');
                    }
                });
            }

            function displayList(list_to_display){
                var classroom = document.getElementById('classroom');
                var classroom_hide = document.getElementById('classroom_hide');

                classroom.style.display = 'none';
                classroom_hide.style.display = 'none';

                if(list_to_display == 'show_list'){
                    classroom.style.display = 'block';
                }
                else if(list_to_display == 'hide_list'){   
                    classroom_hide.style.display = 'block';
                }
            }

            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    searchFunction();
                }
            });

            function searchFunction() {
                const keyword = document.getElementById('searchInput').value.toLowerCase();

                if (keyword.trim() === '') {
                    location.reload();
                    return;
                }

                const classroomItems = document.querySelectorAll('.search_classroom');
                const classroomItemsHide = document.querySelectorAll('.search_classroom_hide');
                const classroomItemsHideBtn = document.querySelectorAll('.display_classroom_hide_btn');
                const classroomItemsBtn = document.querySelectorAll('.display_classroom_btn');
                const prevClassroom = document.querySelector('.prev-display_classroom');
                const nextClassroom = document.querySelector('.next-display_classroom');
                const prevClassroomHide = document.querySelector('.prev-display_classroom_hide');
                const nextClassroomHide = document.querySelector('.next-display_classroom_hide');

                let found = false;
                let found_hide = false;

                classroomItems.forEach(item => {
                    const classroomName = item.textContent.toLowerCase();
                    const parentElement = item.parentElement.parentElement;
                    if (classroomName.includes(keyword)) {
                        parentElement.style.display = 'block';
                        found = true; 
                    } else {
                        parentElement.style.display = 'none';
                    }
                });

                classroomItemsHide.forEach(item => {
                    const classroomName = item.textContent.toLowerCase();
                    const parentElement = item.parentElement.parentElement;
                    if (classroomName.includes(keyword)) {
                        parentElement.style.display = 'block';
                        found_hide = true; 
                    } else {
                        parentElement.style.display = 'none';
                    }
                });
                const showBtn = document.getElementById('show_btn');
                const hideBtn = document.getElementById('hide_btn');

                if (!found && !found_hide) {
                    alert('No matching classroom found.');
                    location.reload();
                    return;
                }
                if (showBtn.classList.contains('w3-black')) {
                    if (!found && found_hide) {
                        alert('Classroom found in "hide".');
                        filterBtn('hide_btn'); 
                        displayList('hide_list');
                    }
                } else if (hideBtn.classList.contains('w3-black')) {
                    if (found && !found_hide) {
                        alert('Classroom found in "show".');
                        filterBtn('show_btn'); 
                        displayList('show_list');
                    }
                }

                classroomItemsHideBtn.forEach(item => {
                    const classroomName = item.textContent.toLowerCase();
                    if (classroomName.includes(keyword)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });

                classroomItemsBtn.forEach(item => {
                    const classroomName = item.textContent.toLowerCase();
                    if (classroomName.includes(keyword)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });

                prevClassroom.style.display = 'none';
                nextClassroom.style.display = 'none';
                prevClassroomHide.style.display = 'none';
                nextClassroomHide.style.display = 'none';
            }
        </script>
        <script src="settingProcess.js"></script>
    </body>
</html>