<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_crop'])){
		$crop_name=normalize($_POST['crop_name']);
		$crop_symbol=normalize($_POST['crop_symbol']);
		$crop_variety_name=normalize($_POST['crop_variety_name']);
		if(isset($_POST['crop_used_for_intercropping'])){
			$crop_used_for_intercropping=$_POST['crop_used_for_intercropping'];
		} else {
			$crop_used_for_intercropping=0;
		}
		$query="INSERT INTO crop (crop_name, crop_symbol, crop_variety_name, crop_used_for_intercropping) VALUES ('$crop_name', '$crop_symbol', '$crop_variety_name', $crop_used_for_intercropping)";
		$result = mysqli_query($dbh,$query);
		header("Location: crops.php");
	} else if(isset($_POST['cancel'])){
		header("Location: crops.php");
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
<h2 class="w3-green">Add crop</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">Crop name:</label>
<input class="w3-input w3-border-green w3-text-green" name="crop_name" type="text" maxlength="20"></p>
<p>      
<label class="w3-text-green">Crop symbol:</label>
<input class="w3-input w3-border-green w3-text-green" name="crop_symbol" type="text" maxlength="10"></p>
<p>      
<label class="w3-text-green">Crop variety:</label>
<input class="w3-input w3-border-green w3-text-green" name="crop_variety_name" type="text" maxlength="40"></p>
<p><input class="w3-check" type="checkbox" value="1" name="crop_used_for_intercropping" id="crop_used_for_intercropping">
<label class="w3-validate w3-text-green">Crop used for intercropping</label></p>
<p>      
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_crop" name="add_crop">Add crop</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>