<!DOCTYPE html>
<html>
    <head>
        <title>Registration</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="registration.css">
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    </head>
    <body>
        <form action="registrationProcess.php" method="POST">
            <div class="screen-1">
				<div style="font-size: 30px; margin-left: 150px; margin-right: 150px; color: black;">Sign Up</div>
				<div class="screen-1-class">
                    <label>Name :</label>
                    <?php if (isset($_GET['name'])) { ?>
                        <input type="text" name="name" placeholder="Full Name" required value="<?php echo $_GET['name']; ?>">
                    <?php }else{ ?>
                        <input type="text" name="name" placeholder="Full Name" required />
                    <?php }?>
                </div>
                <div class="screen-1-class">
                    <label>Username :</label>
                    <?php if (isset($_GET['uname'])) { ?>
                        <input type="text" name="uname" placeholder="Username" required value="<?php echo $_GET['uname']; ?>"><br>
                    <?php }else{ ?>
                        <input type="text" name="uname" placeholder="Username" required><br>
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
                        <input type="email" name="email" placeholder="xxx@xxx.xxx" required/><br/><div class="smallContent">Follow this format (xxx@xxx.xxx)</div>
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
                        <input type="radio" name="gender" value="M" required /><span>Male</span><br/>
                        <input type="radio" name="gender" value="F" required /><span>Female</span>
                    <?php } ?>
                </div>
                <div class="screen-1-class">
                    <label>Role :</label><br/>
                    <?php if (isset($_GET['role'])) { ?>
                        <?php if($_GET['role'] == "teacher") {?>
                            <input type="radio" name="role" value="teacher" required checked/><span>Teacher</span><br/>
                            <input type="radio" name="role" value="admin" required /><span>Admin</span>
                        <?php } else{ ?>
                            <input type="radio" name="role" value="teacher" required /><span>Teacher</span><br/>
                            <input type="radio" name="role" value="admin" required checked/><span>Admin</span>  
                        <?php } ?>
                    <?php } else { ?>
                        <input type="radio" name="role" value="teacher" required /><span>Teacher</span><br/>
                        <input type="radio" name="role" value="admin" required /><span>Admin</span>
                    <?php } ?>
                </div>
                <div class="screen-1-class">
                    <label>No.Phone(Option) :</label>
                    <?php if (isset($_GET['phone'])) { ?>
                        <input type="tel" name="phone" placeholder="xxx-xxxxxxx" pattern="[0-9]{3}-[0-9]{7,}" value="<?php echo $_GET['phone']; ?>" title="Must follow this format (xxx-xxxxxxx)" /><br/><div class="smallContent">Follow this format (xxx-xxxxxxx)</div>
                    <?php }else{ ?>
                        <input type="tel" name="phone" placeholder="xxx-xxxxxxx" pattern="[0-9]{3}-[0-9]{7,}" title="Must follow this format (xxx-xxxxxxx)" /><br/><div class="smallContent">Follow this format (xxx-xxxxxxx)</div>
                    <?php }?>
                </div>
                <div class="screen-1-class">
                    <label>Date of Birth :</label>
                    <?php if (isset($_GET['DOB'])) { ?>
                        <input type="date" name="DOB" required value="<?php echo $_GET['DOB']; ?>"/>
                    <?php }else{ ?>
                        <input type="date" name="DOB" required /><br/>
                    <?php }?>
                </div>
				<button class="submitReset">Submit </button>
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