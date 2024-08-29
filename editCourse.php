<!DOCTYPE html>
<html>
    <head>
        <title>Edit Course</title>
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
            <h2>Edit Course</h2>
            <form action="checkCourse_input.php" method="get">
                <input type="text" id="searchInput" class="w3-input w3-border" oninput="searchCourses()" placeholder="Search for course...">
                <select name="abbreviation" class="w3-select" size="20" style="font-size: 18px;">
                    <?php
                    include("connection.php");

                    $query = "SELECT classroom_name, abbreviation FROM classroom_type";
                    $result = mysqli_query($con, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['abbreviation'] . "'>" . $row['classroom_name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No courses found</option>";
                    }

                    mysqli_close($con);
                    ?>
                </select>
            </form>
            <button onclick="validateAndSubmit()" class="w3-button w3-black">Edit Course</button>
            <button onclick="goToAdminPage()" class="w3-button w3-black">Back</button>
        </div>
        <script>
            function searchCourses() {
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

            function goToAdminPage() {
                window.location.href = "admin.php";
            }
            function validateAndSubmit() {
                var selectedCourse = document.querySelector('select[name="abbreviation"]').value;
        
                if (selectedCourse === '') {
                    alert('Please select a course to edit.');
                    return false;
                } else {
                    document.forms[0].submit();
                }

            }
        </script>
    </body>
</html>