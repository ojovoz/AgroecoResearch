<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
$dbh = initDB();
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_treatment'])){
		header("Location: add_treatment.php");
	} else if(isset($_POST['menu'])){
		header("Location: menu.php");
	} else if(isset($_POST['treatment_colors'])){
		header("Location: treatment_colors.php");
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
<h2 class="w3-green">Treatments</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_treatment" name="add_treatment">Add treatment</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="treatment_colors" name="treatment_colors">Treatment colors</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="menu" name="menu">Main menu</button></form><br>
<?php
$query="(SELECT treatment_id AS id, treatment_name AS treatment, treatment_category AS category, ' ' AS crop1, ' ' AS crop2 FROM treatment WHERE primary_crop_id = NULL AND intercropping_crop_id = NULL) UNION (SELECT treatment_id AS id, treatment_name AS treatment, treatment_category AS category, (SELECT CONCAT(crop_name,' (',crop_variety_name,')') FROM crop WHERE crop_id = treatment.primary_crop_id) AS crop1, (SELECT CONCAT(crop_name,' (',crop_variety_name,')') FROM crop WHERE crop_id = treatment.intercropping_crop_id) AS crop2 FROM treatment) ORDER BY category, treatment";
$result = mysqli_query($dbh,$query);
$cat_color=0;
$prev_cat="";
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	if($prev_cat==""){
		$prev_cat=$row[2];
		echo('<div class="w3-container '.$row_color[$cat_color].'">Category: '.$row[2].'</div>');
	} else if ($row[2]!=$prev_cat){
		$prev_cat=$row[2];
		$cat_color++;
		if($cat_color==sizeof($row_color)){
			$cat_color=0;
		}
		echo('<br><div class="w3-container '.$row_color[$cat_color].'">Category: '.$row[2].'</div>');
	}
	echo('<div class="w3-container '.$row_color[$cat_color].'">');
	echo("Treatment: ".$row[1]);
	if(!is_null($row[3]) && !is_null($row[4])){
		echo(" -- Primary crop: ".$row[3]);
		echo(", Intercropping crop: ".$row[4]);
		$tname=$row[1].": ".$row[3]." with ".$row[4];
	} else {
		$tname=$row[1];
	}
?> -- <a class="w3-text-black" href="edit_treatment.php?id=<?php echo($row[0]); ?>">Edit</a> -- <a class="w3-text-black" href="delete_treatment.php?id=<?php echo($row[0]); ?>&tname=<?php echo($tname); ?>">Delete</a></div>
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