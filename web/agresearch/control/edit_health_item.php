<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_item'])){
		$item_id=$_POST['id'];
		$item=normalize($_POST['item']);
		$item_categories=normalize($_POST['item_categories']);
		$query="UPDATE health_report_item SET item='$item', item_categories='$item_categories' WHERE health_report_item_id=$item_id";
		$result = mysqli_query($dbh,$query);
		header("Location: health.php");
	} else if(isset($_POST['cancel'])){
		header("Location: health.php");
	}
} else if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$item_id=$_GET['id'];
	$query="SELECT item, item_categories FROM health_report_item WHERE health_report_item_id=$item_id";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$item=$row[0];
		$item_categories=stripslashes($row[1]);
	}
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
<h2 class="w3-green">Edit health report item</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="id" type="hidden" id="id" value="<? echo($item_id); ?>">
<p>      
<label class="w3-text-green">Item name:</label>
<input class="w3-input w3-border-green w3-text-green" name="item" type="text" maxlength="30" value="<?php echo($item); ?>"></p>
<p>      
<label class="w3-text-green">Item categories (separated by commas):</label>
<input class="w3-input w3-border-green w3-text-green" name="item_categories" type="text" maxlength="1000" value="<?php echo($item_categories); ?>"></p>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_item" name="edit_item">Edit item</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>