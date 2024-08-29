<?php
    session_start();
    include("connection.php");

    if (!$con) {
        die(mysqli_connect_error());
    }
    
    $abbreviation = $_GET['abbreviation'];

    $_SESSION['current_abbreviation'] = $abbreviation;

    $query = "SELECT * FROM classroom_type WHERE abbreviation = '$abbreviation'";
    $result = mysqli_query($con, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $classroom_name = $row['classroom_name'];
    }
?>

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
            <h1><b>Edit Course</b></h1>
            <form id="editCourseForm" action="editCourseProcess.php" method="post" enctype="multipart/form-data" class="w3-container">
                <label class="w3-text-black"><b>Course Name:</b></label>
                <input class="w3-input w3-border" type="text" name="classroom_name" value="<?php echo $classroom_name ?>" required><br>

                <label class="w3-text-black"><b>Abbreviation:</b></label>
                <input class="w3-input w3-border" type="text" name="abbreviation" value="<?php echo $_GET['abbreviation']; ?>" required><br>

                <label class="w3-text-black"><b>Image:</b></label>
                <input class="w3-input w3-border" type="file" name="image" accept="image/jpeg, image/png" required><br>
            </form>
            <button onclick="validateAndSubmit()" class="w3-button w3-black">Edit Course</button>
            <button onclick="goBackPage()" class="w3-button w3-black">Back</button>
        </div>
        <script>
            function goBackPage() {
                window.location.href = "editCourse.php";
            }
            function validateAndSubmit() {
                var form = document.getElementById("editCourseForm");
                if (form.checkValidity()) {
                    form.submit();
                } else {
                    alert("Please fill in all required fields.");
                }
            }

        </script>
    </body>
</html>