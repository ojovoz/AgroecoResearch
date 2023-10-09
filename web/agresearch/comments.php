<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	
	if(!isset($_GET['from'])){
		$from=0;
	} else {
		$from=$_GET['from'];
	}
	
	if(isset($_GET['delete'])){
		$delete_id=$_GET['delete'];
		$query="DELETE FROM general_observation WHERE general_observation_id = $delete_id";
		$result = mysqli_query($dbh,$query);
		$from=0;
	}
	
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script language="javascript" type="text/javascript">
<!--
function showPopup(url,width,height) {
	newwindow=window.open(url,'name','height=' + height +',width=' + width + ',top=50,left=50,screenX=50,screenY=50');
	if (window.focus) {newwindow.focus()}
	return false;
}

function goToMenu(){
	document.location = "menu.php";
}

function refresh(){
	document.location = "comments.php";
}

function confirmDelete(id){
	if(window.confirm("Delete comment?")){
		document.location = "comments.php?delete=" + id;
	}
}

// -->
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">General comments</h2><br>
<p><table class="w3-table w3-border w3-bordered w3-striped w3-hoverable w3-mobile w3-small">
  <thead>
	<tr class="w3-green">
	  <th>Author</th>
	  <th>Date</th>
	  <th>Location</th>
	  <th>Comment</th>
	  <th>Photo</th>
	  <th>&nbsp;</th>
	</tr>
  </thead>
<?php
$query="SELECT field_id, user_id, category, date, comments, image, general_observation_id FROM general_observation ORDER BY date DESC LIMIT $from, $max_log_items_per_page";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	$field_name=getFieldNameFromIdWithoutReplication($dbh,$row[0]);
	$author=getUserNameFromId($dbh,$row[1]);
	echo('<tr><td>'.$author.'</td>');
	echo('<td>'.$row[3].'</td>');
	echo('<td>'.$field_name.'</td>');
	echo('<td>Category: '.$row[2].'<br>'.$row[4].'</td>');
	if($row[5]!=""){
		$filename=$row[5];
		list($width, $height)=getimagesize($filename);
		$w=$width*(150/$height);
		$h=150;
		echo('<td><img src="'.$filename.'" width="'.$w.'" height="'.$h.'"></td>');
	} else {
		echo('<td>&nbsp;</td>');
	}
	echo('<td><a href="javascript: confirmDelete('.$row[6].');">Delete</a></td>');
	echo('</tr>');
}
?>
</table></p><br>
<div class="w3-row-padding"><div class="w3-half" align="center">
<?php
if($from>0){
	$prev=$from-$max_log_items_per_page;
	echo('<a href="comments.php?from='.$prev.'">Previous</a>');
} else {
	echo('&nbsp;');
}
?>
</div><div class="w3-half" align="center">
<?php 
$query="SELECT general_observation_id FROM general_observation";
$result = mysqli_query($dbh,$query);

if(mysqli_num_rows($result)>($from+$max_log_items_per_page)){
	$next=$from+$max_log_items_per_page;
	echo('<a href="comments.php?from='.$next.'">Next</a>');
} else {
	echo('&nbsp;');
}
?>
</div><br>
<button class="w3-button w3-green w3-round w3-border w3-border-green" id="add" name="add" type="button" style="width:20%; height:40px; max-width:300px;" onclick="showPopup('add_comment.php',800,600)">Add comment</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" id="menu" name="menu" type="button" style="width:20%; height:40px; max-width:300px;" onclick="goToMenu()">Menu</button><br><br>
</body>
</html>
<?php
} else {
	header("Location: index.php");
}
?>