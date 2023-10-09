<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	checkMessages($mail_server, $email, $password, $dbh);
	
	if(!isset($_GET['selected'])){
		$selected="";
	} else {
		$selected=$_GET['selected'];
	}
	
	if(!isset($_SESSION['report_log_field_filter'])){
		$_SESSION['report_log_field_filter']=" ";
	} else if($_SESSION['report_log_field_filter']==""){
		$_SESSION['log_field_filter']=" ";
	}
	
	if(!isset($_SESSION['report_log_date_filter'])){
		$_SESSION['report_log_date_filter']=" ";
	}
	
	if(!isset($_SESSION['report_log_measurement_filter'])){
		$_SESSION['report_log_measurement_filter']=" ";
		$_SESSION['report_measurement_category_filter']=" ";
	}
	
	if(!isset($_SESSION['report_max_messages']) || !is_numeric($_SESSION['report_max_messages'])){
		$max_messages=$max_log_items_per_page;
	} else {
		if($_SESSION['report_max_messages']<=0){
			$max_messages=$max_log_items_per_page;
		} else {
			$max_messages=$_SESSION['report_max_messages'];
		}
	}
	
	if(!isset($_GET['from']) || $_SESSION['report_reset']){
		$from=0;
		$selected="";
		$_SESSION['report_reset']=false;
	} else {
		$from=$_GET['from'];
	}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
<script language="javascript" type="text/javascript">
<!--
function showPopup(url,width,height) {
	newwindow=window.open(url,'name','height=' + height +',width=' + width + ',top=50,left=50,screenX=50,screenY=50');
	if (window.focus) {newwindow.focus()}
	return false;
}

function goToMenu(){
	document.location = "./../menu.php";
}

function goTo(n){
	var selected = document.getElementById("selected").value;
	document.location = 'measurement_report.php?from=' + n + "&selected=" + selected;
}

function generateReport(){
	var selected = document.getElementById("selected").value;
	if(selected==""){
		alert("No data points were selected");
	} else {
		var url = "generate.php?selected=" + selected
		showPopup(url,800,700);
	}
}

function addRemove(n,x){
	var add_to_report =  document.getElementsByName("add_to_report[]");
	var add = (add_to_report[n].checked);
	var selected = document.getElementById("selected").value;
	if(add){
		if(selected==""){
			selected = x;
		} else {
			selected += "*" + x;
		}
		document.getElementById("selected").value = selected;
	} else {
		var selected_parts = selected.split("*");
		var new_values = "";
		for(var i=0;i<selected_parts.length;i++){
			if(selected_parts[i]!=x){
				if(new_values==""){
					new_values = selected_parts[i];
				} else {
					new_values += "*" + selected_parts[i];
				}
			}
		}
		document.getElementById("selected").value = new_values;
	}
}
// -->
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Download data</h2>
<?php
if($_SESSION['report_filter_reminder']!=""){
	echo("Filtered by: ".$_SESSION['report_filter_reminder']."<br>");
}
?>
<table class="w3-table w3-border w3-bordered w3-striped w3-hoverable w3-mobile">
  <thead>
	<tr class="w3-green">
	  <th>Date</th>
	  <th>Location</th>
	  <th>Parameter</th>
	  <th>&nbsp;</th>
	</tr>
  </thead>
<?php
if($_SESSION['report_log_measurement_filter']!=" "){
	if($_SESSION['report_log_measurement_filter']>0){
		$measurement_filter=" AND measurement.measurement_id = ".$_SESSION['report_log_measurement_filter']." ";
	} else if($_SESSION['report_log_measurement_filter']==-1 && $_SESSION['report_measurement_category_filter']>0){
		$cat=$_SESSION['report_measurement_category_filter']-1;
		$query="SELECT measurement_id, measurement_category FROM measurement ORDER BY measurement_category";
		$result = mysqli_query($dbh,$query);
		$n=0;
		$prev_cat="";
		$measurement_filter="";
		while($row = mysqli_fetch_array($result,MYSQL_NUM)){
			$this_cat=$row[1];
			if($prev_cat==""){
				$prev_cat=$this_cat;
			} else if($this_cat!=$prev_cat){
				$prev_cat=$this_cat;
				$n++;
			}
			if($n==$cat){
				$measurement_filter = ($measurement_filter=="") ? " AND measurement.measurement_id IN(".$row[0] : $measurement_filter.",".$row[0];
			}
		}
		if($measurement_filter!=""){
			$measurement_filter.=") ";
		}
	} else {
		$measurement_filter="";
	}
}
$query="SELECT DISTINCT log.log_date AS date, field.parent_field_id AS field, log.measurement_id FROM log, field, measurement WHERE field.field_id = log.field_id AND measurement.measurement_id = log.measurement_id AND measurement.measurement_type <> 2".$_SESSION['report_log_field_filter'].$_SESSION['report_log_date_filter'].$measurement_filter."ORDER BY date DESC, field LIMIT $from, $max_messages";
$result = mysqli_query($dbh,$query);
$n=0;
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	echo('<tr>');
	echo('<td>'.$row[0].'</td>');
	echo('<td>'.getFieldNameFromIdWithoutReplication($dbh,$row[1]).'</td>');
	echo('<td>'.getMeasurementNameFromId($dbh,$row[2]).'</td>');
	$checked="";
	$selected_parts=explode("*",$selected);
	for($i=0;$i<sizeof($selected_parts);$i++){
		if($selected_parts[$i]==$row[0].','.$row[1].','.$row[2]){
			$checked=" checked";
			break;
		}
	}
	echo('<td><input class="w3-check" type="checkbox" name="add_to_report[]" value="'.$row[0].','.$row[1].','.$row[2].'" onClick="addRemove('.$n.',\''.$row[0].','.$row[1].','.$row[2].'\')"'.$checked.'> Add to report</td></tr>');
	$n++;
}
?>
</table>
<?php
if($n==0){ echo("No results"); }
?>
<div class="w3-row-padding"><div class="w3-half" align="center">
<?php
if($from>0){
	$prev=$from-$max_messages;
	echo('<a href="javascript:goTo('.$prev.');">Previous</a>');
} else {
	echo('&nbsp;');
}
?>
</div><div class="w3-half" align="center">
<?php 
if(getTotalItemsReport($dbh,$_SESSION['report_log_field_filter'],$_SESSION['report_log_date_filter'],$measurement_filter)>($from+$max_messages)){
	$next=$from+$max_messages;
	echo('<a href="javascript:goTo('.$next.');">Next</a>');
}
?></div></div>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="selected" type="hidden" id="selected" value="<?php echo($selected); ?>">
</form>
<button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:20%; height:40px; max-width:300px;" type="button" id="generate" name="generate" onclick="generateReport()">Generate report</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:20%; height:40px; max-width:300px;" type="button" id="filters" name="filters" onclick="return showPopup('filters.php',800,700)">Filters</button> <button class="w3-button w3-green w3-round w3-border w3-border-green" style="width:20%; height:40px; max-width:300px;" type="button" id="menu" name="menu" onclick="goToMenu()">Menu</button><br><br>
</div>
</body>
</html>
<?php
} else {
        header("Location: ./../index.php");
}
?>