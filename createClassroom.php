<?php
    session_start();
    include("connection.php");

    if ($con->connect_error) {
        die("Error: " . $con->connect_error);
    }
    
    $ids = [];
    $teacher_name_list = [];

    $query = "SELECT * FROM users WHERE role = 'teacher'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ids[] = $row['id'];
            $teacher_name_list[] = $row['name'];
        }
    }

    $classroom_name_list = [];
    $abbreviation_list = [];

    $query2 = "SELECT classroom_name, abbreviation FROM classroom_type";
    $result2 = mysqli_query($con, $query2);
    if (mysqli_num_rows($result2) > 0) {
        while ($row2 = mysqli_fetch_assoc($result2)) {
            $classroom_name_list[] = $row2['classroom_name'];
            $abbreviation_list[] = $row2['abbreviation'];
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Create Classroom</title>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <style>
            .w3-button{
                border-radius: 5px;
                transition: background-color 0.3s;
            }
        </style>
    </head>
    <body>
        <div class="w3-container">
            <h3><label >Teacher List:</label></h3>
            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput" placeholder="Search by ID..." oninput="searchById()">
            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput" placeholder="Search by name..." oninput="searchByName()">
            <select id="teacherListBox" class="w3-select w3-border w3-margin-bottom" size="10" style="font-size: 20px;">
                <?php
                    for ($i = 0; $i < count($ids); $i++) {
                        echo "<option value='" . $ids[$i] . "'>" . $teacher_name_list[$i] . "</option>";
                    }
                ?>
            </select>
            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchClassroomInput" placeholder="Search by classroom name..." oninput="searchClassroom()">
            <select id="classroomListBox" class="w3-select w3-border w3-margin-bottom" size="5" style="font-size: 20px;">
                <?php
                    for ($i = 0; $i < count($abbreviation_list); $i++) {
                        echo "<option value='" . $abbreviation_list[$i] . "'>" . $classroom_name_list[$i] . "</option>";
                    }
                ?>
            </select>
            <button onclick="createClassroom()" class="w3-button w3-card w3-black">Create Classroom</button>
            <button onclick="goToAdminPage()" class="w3-button w3-card w3-black">Back</button>
        </div>
        <script>
            function goToAdminPage() {
                window.location.href = 'admin.php';
            }

            function createClassroom() {
                var select = document.getElementById('teacherListBox');
                var select2 = document.getElementById('classroomListBox');
                var selectedTeacherId = select.value;
                var selectedClassroom = select2.value;
                if (selectedTeacherId) {
                    if (selectedClassroom){
                        var url = 'createClassroomProcess.php?teacher_id='+ selectedTeacherId +'&classroom=' + selectedClassroom;
                        window.location.href = url;
                    }else{
                        alert('Please select a course.');    
                    }
                } else {
                    alert('Please select a teacher.');
                }
            }

            function searchClassroom(){
                var input = document.getElementById('searchClassroomInput').value.toUpperCase();
                var select = document.getElementById('classroomListBox');
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

            function searchByName() {
                var input = document.getElementById('searchNameInput').value.toUpperCase();
                var select = document.getElementById('teacherListBox');
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
                var select = document.getElementById('teacherListBox');
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
        </script>
    </body>
</html>