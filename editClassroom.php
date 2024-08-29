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
            <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchInput" oninput="searchClassrooms()" placeholder="Search by abbreviation or teacher ID">
            <form action="checkClassroomProcess.php" method="post">
                <select name="classroom_id" class="w3-select" size="10" style="font-size: 18px;">
                    <?php
                    include("connection.php");

                    $query = "SELECT abbreviation, teacher_id FROM teacher_classroom";
                    $result = mysqli_query($con, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['abbreviation'] . "' data-teacher-id='" . $row['teacher_id'] . "'> (Teacher ID: " . $row['teacher_id'] . ", Abbreviation: " . $row['abbreviation'] . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No classrooms found</option>";
                    }

                    mysqli_close($con);
                    ?>
                </select>
            </form>
            <button onclick="validateAndSubmit()" class="w3-button w3-black">Edit Classroom</button>
            <button onclick="goToAdminPage()" class="w3-button w3-black">Back</button>
        </div>
        <script>
            function searchClassrooms() {
                var keyword = document.getElementById('searchInput').value.toLowerCase();
                var options = document.querySelectorAll('select[name="classroom_id"] option');
                
                options.forEach(function(option) {
                    var text = option.textContent.toLowerCase();
                    if (text.includes(keyword)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
            }

            function goToAdminPage() {
                window.location.href = "admin.php";
            }
            function validateAndSubmit() {
                var selectedClassroom = document.querySelector('select[name="classroom_id"]').value;

                if (selectedClassroom === '') {
                    alert('Please select a classroom to edit.');
                    return false;
                } else {
                    var selectedTeacherId = document.querySelector('select[name="classroom_id"] option:checked').getAttribute('data-teacher-id');
                    window.location.href = "checkClassroomProcess.php?abbreviation=" + selectedClassroom + "&teacher_id=" + selectedTeacherId;
                }

            }
        </script>
    </body>
</html>