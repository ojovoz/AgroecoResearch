<?php
header("Cache-Control: no-cache, must-revalidate");
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
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
<h2 class="w3-green">Agroeco Research</h2><br>
<p><div align="center"><img src="agroeco_research_icon.png"><br><br><form method="get" action="https://github.com/ojovoz/AgroecoResearch/raw/master/app/AgroecoResearch/apk/agroeco_r.apk"><button class="w3-button w3-green w3-round w3-border w3-border-green w3-xlarge w3-round-large" style="width:192px;" id="download" type="submit">Download</button></form></div></p>
<p><div align="center"><a href="agroeco_manual.pdf">User's manual</a><br><br><form method="get" action="./../menu.php"><button class="w3-button w3-green w3-round w3-border w3-border-green w3-xlarge w3-round-large" style="width:192px;" id="menu" type="submit">Menu</button></form><br><br></div></p>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>