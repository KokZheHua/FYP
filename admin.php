<?php
    session_start();
    include("connection.php");
    
    if ($con->connect_error) {
        die("Error: " . $con->connect_error);
    }

    $id_list = [];
    $user_name_list = [];
    $name_list = [];
    $email_list = [];
    $role_list = [];
    $phone_list = [];
    $age_list = [];
    $DOB_list = [];
    $gender_list = [];
    $admin_notifications_list = [];

    $query = "SELECT * FROM users ORDER BY admin_notifications DESC, id ASC";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id_list[] = $row['id'];
            $user_name_list[] = $row['user_name'];
            $name_list[] = $row['name'];
            $email_list[] = $row['email'];
            $role_list[] = $row['role'];
            $phone_list[] = $row['phone'];
            $age_list[] = $row['age'];
            $DOB_list[] = $row['DOB'];
            $gender_list[] = $row['gender'];
            $admin_notifications_list[] = $row['admin_notifications'];
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Admin Page</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="index.css">
        <script src="logoutConfirmation.js"></script>
        <style>
            #userDataBodyContainer  {
                max-height: 70vh; 
                overflow-y: auto; 
            }
        </style>
    </head>
    <body>
        <div class="w3-container w3-margin">
            <h2><b>Admin Dashboard</b></h2>
            <div class="w3-right">
                <a href="#" onclick="confirmLogout()" class="w3-margin w3-button w3-black">Logout</a>
            </div>
            <div class="w3-card-4 w3-padding">
                <h3><b>Manage Personal Information</b></h3>
                <p>
                    <button onclick="goToRegisterPage()" class="w3-button w3-black">Register Teacher/Admin</button>
                    <button onclick="createClassroom()" class="w3-button w3-black">Create New Classroom</button>
                    <button onclick="createCourse()" class="w3-button w3-black">Create New Course</button>
                    <button onclick="editClassroom()" class="w3-button w3-black">Edit Classroom</button>
                    <button onclick="editCourse()" class="w3-button w3-black">Edit Course</button>
                </p>
                <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchIdInput" placeholder="Search by ID..." oninput="searchById()">
                <input class="w3-input w3-border w3-margin-bottom" type="text" id="searchNameInput" placeholder="Search by name..." oninput="searchByName()">
                <div id="userDataBodyContainer">
                    <table id="userTable" class="w3-table w3-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Age</th>
                                <th>DOB</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody id="userDataBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <footer class="w3-center w3-padding-16"> 
            <p>Kok Zhe Hua TP064759</p>
        </footer>
        <script>
            var id_list = <?php echo json_encode($id_list); ?>;
            var user_name_list = <?php echo json_encode($user_name_list); ?>;
            var name_list = <?php echo json_encode($name_list); ?>;
            var email_list = <?php echo json_encode($email_list); ?>;
            var role_list = <?php echo json_encode($role_list); ?>;
            var phone_list = <?php echo json_encode($phone_list); ?>;
            var age_list = <?php echo json_encode($age_list); ?>;
            var DOB_list = <?php echo json_encode($DOB_list); ?>;
            var gender_list = <?php echo json_encode($gender_list); ?>;
            var admin_notifications_list = <?php echo json_encode($admin_notifications_list); ?>;

            displayUserData();

            function searchById() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchIdInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("userDataBody");
                tr = table.getElementsByTagName("tr");
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[0];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }

            function searchByName() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchNameInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("userDataBody");
                tr = table.getElementsByTagName("tr");
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[1];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }

            function displayUserData() {
                var tableBody = document.getElementById('userDataBody');
                tableBody.innerHTML = '';

                for (var i = 0; i < id_list.length; i++) {
                    var row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${id_list[i]}</td>
                        <td>${user_name_list[i]}</td>
                        <td>${name_list[i]}</td>
                        <td>${email_list[i]}</td>
                        <td>${role_list[i]}</td>
                        <td>${phone_list[i]}</td>
                        <td>${age_list[i]}</td>
                        <td>${DOB_list[i]}</td>
                        <td>${gender_list[i]}</td>
                        <td>${admin_notifications_list[i] == 1 ? '<button onclick="consultAdmin(' + id_list[i] + ')" class="w3-button w3-black flag_css">Consult</button>' : '<button onclick="consultAdmin(' + id_list[i] + ')" class="w3-button w3-black">Consult</button>'}</td>
                        <td><button onclick="editUser(${id_list[i]})" class="w3-button w3-black">Edit</button></td>
                    `;
                    tableBody.appendChild(row);
                }
            }

            function goToRegisterPage() {
                window.location.href = 'register_page.php';
            }

            function createCourse(){
                window.location.href = 'createCourse.php';
            }

            function createClassroom() {
                window.location.href = 'createClassroom.php';
            }

            function editClassroom() {
                window.location.href = 'editClassroom.php';
            }
            
            function editCourse() {
                window.location.href = 'editCourse.php';
            }

            function consultAdmin(id) {
                window.location.href = 'admin_consult_page.php?student_id=' + id;
            }

            function editUser(id) {
                window.location.href = 'edit_user_page.php?id=' + id;
            }

        </script>

    </body>
</html>