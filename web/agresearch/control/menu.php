<?php
header("Cache-Control: no-cache, must-revalidate");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['users'])){
		header("Location: users.php");
	} else if(isset($_POST['crops'])){
		header("Location: crops.php");
	} else if(isset($_POST['treatments'])){
		header("Location: treatments.php");
	} else if(isset($_POST['measurements'])){
		header("Location: measurements.php");
	} else if(isset($_POST['activities'])){
		header("Location: activities.php");
	} else if(isset($_POST['fields'])){
		header("Location: fields.php");
	} else if(isset($_POST['notifications'])){
		header("Location: notifications.php");
	} else if(isset($_POST['health'])){
		header("Location: health.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
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
<h2 class="w3-green">Control panel</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" id="users" name="users">Users</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" id="fields" name="fields" style="width:40%; height:40px; max-width:500px;">Fields</button></div><br>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" id="crops" name="crops" style="width:40%; height:40px; max-width:500px;">Crops</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" id="treatments" name="treatments" style="width:40%; height:40px; max-width:500px;">Treatments</button></div><br>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" id="activities" name="activities" style="width:40%; height:40px; max-width:500px;">Activities</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" id="measurements" name="measurements" style="width:40%; height:40px; max-width:500px;">Measurements</button></div>
<br>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" id="health" name="health" style="width:40%; height:40px; max-width:500px;">Health report</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" id="notifications" name="notifications" style="width:40%; height:40px; max-width:500px;">Notifications</button></div><br>
</form><br>
</div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>