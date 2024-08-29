<?php 
    session_start();

    include "connection.php";

    if (!$con) {
        die(mysqli_connect_error());
    }

    $id = $_SESSION['userId'];
    $oldUname = $_SESSION['userOldName'];
    $name = $_POST['name'];
    $uname = $_POST['uname'];
    $age = date("Y") - date("Y", strtotime($_POST['DOB']));
    $pass = $_POST['pass'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
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

    $user_data = 'name='. $name. '&uname='. $uname. '&age='. $age. '&email='. $email. '&gender='. $gender. '&phone='. $phone. '&DOB='. $DOB;

    if($uname != $oldUname){
        $sql = "SELECT * FROM users WHERE user_name='$uname' ";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo '<script type="text/javascript">';
            echo 'alert("The username is taken. Please try another.");';
            echo 'window.location.href = "edit_user_page.php?id=' . $id . '&' . $user_data . '";';
            echo '</script>';
            exit();
        }
        else{
            $sql2 = "UPDATE users SET user_name='$uname', password='$hashedPass', name='$name', email='$email', phone='$phone', age='$age', DOB='$DOB', gender='$gender' WHERE id='$id'";
            $result2 = mysqli_query($con, $sql2);
            if ($result2) {
                echo '<script type="text/javascript">alert("Your account has been updated successfully");</script>';
                echo '<script type="text/javascript">window.location.href = "edit_user_page.php?id=' . $id . '"</script>';
                exit();
            }else {
                header("Location: edit_user_page.php?error=unknown error occurred&$user_data");
                exit();
            }
        }
    }else {
        $sql2 = "UPDATE users SET user_name='$uname', password='$hashedPass', name='$name', email='$email', phone='$phone', age='$age', DOB='$DOB', gender='$gender' WHERE id='$id'";
        $result2 = mysqli_query($con, $sql2);
        if ($result2) {
            echo '<script type="text/javascript">alert("Your account has been updated successfully");</script>';
            echo '<script type="text/javascript">window.location.href = "edit_user_page.php?id=' . $id . '"</script>';
            exit();
        }else {
            header("Location: edit_user_page.php?error=unknown error occurred&$user_data");
            exit();
        }
    }

    mysqli_close($con);

?>