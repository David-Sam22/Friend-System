<?php
require_once ("settings.php");
$DBConnect = @mysqli_connect($host, $user, $pswd,$dbnm)
	Or die("<p>Unable to connect to the database server.</p>"
	. "<p>Error code " . mysqli_connect_errno()
	. ": " . mysqli_connect_error()) . "</p>";
// TABLE 1 - friend
// create table if necessary 
$TableName1 = "friends";
$SQLstring = "SELECT * FROM $TableName1";
$QueryResult = @mysqli_query($DBConnect, $SQLstring);
if (!$QueryResult) {
 	$SQLstring = "CREATE TABLE IF NOT EXISTS $TableName1 (
				friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
				friend_email VARCHAR(50) NOT NULL, 
				password VARCHAR(20) NOT NULL,
				profile_name VARCHAR(30) NOT NULL,
				date_started DATE NOT NULL,
				num_of_friends INT unsigned)";
 	$QueryResult = @mysqli_query($DBConnect, $SQLstring) 		
	Or die("<p>Unable to create". $TableName1. " table.</p>"
 		. "<p>Error code " . mysqli_errno($DBConnect)
 		. ": " . mysqli_error($DBConnect)) . "</p>";
	if($QueryResult)
	{
		$table1 = true;
	}
}else
	$table1 = true;
// TABLE 2 - myfriends
$TableName2 = "myfriends";
$SQLstring = "SELECT * FROM $TableName2";
$QueryResult = @mysqli_query($DBConnect, $SQLstring);
if (!$QueryResult) {
 	$SQLstring = "CREATE TABLE IF NOT EXISTS $TableName2 (
				friend_id1 INT NOT NULL, 
				friend_id2 INT NOT NULL )";
 	$QueryResult = @mysqli_query($DBConnect, $SQLstring)
	Or die("<p>Unable to create ". $TableName2. " table.</p>"
 		. "<p>Error code " . mysqli_errno($DBConnect)
 		. ": " . mysqli_error($DBConnect)) . "</p>";
	if($QueryResult)
	{
		$table2 = true;
	}
}else
	$table2 = true;
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="description" content="My Friends System" />
		<meta name="keywords" content="PHP" />
		<meta name="author" content="David Sam" />
		<title>Assignment 2</title>
	</head>
	<body>
		<center><h1>My Friend System</h1>
		<h1>Assignment Home Page</h1></center>
		</br>
		<address>
			<div>
			<p><strong>Name: </strong> <font color="orange">David Sam</font></p>
			<p><strong>Student ID:</strong> <p>
			<p><strong>Email:</strong>  </>
			</div>
		</address>
		</br>
		<p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</>
		</br>

		<?php
		if($table1 && $table2)
		{
			echo "</br><font color='green'><p>Tables successfully created and populated.</p></font>";
		}
		?>
		</br>
		<div>
			<p style="text-align:center;">
			<span style="padding-right: 100px;">
				<a href="signup.php">Sign-Up</a>
			</span>
			<span style="padding-right: 100px;">
				<a href="login.php">Log-In</a>
			</span>
				<a href="about.php">About</a>
			</p>
		</div>
	</body>
</html>