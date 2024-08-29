<?php 
	session_start();
	
	include("connection.php");

	if (!$con) {
        die(mysqli_connect_error());
    }

	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		$user_name = $_POST['username'];
		$password = $_POST['password'];

		if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
		{
			$query = "select * from users where user_name = '$user_name' limit 1";
			$result = mysqli_query($con, $query);

			if($result)
			{
				if($result && mysqli_num_rows($result) > 0)
				{

					$user_data = mysqli_fetch_assoc($result);
					
					$hashPass = $user_data['password'];

					if(password_verify($password, $hashPass))
					{
						$_SESSION['id'] = $user_data['id'];
						$_SESSION['user_name'] = $user_data['user_name'];
						$_SESSION['role'] = $user_data['role'];
						echo '<script type="text/javascript">alert("Login Successfuly! Welcome ' . $user_name . '!");</script>';
						switch ($user_data['role']) {
							case 'teacher':
								echo '<script type="text/javascript">window.location.href = "teacher.php?";</script>';
								break;
							case 'student':
								echo '<script type="text/javascript">window.location.href = "index.php?";</script>';
								break;
							case 'admin':
								echo '<script type="text/javascript">window.location.href = "admin.php?";</script>';
								break;
						}						
						exit();
					}
				}
			}			
			echo '<script type="text/javascript">alert("Wrong username or password!");</script>';
		}else
		{
			echo '<script type="text/javascript">alert("Wrong username or password!");</script>';
		}
	}

	mysqli_close($con);

?>


<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="login.css">
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
	</head>
	<body>
		<form method="post">
			<div class="screen-1">
				<div style="font-size: 30px; margin-left: 150px; margin-right: 150px; color: black;">Login</div>
				<div class="username">
					<label>Username</label><br/>
					<img class="user_icon" src="user_icon.png" alt="User Icon">
					<input type="text" name="username" placeholder="Username" required/>
				</div>
				<div class="password">
					<label>Password</label><br/>
					<img class="pass_icon" src="password_icon.png" alt="Password Icon">
					<input class="pass" type="password" name="password" placeholder="·····················" required/>
				</div>
				<button class="login">Login </button>
				<div class="footer">
					<a href="registration.php">Signup</a>	
				</div>
			</div>
		</form>
	</body>
</html>