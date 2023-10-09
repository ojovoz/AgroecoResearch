<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['delete_crop'])){
		$crop_id=$_POST['id'];
		$query="DELETE FROM crop WHERE crop_id=$crop_id";
		$result = mysqli_query($dbh,$query);
		header("Location: crops.php");
	} else if(isset($_POST['cancel'])){
		header("Location: crops.php");
	}
} else if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$crop_id=$_GET['id'];
	$query="SELECT crop_name, crop_variety_name FROM crop WHERE crop_id=$crop_id";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$crop_name=$row[0]." (".$row[1].")";
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
<h2 class="w3-green">Delete crop</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="id" type="hidden" id="id" value="<? echo($crop_id); ?>">
<span class="w3-text-red">Are you sure you want to delete crop <?php echo($crop_name) ?>?</span><br><br>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="delete_crop" name="delete_crop">Delete</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button>
</form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>