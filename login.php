<?php
session_start(); // start the session
validateForm();
?>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="description" content="Web application development" />
		<meta name="keywords" content="PHP" />
		<meta name="author"   content="David Sam" />
		<title>Log-in</title>
	</head>
	<body>
		<center>
			<h2>My Friend System</h2>
			<h2>Log-in Page</h2>
		</center>
		</br>
		<span style="text-align:center">
			<form action="" method="POST">
				<p>Email: <input type="email" name="email" value="<?php echo isset($semail)?$semail:"";?>" required />
				</p>
				<p>Password: <input type="password" name="pwd" required title="- Only number and letter allowed." pattern="[A-Za-z0-9]+" />
				</p>
				<span style="padding-right: 40px;">
					<input type="submit" value="Log-in"/>
				</span>	
					<input type="reset" value="Clear"/>
			</form>
			</br>
		</span>
		<div  style="text-align:center">
			<a href="index.php"> Home </a>
		</div>
	</body>
</html>
<?php
function validateForm() // validate
{		
	global $semail;	
	$conn = getConnection();
	
	if(isset($_POST["email"]) && isset($_POST["pwd"]))
	{
		if(checkLogin($_POST["email"],$_POST["pwd"],$conn))
		{
			$emailcheck = true;
		}else
		{
			$emailcheck = false;
	}
		
		if($emailcheck)
		{
			redirect(); // when everything is success
		}else
		{
			$semail = $_POST["email"];
			$_SESSION["email"] = $semail;
		}
	}
}
?>
<?php
function checkLogin($input,$pass,$conn) // check if valid
{
	$isMatch = false;
	$SQLstring = "SELECT * FROM friends where friend_email='$input'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	if (mysqli_num_rows($QueryResult)==0) {		//if search not found return true = your email is not in records;
		echo "<p>". $_POST["email"] . " need to sign up, before you can log in.</p>";
		return $isMatch;  
	}else							// otherwise checked, if the existed, is match in the database.
	{
		while($rows = mysqli_fetch_assoc($QueryResult))
		{
			if($rows["password"] == $pass)
			{
				$isMatch = true;
				$_SESSION["info"] = $rows;
			}else
			{
				echo "<p> Password for ". $_POST["email"] . " is not correct.</p>";
			}
		}	
		return $isMatch;
	}
}
?>
<?php
function getConnection()
{
	require_once ("settings.php");
	$DBConnect = @mysqli_connect($host, $user, $pswd,$dbnm)
	Or die("<p>Unable to connect to the database server.</p>"
	. "<p>Error code " . mysqli_connect_errno()
	. ": " . mysqli_connect_error()) . "</p>";
	
	return $DBConnect;
}
?>
<?php
function redirect()
{
	$_SESSION["login"] = true;
	header("location:friendlist.php");
}
?>
