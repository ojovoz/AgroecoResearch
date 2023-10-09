<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
$dbh = initDB();
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_health_item'])){
		header("Location: add_health_item.php");
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
<h2 class="w3-green">Health report items</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_health_item" name="add_health_item">Add item</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="menu" name="menu">Main menu</button></form><br>
<?php
$query="SELECT health_report_item_id, item, item_categories FROM health_report_item ORDER BY item";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	echo("Item: ".$row[1]."<br>Categories: ".stripslashes($row[2])."<br>");
?><a class="w3-text-green" href="edit_health_item.php?id=<?php echo($row[0]); ?>">Edit</a> -- <a class="w3-text-green" href="delete_health_item.php?id=<?php echo($row[0]); ?>">Delete</a><br><br>
<?php
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