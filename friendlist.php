<?php
session_start();	
$row = $_SESSION["info"];
?>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="description" content="Web application development" />
		<meta name="keywords" content="PHP" />
		<meta name="author"   content="David Sam" />
		<title>My Friend System</title>
	</head>
	<body>
		<center>
			<h2>My Friend System</h2>
			<h2><font color="red"><?php echo $row["profile_name"]; ?></font> Friend List Page</h2>
			<h2>Total number of friend is <font color="red"><?php echo $row["num_of_friends"]; ?></font></h2>
		</center>
		<?php
			showMyFriends();
		?>
			</br>
		<div  style="text-align:center;">
			<span style="padding-right: 100px;">
				<a href="friendadd.php"> Add Friends </a>
			</span>
				<a href="logout.php"> Log out </a>
		</div>
	</body>
</html>
<?php
function updateinfo($conn)
{
	global $row;
	$id = $row["friend_id"];
	$SQLstring = "Select * from friends where friend_id='$id'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	$rows = mysqli_fetch_assoc($QueryResult);
	return $rows;
}
?>

<?php
function showMyFriends()  // show friends from myfriends table.
{
	global $row;

	$conn = getConnection();
	$id = $row["friend_id"];
	$SQLstring = "Select * from myfriends where friend_id1='$id'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	if (mysqli_num_rows($QueryResult) > 0) 
	{
		while ($rows = mysqli_fetch_assoc($QueryResult))
		{	
			$thisID = getFriends($rows["friend_id2"],$conn);
			putintotable($thisID);
		}

	}else
	{
		echo "<p>You don't have any friends.</p>";	
	}
	
	if(isset($_POST["unf"]))
	{
		unfriend($_POST["unf"],$conn);
		$_SESSION["info"] = updateinfo($conn);
		header("location:friendlist.php");
	}
}
?>
<?php
function getFriends($getID,$conn)  // get specific friend by ID
{
	$result = "";
	$SQLstring = "Select * from friends where friend_id='$getID'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	if (mysqli_num_rows($QueryResult) > 0) 
	{
		$countrows = mysqli_num_rows($QueryResult);
		$row["num_of_friends"] = $countrows;
		while ($rows = mysqli_fetch_assoc($QueryResult))
		{
		$result = $rows;
		}
	}else
	{
		echo "<p>". $getID . " not found.</p>";
	}
	return $result;
}
?>


<?php
function putintotable($content)  // function to generate table
{?>
<center>
<table style="width:20%" border=1 >
  <col width="290">
  <col width="30">
  <tr>
    <td><?php echo $content["profile_name"] ?></td>
    <td>
		<form action="" method="POST">
			<button type="submit" class="button" name="unf" value=<?php echo $content["friend_id"]?>>Unfriend</button>
		</form>
	</td>
  </tr>
</table>
</center>
<?php
}
?>

<?php
function unfriend($id,$conn) // function to remove friend from mysql database;
{
	global $row;
	$myid= $row["friend_id"];
	$SQLstring = "Delete FROM myfriends where friend_id2='$id' and friend_id1='$myid'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	$SQLstring = "update friends set num_of_friends = num_of_friends - 1 where friend_id='$myid'";
	$QueryResult = @mysqli_query($conn, $SQLstring);
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

