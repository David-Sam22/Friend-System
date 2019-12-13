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
// how many number of records to display per page.
	    if (isset($_GET['pageno'])) {
            $pageno = $_GET['pageno'];
        } else {
            $pageno = 1;
        }
		
		$conn = getConnection();
		$id = $row["friend_id"];
		$no_of_records_per_page = 5;
		$offset = ($pageno-1) * $no_of_records_per_page;
		
        $total_pages_sql = "SELECT count(*) As total_records FROM friends WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = '$id') order by profile_name ASC";
        $result = mysqli_query($conn,$total_pages_sql);
        $total_rows = mysqli_fetch_array($result)[0];
        $total_pages = ceil($total_rows / $no_of_records_per_page);

//put to table for friend that is not exist in myfriends database
        $SQLstring = "SELECT * FROM friends WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = '$id') order by profile_name ASC LIMIT $offset, $no_of_records_per_page";
		$QueryResult = @mysqli_query($conn, $SQLstring);
		if (mysqli_num_rows($QueryResult) > 0) 
		{
			while ($rows = mysqli_fetch_assoc($QueryResult))
			{	
				$addable = true;
				if($rows["friend_id"] != $id)
				{
					putintotable($rows,getMutual($conn,$rows["friend_id"]),$id,$addable);	
				}else
				{
					$addable = false;
					$mutual = "-";
					putintotable($rows,$mutual,$id,$addable);
				}
			}
		}else
		{
			echo "<p>You don't have any friends.</p>";	
		}
		if(isset($_POST["add"]))
		{
			Addfriend($_POST["add"],$conn);
			$_SESSION["info"] = updateinfo($conn);
			header("location:friendadd.php");
		}
			mysqli_close($conn);
	?>
	
    <ul class="pagination">
	<span style="padding-left: 200px;">
	<?php if ($pageno != 1) {?>
		<span class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
			<a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
		</span>
	<?php }?>
	</span>
	<span  style="padding-left: 200px;">
	<?php if ($pageno != $total_pages) {?>
		<span class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
			<a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1);} ?>">Next</a>
		</span>
	<?php }?>
	</span>
    </ul>
	</br>
	<div  style="text-align:center">
		<span style="padding-right: 100px;">
			<a href="friendlist.php"> Friend Lists </a>
		</span>
			<a href="logout.php"> Log out </a>
	</div>

	</body>
</html>


<?php
function updateinfo($conn) // update session
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
function getMutual($conn,$friID) // return row that's has the same id in the list.
{	
	global $row;
	$id = $row["friend_id"];
	$SQLstring = "SELECT *
	FROM (
	  SELECT *
	  FROM myfriends
	  WHERE friend_id1 = '$id'
	) p1 INNER JOIN (
	  SELECT *
	  FROM myfriends
	  WHERE friend_id1 = '$friID'
	) p2
	  ON p1.friend_id2 = p2.friend_id2";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	if(mysqli_num_rows($QueryResult) != 0)
	{
		$count = mysqli_num_rows($QueryResult);
	}else
	{
		$count = 0;
	}
	return $count;
}
?>

<?php
function putintotable($content,$mutual,$id,$addable) // function to add to table
{?>
<center>
<table style="width:50%" border=1 >
  <col width="100">
  <col width="200">
  <col width="120">
  <tr>
    <td><?php echo $content["profile_name"] ?></td>
	<td><?php echo $mutual . " mutual friends" ?></td>
    <td>
	<?php if($addable){?>
		<form action="" method="POST">
			<button type="submit" class="button" name="add" value=<?php echo $content["friend_id"]?>>Add as friend</button>
		</form>
	<?php } else {?>
		<form action="" method="POST">
			<button type="submit" class="button" name="add" disabled value=<?php echo $content["friend_id"]?>>Add as friend</button>
		</form>
	<?php }?>
	</td>
  </tr>
</table>
</center>
<?php
}
?>

<?php
function Addfriend($content,$conn) // query to add friend to database
{
	Global $row;
	$id = $row["friend_id"];
	$SQLstring = "insert into myfriends(friend_id1,friend_id2) Values ('$id','$content')";
	$QueryResult = @mysqli_query($conn, $SQLstring);
	$SQLstring = "update friends set num_of_friends = num_of_friends + 1 where friend_id='$id'";
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