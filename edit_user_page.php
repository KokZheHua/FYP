<?php
    session_start();
    include("connection.php");

    if (!$con) {
        die(mysqli_connect_error());
    }

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
    }
    $_SESSION['userId'] = $_GET['id'];

    $query = "SELECT * FROM users WHERE id = '$id'";

    $result = mysqli_query($con, $query);

    if(!$result)
    {
        die('Error: ' . mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $username = $row['user_name'];
        $_SESSION['userOldName'] = $row['user_name'];
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $email = $row['email'];
        $gender = $row['gender'];
        $DOB = $row['DOB'];
    }
    mysqli_close($con);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit Account</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="registration.css">
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    </head>
    <body>
        <?php 
        if(isset($_GET['classroom_abbreviation'])){
            echo '<form action="updateProfileProcess.php?&classroom_abbreviation='.$classroom_abbreviation.'" method="POST">';
        }
        else{
            echo '<form action="updateUserProcess.php" method="POST">';
        }
        ?>
            <div class="screen-1">
				<div style="font-size: 30px; margin-left: 150px; margin-right: 150px; color: black;">Update Profile</div>
				<div class="screen-1-class">
                    <label>Name :</label>
                    <?php if (isset($_GET['name'])) { ?>
                        <input type="text" name="name" placeholder="Full Name" required value="<?php echo $_GET['name']; ?>">
                    <?php }else{ ?>
                        <input type="text" name="name" placeholder="Full Name" required value="<?php echo $name; ?>">
                    <?php }?>
                </div>
                <div class="screen-1-class">
                    <label>Username :</label>
                    <?php if (isset($_GET['uname'])) { ?>
                        <input type="text" name="uname" placeholder="Username" required value="<?php echo $_GET['uname']; ?>"><br>
                    <?php }else{ ?>
                        <input type="text" name="uname" placeholder="Username" required value="<?php echo $username; ?>"><br>
                    <?php }?>
                </div>
                <div class="screen-1-class">
                    <label>Password :</label>
                    <input type="password" name="pass" id="pass" placeholder="·····················" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,}" title="Must contain at least one number and one uppercase and lowercase letter" required /><br/><div class="smallContent">(include one number, one uppercase and one lowercase)</div>
                </div>
                <div class="screen-1-class">
                    <label>Confirm Password :</label>
                    <input type="password" name="confirmPass" id="confirmPass" placeholder="·····················" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,}" title="Must contain at least one number and one uppercase and lowercase letter" required /><br/>
                </div>
                <div class="screen-1-class">
                    <label>Email :</label>
                    <?php if (isset($_GET['email'])) { ?>
                        <input type="email" name="email" placeholder="xxx@xxx.xxx" required value="<?php echo $_GET['email']; ?>"><br/><div class="smallContent">Follow this format (xxx@xxx.xxx)</div>
                    <?php }else{ ?>
                        <input type="email" name="email" placeholder="xxx@xxx.xxx" required value="<?php echo $email; ?>"><br/><div class="smallContent">Follow this format (xxx@xxx.xxx)</div>
                    <?php }?>
                </div>
                <div class="screen-1-class">
                    <label>Gender :</label><br/>
                    <?php if (isset($_GET['gender'])) { ?>
                        <?php if($_GET['gender'] == "M") {?>
                            <input type="radio" name="gender" value="M" required checked/><span>Male</span><br/>
                            <input type="radio" name="gender" value="F" required /><span>Female</span>
                        <?php } else{ ?>
                            <input type="radio" name="gender" value="M" required /><span>Male</span><br/>
                            <input type="radio" name="gender" value="F" required checked/><span>Female</span>  
                        <?php } ?>
                    <?php } else { ?>
                        <?php if($gender == "M") {?>
                            <input type="radio" name="gender" value="M" required checked/><span>Male</span><br/>
                            <input type="radio" name="gender" value="F" required /><span>Female</span>
                        <?php } else{ ?>
                            <input type="radio" name="gender" value="M" required /><span>Male</span><br/>
                            <input type="radio" name="gender" value="F" required checked/><span>Female</span>  
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="screen-1-class">
                    <label>No.Phone(Option) :</label>
                    <?php if (isset($_GET['phone'])) { ?>
                        <input type="tel" name="phone" placeholder="xxx-xxxxxxx" pattern="[0-9]{3}-[0-9]{7,}" value="<?php echo $_GET['phone']; ?>" title="Must follow this format (xxx-xxxxxxx)" /><br/><div class="smallContent">Follow this format (xxx-xxxxxxx)</div>
                    <?php }else{ ?>
                        <input type="tel" name="phone" placeholder="xxx-xxxxxxx" pattern="[0-9]{3}-[0-9]{7,}" value="<?php echo $phone; ?>" title="Must follow this format (xxx-xxxxxxx)" /><br/><div class="smallContent">Follow this format (xxx-xxxxxxx)</div>
                    <?php }?>
                </div>
                <div class="screen-1-class">
                    <label>Date of Birth :</label>
                    <?php if (isset($_GET['DOB'])) { ?>
                        <input type="date" name="DOB" required value="<?php echo $_GET['DOB']; ?>"/>
                    <?php }else{ ?>
                        <input type="date" name="DOB" required value="<?php echo $DOB; ?>"/>
                    <?php }?>
                </div>
				<button class="submitReset">Update </button>
                <button class="submitReset" type="reset">Reset</button>
				<div class="footer">
                    <a href="admin.php">Back</a>
				</div>
			</div>
        </form>
        <script>
            function verifyPass(){
                var pass1 = document.getElementById("pass");
                var pass2 = document.getElementById("confirmPass");
                if (pass1.value != pass2.value)
                {
                    confirmPass.setCustomValidity("Password not match!!!!!");
                }
                else
                {
                    confirmPass.setCustomValidity('');
                }
            }
            pass.onchange = verifyPass;
            confirmPass.onkeyup = verifyPass;
        </script>
    </body>
</html>