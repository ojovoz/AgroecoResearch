<?php
if(isset($_GET['name'])){
	$filename="generated/".$_GET['name'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
<body>
</head>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Get your report</h2><br><br><br>
<p>
<div align="center"><a href="<?php echo($filename); ?>">Click here to download your report</a>
</div>
<p>
<div align="center"><button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:40%; height:40px; max-width:500px;" onclick="javascript:window.close();">Close</button></div><br></p>
<?php
}
?>