<?php 
    session_start();

    include "connection.php";
    
    $id = $_SESSION['id'];
    $oldUname = $_SESSION['user_name'];
    $previous_page = $_SESSION['previous_page'];
    
    if(isset($_GET['classroom_abbreviation'])){
        $classroom_abbreviation = $_GET['classroom_abbreviation'];
    }

    if (!$con) {
        die(mysqli_connect_error());
    }

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
            if(isset($_GET['classroom_abbreviation'])){
                echo 'window.location.href = "profile.php?&previous_page='. $previous_page .'&classroom_abbreviation='. $classroom_abbreviation .'&' . $user_data . '";';
            }
            else{
                echo 'window.location.href = "profile.php?&previous_page='. $previous_page .'&' . $user_data . '";';
            }
            echo '</script>';
            exit();
        }
        else{
            $sql2 = "UPDATE users SET user_name='$uname', password='$hashedPass', name='$name', email='$email', phone='$phone', age='$age', DOB='$DOB', gender='$gender' WHERE id='$id'";
            $result2 = mysqli_query($con, $sql2);
            if ($result2) {
                $_SESSION['user_name'] = $uname;
                echo '<script type="text/javascript">alert("Your account has been updated successfully");</script>';
                if(isset($_GET['classroom_abbreviation'])){
                    echo '<script type="text/javascript">window.location.href = "profile.php?&previous_page='. $previous_page .'&classroom_abbreviation='. $classroom_abbreviation .'"</script>';
                }
                else{
                    echo '<script type="text/javascript">window.location.href = "profile.php?&previous_page='. $previous_page .'"</script>';
                }
                exit();
            }else {
                header("Location: profile.php?error=unknown error occurred&$user_data");
                exit();
            }
        }
    }else {
        $sql2 = "UPDATE users SET user_name='$uname', password='$hashedPass', name='$name', email='$email', phone='$phone', age='$age', DOB='$DOB', gender='$gender' WHERE id='$id'";
        $result2 = mysqli_query($con, $sql2);
        if ($result2) {
            $_SESSION['user_name'] = $uname;
            echo '<script type="text/javascript">alert("Your account has been updated successfully");</script>';
            if(isset($_GET['classroom_abbreviation'])){
                echo '<script type="text/javascript">window.location.href = "profile.php?&previous_page='. $previous_page .'&classroom_abbreviation='. $classroom_abbreviation .'"</script>';
            }
            else{
                echo '<script type="text/javascript">window.location.href = "profile.php?&previous_page='. $previous_page .'"</script>';
            }
            exit();
        }else {
            header("Location: profile.php?error=unknown error occurred&$user_data");
            exit();
        }
    }

    mysqli_close($con);

?>