<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
$dbh = initDB();
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_crop'])){
		header("Location: add_crop.php");
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
<h2 class="w3-green">Crops</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_crop" name="add_crop">Add crop</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="menu" name="menu">Main menu</button></form><br>
<?php
$query="SELECT crop_id, crop_name, crop_symbol, crop_variety_name FROM crop ORDER BY crop_name";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	echo("Crop: ".$row[1]."<br>Symbol: ".$row[2]."<br>Variety: ".$row[3]."<br>");
?><a class="w3-text-green" href="edit_crop.php?id=<?php echo($row[0]); ?>">Edit</a> -- <a class="w3-text-green" href="delete_crop.php?id=<?php echo($row[0]); ?>">Delete</a><br><br>
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