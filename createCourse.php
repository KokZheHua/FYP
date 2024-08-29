<!DOCTYPE html>
<html>
    <head>
        <title>Create Course</title>
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
            <h1><b>Create Course</b></h1>
            <form id="createCourseForm" action="createCourseProcess.php" method="post" enctype="multipart/form-data" class="w3-container">
                <label class="w3-text-black"><b>Course Name:</b></label>
                <input class="w3-input w3-border" type="text" name="classroom_name" required><br>

                <label class="w3-text-black"><b>Abbreviation:</b></label>
                <input class="w3-input w3-border" type="text" name="abbreviation" required><br>

                <label class="w3-text-black"><b>Image:</b></label>
                <input class="w3-input w3-border" type="file" name="image" accept="image/jpeg, image/png" required><br>
            </form>
            <button onclick="validateAndSubmit()" class="w3-button w3-black">Create Course</button>
            <button onclick="goToAdminPage()" class="w3-button w3-black">Back</button>
        </div>
        <script>
            function goToAdminPage() {
                window.location.href = "admin.php";
            }
            function validateAndSubmit() {
                var form = document.getElementById("createCourseForm");
                if (form.checkValidity()) {
                    form.submit();
                } else {
                    alert("Please fill in all required fields.");
                }
            }

        </script>
    </body>
</html>