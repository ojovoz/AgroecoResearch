<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	
	$id=$_GET['id'];
	$query="SELECT input_log_id, input_log_date, field_name, field_replication_number, plots, crop_name, crop_variety_name, input_age, input_origin, input_quantity, input_cost, input_comments, input_picture, field.field_id, input_log.user_id, input_crop_variety, input_units FROM input_log, field, crop WHERE input_log_id=$id AND field.field_id = input_log.field_id AND crop.crop_id = input_log.crop_id";
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	
	$plot_labels = calculatePlotLabels($dbh,$row[13],$row[4]);
	
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
<h2 class="w3-green">Item details</h2>
<p><div class="w3-text-green">
<b>Registered by:</b> <?php echo(getUserNameFromId($dbh,$row[14])); ?><br>
<b>Field:</b> <?php echo($row[2]." R".$row[3]); ?><br>
<b>Plots:</b> <?php echo($plot_labels); ?><br>
<b>Crop:</b> <?php echo($row[5]); ?><br>
<b>Date:</b> <?php echo($row[1]); ?> <br>
<b>Age (days):</b> <?php echo($row[7]); ?> <br>
<b>Origin:</b> <?php echo($row[8]); ?><br>
<b>Variety:</b> <?php echo($row[15]); ?><br>
<b>Quantity (<?php echo($row[16]); ?>):</b> <?php echo($row[9]); ?><br>
<b>Cost (local currency):</b> <?php echo($row[10]); ?><br>
<?php
if($row[12]!=""){
	$filename=$row[12];
	list($width, $height)=getimagesize($filename);
	$w=$width*(150/$height);
	$h=150;
?>
<img src="<?php echo($filename); ?>" width="<?php echo($w); ?>" height="<?php echo($h); ?>"><br>
<?php
}
?>
<b>Comments:</b> <?php echo($row[11]); ?><br>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
<?php
} 
?>