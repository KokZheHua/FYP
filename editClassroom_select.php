<!DOCTYPE html>
<html>
<head>
    <title>Edit Classroom</title>
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
        <h2>Edit Classroom</h2>
        <form action="checkClassroomProcess.php" method="post">
            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchInputTeacher" oninput="searchTeacherByName()" placeholder="Search by teacher name">
            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchInputTeacherID" oninput="searchTeacherByID()" placeholder="Search by teacher ID">
            <select name="teacher_id" class="w3-select" size="10" style="font-size: 18px;">
                <?php
                session_start();

                include("connection.php");

                $teacher_id = $_GET['teacher_id'];      
                $_SESSION['current_teacher_id'] = $teacher_id;

                $query = "SELECT * FROM users WHERE role='teacher'";
                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = ($row['id'] == $teacher_id) ? 'selected' : '';
                        echo "<option value='" . $row['id'] . "' $selected> (Teacher ID: " . $row['id'] . ", Teacher Name: " . $row['user_name'] . ")</option>";
                    }
                } else {
                    echo "<option value=''>No teacher found</option>";
                }

                mysqli_close($con);
                ?>
            </select>

            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchInput" oninput="searchClassrooms()" placeholder="Search by abbreviation">
            
            <select name="abbreviation" class="w3-select" size="10" style="font-size: 18px;">
                <?php
                include("connection.php");

                $abbreviation = $_GET['abbreviation'];
                $_SESSION['current_abbreviation'] = $abbreviation;

                $query = "SELECT * FROM classroom_type";
                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = ($row['abbreviation'] == $abbreviation) ? 'selected' : '';
                        echo "<option value='" . $row['abbreviation'] . "' $selected> (Abbreviation: " . $row['abbreviation'] . ")</option>";
                    }
                } else {
                    echo "<option value=''>No courses found</option>";
                }

                mysqli_close($con);
                ?>
            </select>
        </form>
        <button onclick="goEditClassroom()" class="w3-button w3-black">Edit Classroom</button>
        <button onclick="goBack()" class="w3-button w3-black">Back</button>
    </div>
    <script>
        function searchTeacherByName() {
            var keyword = document.getElementById('searchInputTeacher').value.toLowerCase();
            var options = document.querySelectorAll('select[name="teacher_id"] option');
            
            options.forEach(function(option) {
                var text = option.textContent.toLowerCase();
                if (text.includes(keyword)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function searchTeacherByID() {
            var keyword = document.getElementById('searchInputTeacherID').value.toLowerCase();
            var options = document.querySelectorAll('select[name="teacher_id"] option');
            
            options.forEach(function(option) {
                var id = option.value.toLowerCase();
                if (id.includes(keyword)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function searchClassrooms() {
            var keyword = document.getElementById('searchInput').value.toLowerCase();
            var options = document.querySelectorAll('select[name="abbreviation"] option');
            
            options.forEach(function(option) {
                var text = option.textContent.toLowerCase();
                if (text.includes(keyword)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        function goEditClassroom() {
            var selectedTeacherId = document.querySelector('select[name="teacher_id"]').value;
            var selectedAbbreviation = document.querySelector('select[name="abbreviation"]').value;

            var url = "editClassroomProcess.php?teacher_id=" + selectedTeacherId + "&abbreviation=" + selectedAbbreviation;

            window.location.href = url;
        }

        function goBack() {
            window.location.href = "editClassroom.php";
        }
    </script>
</body>
</html>