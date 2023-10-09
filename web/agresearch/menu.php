<?php
header("Cache-Control: no-cache, must-revalidate");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['reports'])){
		header("Location: reports/index.php");
	} else if(isset($_POST['download'])){
		header("Location: app/index.php");
	} else if(isset($_POST['log'])){
		$_SESSION['reset']=false;
		header("Location: log.php");
	} else if(isset($_POST['notifications'])){
		header("Location: notifications.php");
	} else if(isset($_POST['weather'])){
		header("Location: weather/index.php");
	} else if(isset($_POST['docs'])){
		header("Location: docs/index.html");
	} else if(isset($_POST['comments'])){
		header("Location: comments.php");
	}
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Main menu</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" id="log" name="log">View/add/edit data</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" id="comments" name="comments">General comments</button></div><br>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" id="download" name="download">Download app</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" id="reports" name="reports">Download data</button></div><br>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" id="notifications" name="notifications" style="width:40%; height:40px; max-width:500px;">Notifications</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" id="weather" name="weather">Weather data</button></div><br>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" id="docs" name="docs" style="width:40%; height:40px; max-width:500px;">Research updates</button></div>
</p>
</form><br>
</div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>