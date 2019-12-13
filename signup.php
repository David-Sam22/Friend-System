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
		<title>Sign-Up</title>
	</head>
	<body>
		<center>
			<h2>My Friend System</h2>
			<h2>Registration Page</h2>
		</center>
		</br>
		<span style="text-align:center">
		<style>
		</style>
			<form action="" method="POST">
				<p>Email: <input type="email" name="email" value="<?php echo isset($semail)?$semail:"";?>" required />
				</p>
				<p>Profile Name: <input type="text" name="pname" value="<?php echo isset($sname)?$sname:"";?>" required title="- Letter only." pattern="[A-Za-z]+" />
				</p>
				<p>Password: <input type="password" name="pwd" required title="- Only number and letter allowed." pattern="[A-Za-z0-9]+" />
				</p>
				<p>Confirm Password: <input type="password" name="cpwd" required />
				</p>
				<span style="padding-right: 40px;">
					<input type="submit" value="Register"/>
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
function checkPwd() // check if both password matched
{
	$result = true;
	if(isset($_POST["pwd"]) && isset($_POST["cpwd"]))
	{
		$pwd = $_POST["pwd"];
		$cpwd = $_POST["cpwd"];
		if(strcmp($pwd,$cpwd))
		{
			$result = false;
		}
	}
	return $result;
}
?>
<?php
function checkEmail($search,$conn) // check if email is already registered
{
	$SQLstring = "SELECT * FROM friends where friend_email='$search'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	if (mysqli_num_rows($QueryResult)==0) { //if search not found return true = your email is not in records and you good to go;
		return true;  
	}else
		return false;
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
function redirect() // when done signed up
{
	$_SESSION["signup"] = true;
	header("location:friendadd.php");
}
?>
<?php
function getInfo($search,$conn) // function to get info of search and store in session
{
	$SQLstring = "SELECT * FROM friends where friend_email='$search'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	while($rows = mysqli_fetch_assoc($QueryResult))
	{
		$_SESSION["info"] = $rows;
	}
}
?>
<?php
function validateForm() // validate
{		
	global $semail;
	global $sname;
			
	$conn = getConnection();
	$errs = array();
	
	if(isset($_POST["email"]) && isset($_POST["pname"]) && isset($_POST["pwd"]) && isset($_POST["cpwd"]))
	{
		if(checkEmail($_POST["email"],$conn))
		{
			$emailcheck = true;
		}else
		{
			$emailcheck = false;
			array_push($errs,"<p>". $_POST["email"] . " already exist. Try different email.</p>");
		}
		
		if(checkPwd()) // if not matched
		{
			$passcheck = true;
		}else
		{
			$passcheck = false;
			array_push($errs,"<p> Password do not match. Try again.</p>");
		}
		
		if($passcheck && $emailcheck)
		{
			$email = $_POST["email"];
			$pwd = $_POST["pwd"];
			$pname = $_POST["pname"];
			$date = date("Y-m-d");
			$SQLstring = "insert into friends(friend_email,password,profile_name,date_started,num_of_friends) Values ('$email','$pwd','$pname','$date',0)";
			$QueryResult = @mysqli_query($conn, $SQLstring);
			getInfo($email,$conn);
			redirect(); // when everything is success
		}else
		{
			$semail = $_POST["email"];
			$sname = $_POST["pname"];
			$_SESSION["name"] = $sname;
			$_SESSION["email"] = $semail;

			if(!empty($errs))
			{
				foreach($errs as &$err)
				{
					echo $err;
				}
			}
		}
	}
}
?>
