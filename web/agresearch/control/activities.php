<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
$dbh = initDB();
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_activity'])){
		header("Location: add_activity.php");
	} else if(isset($_POST['menu'])){
		header("Location: menu.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Activities</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_activity" name="add_activity">Add activity</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="menu" name="menu">Main menu</button></form><br>
<?php
$query="SELECT activity_id, activity_name, activity_category FROM activity ORDER BY activity_category, activity_name";
$result = mysqli_query($dbh,$query);
$cat_color=0;
$prev_cat="";
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	if($prev_cat==""){
		$prev_cat=$row[2];
		echo('<div class="w3-container '.$row_color[$cat_color].'">Category: '.$row[2].'</div>');
	} else if ($row[2]!=$prev_cat){
		$prev_cat=$row[2];
		$cat_color++;
		if($cat_color==sizeof($row_color)){
			$cat_color=0;
		}
		echo('<br><div class="w3-container '.$row_color[$cat_color].'">Category: '.$row[2].'</div>');
	}
	$aname=$row[1]." (Category: ".$prev_cat.")";
	echo('<div class="w3-container '.$row_color[$cat_color].'">'.$row[1].' <a class="w3-text-black" href="edit_activity.php?id='.$row[0].'">Edit</a> -- <a class="w3-text-black" href="delete_activity.php?id='.$row[0].'&aname='.$aname.'">Delete</a> -- <a class="w3-text-black" href="activity_applied.php?id='.$row[0].'&aname='.$aname.'">Applies to...</a></div>');
}
?>
<br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>