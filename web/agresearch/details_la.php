<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	
	$id=$_GET['id'];
	$query="SELECT log_id, log_date, field_name, field_replication_number, plots, activity_name, log_value_units, log_value_number, log_comments, log_picture, log_number_of_laborers, log_cost, field.field_id, log.user_id FROM log, field, activity WHERE log_id=$id AND field.field_id = log.field_id AND activity.activity_id = log.activity_id";
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	
	$plot_labels = calculatePlotLabels($dbh,$row[12],$row[4]);
	
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
<b>Registered by:</b> <?php echo(getUserNameFromId($dbh,$row[13])); ?><br>
<b>Field:</b> <?php echo($row[2]." R".$row[3]); ?><br>
<b>Plots:</b> <?php echo($plot_labels); ?><br>
<b>Activity:</b> <?php echo($row[5]); ?><br>
<b>Date:</b> <?php echo($row[1]); ?> <br>
<b>Value (<?php echo($row[6]); ?>):</b> <?php echo($row[7]); ?> <br>
<b>Number of laborers:</b> <?php echo($row[10]); ?><br>
<b>Cost:</b> <?php echo($row[11]); ?><br>
<b>Comments:</b> <?php echo($row[8]); ?><br>
<?php
if($row[9]!=""){
	$filename=$row[9];
	list($width, $height)=getimagesize($filename);
	$w=$width*(150/$height);
	$h=150;
?>
<img src="<?php echo($filename); ?>" width="<?php echo($w); ?>" height="<?php echo($h); ?>"><br>
<?php
}
?>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
<?php
} 
?>