<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	
	$id=$_GET['id'];
	$query="SELECT log_id, log_date, field_name, field_replication_number, plots, measurement_name, log_value_units, measurement_type, log_value_number, log_value_text, log_comments, log_picture, field.field_id, measurement_has_sample_number, log.user_id, measurement_category FROM log, field, measurement WHERE log_id=$id AND field.field_id = log.field_id AND measurement.measurement_id = log.measurement_id";
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
<b>Registered by:</b> <?php echo(getUserNameFromId($dbh,$row[14])); ?><br>
<b>Field:</b> <?php echo($row[2]." R".$row[3]); ?><br>
<b>Plot:</b> <?php echo($plot_labels); ?><br>
<b>Measurement:</b> <?php echo($row[5]. " (".$row[15].")"); ?><br>
<b>Date:</b> <?php echo($row[1]); ?> <br>
<?php
if($row[13]==0){
	if($row[7]==0){
	?>
<b>Value:</b> <?php echo($row[9]); ?><br>
<?php
	} else {
?>
<b>Value (<?php echo($row[6]); ?>):</b> <?php echo($row[8]); ?><br>
<?php
	}
} else {
	if($row[7]==0){
		$values=parseSampleValues($row[9]);
	?>
<b>Values (sample #:value)</b> <?php echo($values); ?><br>
<?php	
	} else if ($row[7]==1) {
		$values=parseSampleValues($row[9]);
	?>
<b>Values (sample #:value in <?php echo($row[6]); ?>)</b> <?php echo($values); ?><br>
<?php
	} else {
		$values=parseHealthReportValues($dbh,$row[9]);
	?>
	<b>Health report (sample #:problems)</b><br> <?php echo($values); ?><br>
<?php
	}
}
?>
<?php
if($row[11]!=""){
	$filename=$row[11];
	list($width, $height)=getimagesize($filename);
	$w=$width*(150/$height);
	$h=150;
?>
<img src="<?php echo($filename); ?>" width="<?php echo($w); ?>" height="<?php echo($h); ?>"><br>
<?php
}
?>
<b>Comments:</b> <?php echo($row[10]); ?><br>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
<?php
} 
?>