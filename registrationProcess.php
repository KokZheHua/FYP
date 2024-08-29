<?php 
    include "connection.php";

    if (!$con) {
        die(mysqli_connect_error());
    }

    $name = $_POST['name'];
    $uname = $_POST['uname'];
    $age = date("Y") - date("Y", strtotime($_POST['DOB']));
    $pass = $_POST['pass'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    if(isset($_POST['role'])) {
        $role = $_POST['role'];
    }
    $phone = null;
    $DOB = null;
    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

    if($_POST['phone'] != null)
    {
        $phone = $_POST['phone'];
    }
    if($_POST['DOB'] != null)
    {
        $DOB = $_POST['DOB'];
    }
    
    if(isset($_POST['role'])) {
        $user_data = 'name='. $name. '&uname='. $uname. '&age='. $age. '&email='. $email. '&gender='. $gender. '&phone='. $phone. '&DOB='. $DOB. '&role='. $role ;
    }else{
        $user_data = 'name='. $name. '&uname='. $uname. '&age='. $age. '&email='. $email. '&gender='. $gender. '&phone='. $phone. '&DOB='. $DOB;
    }

    $sql = "SELECT * FROM users WHERE user_name='$uname' ";
    $result = mysqli_query($con, $sql);

    if(isset($_POST['role'])) {
        if (mysqli_num_rows($result) > 0) {
            echo '<script type="text/javascript">';
            echo 'alert("The username is taken. Please try another.");';
            echo 'window.location.href = "register_page.php?&' . $user_data . '";';
            echo '</script>';
            exit();
        }else {
            $sql2 = "INSERT INTO users(user_name, password, name, email , role, phone, age, DOB, gender) VALUES('$uname', '$hashedPass', '$name','$email','$role', '$phone','$age', '$DOB', '$gender')"; 
            $result2 = mysqli_query($con, $sql2);
            if ($result2) {
                echo '<script type="text/javascript">alert("Your account has been created successfully");</script>';
                echo '<script type="text/javascript">window.location.href = "register_page.php?";</script>';
                exit();
            }else {
                header("Location: register_page.php?error=unknown error occurred&$user_data");
                exit();
            }
        }
    }else{
        if (mysqli_num_rows($result) > 0) {
            echo '<script type="text/javascript">';
            echo 'alert("The username is taken. Please try another.");';
            echo 'window.location.href = "registration.php?&' . $user_data . '";';
            echo '</script>';
            exit();
        }else {
            $sql2 = "INSERT INTO users(user_name, password, name, email , role, phone, age, DOB, gender) VALUES('$uname', '$hashedPass', '$name','$email','student', '$phone','$age', '$DOB', '$gender')"; 
            $result2 = mysqli_query($con, $sql2);
            if ($result2) {
                echo '<script type="text/javascript">alert("Your account has been created successfully");</script>';
                echo '<script type="text/javascript">window.location.href = "login.php?";</script>';
                exit();
            }else {
                header("Location: registration.php?error=unknown error occurred&$user_data");
                exit();
            }
        }
    }

    mysqli_close($con);

?>