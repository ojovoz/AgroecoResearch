<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_health_item'])){
		$item=normalize($_POST['item']);
		$item_categories=normalize($_POST['item_categories']);
		$query="INSERT INTO health_report_item (item, item_categories) VALUES ('$item', '$item_categories')";
		$result = mysqli_query($dbh,$query);
		header("Location: health.php");
	} else if(isset($_POST['cancel'])){
		header("Location: health.php");
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
<h2 class="w3-green">Add health report item</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">Item name:</label>
<input class="w3-input w3-border-green w3-text-green" name="item" type="text" maxlength="60"></p>
<p>      
<label class="w3-text-green">Item categories (separated by commas):</label>
<input class="w3-input w3-border-green w3-text-green" name="item_categories" type="text" maxlength="1000"></p>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_health_item" name="add_health_item">Add item</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>