<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['generate'])){
	if(substr_count($_POST['field'],',')>0){
		$log_field_filter=" AND log.field_id IN(".$_POST['field'].") ";
	} else {
		$log_field_filter=" AND log.field_id=".$_POST['field']." ";
	}
		
	if(isset($_POST['toggle_dates'])){
		$dd1=$_POST['dd1'];
		$mm1=$_POST['mm1'];
		$yy1=$_POST['yy1'];
		$dd2=$_POST['dd2'];
		$mm2=$_POST['mm2'];
		$yy2=$_POST['yy2'];
		if(checkdate($mm1,$dd1,$yy1) && checkdate($mm2,$dd2,$yy2)){
			$date1 = strtotime($yy1."-".$mm1."-".$dd1);
			$date2 = strtotime($yy2."-".$mm2."-".$dd2);
			if($date1>$date2){
				$log_date_filter=" AND (log.log_date BETWEEN '".$yy2."-".$mm2."-".$dd2."' AND '".$yy1."-".$mm1."-".$dd1."') ";
			} else if($date1<$date2) {
				$log_date_filter=" AND (log.log_date BETWEEN '".$yy1."-".$mm1."-".$dd1."' AND '".$yy2."-".$mm2."-".$dd2."') ";
			} else {
				$log_date_filter=" AND log.log_date = '".$yy1."-".$mm1."-".$dd1."' ";
			}
		} else {
			$log_date_filter=" ";
		}
	} else {
		$log_date_filter=" ";
		$date1=strtotime("2018-03-01");
		$date2=strtotime(date("Y-m-d"));
	}
	if(isset($_POST['toggle_activity'])){
		$activities="";
		foreach ($_POST['activity'] as $selected_activity){
			if($activities==""){
				$activities=$selected_activity;
			} else {
				$activities.=",".$selected_activity;
			}
		}
		$log_activity_filter=" AND log.activity_id IN(".$activities.") ";
	} else {
		$log_activity_filter=" ";
	}
	if(isset($_POST['toggle_measurement'])){
		$measurements="";
		foreach ($_POST['measurement'] as $selected_measurement){
			if($measurements==""){
				$measurements=$selected_measurement;
			} else {
				$measurements.=",".$selected_measurement;
			}
		}
		$log_measurement_filter=" AND log.measurement_id IN(".$measurements.") ";
	} else {
		$log_measurement_filter=" ";
	}
	
	//generate report
	
	if(isset($_POST['toggle_activity'])){
		$query_activities="SELECT activity_name FROM activity WHERE activity_id IN(".$activities.") ORDER BY activity_category, activity_name";
	} else {
		$query_activities="SELECT activity_name FROM activity ORDER BY activity_category, activity_name";
	}
	$result = mysqli_query($dbh,$query_activities);
	$all_activities=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		array_push($all_activities,$row[0]);
	}
	
	if(isset($_POST['toggle_measurement'])){
		$query_measurements="SELECT measurement_name, measurement_category, measurement_subcategory FROM measurement WHERE measurement_id IN(".$measurements.") ORDER BY measurement_category, measurement_subcategory, measurement_name";
	} else {
		$query_measurements="SELECT measurement_name, measurement_category, measurement_subcategory FROM measurement ORDER BY measurement_category, measurement_subcategory, measurement_name";
	}
	$result = mysqli_query($dbh,$query_measurements);
	$all_measurements=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$measurement_string=strval($row[0])." (".strval($row[1])." - ".strval($row[2]).")";
		array_push($all_measurements,$measurement_string);
	}
	
	
	$query1="SELECT DISTINCT user.user_name, activity.activity_name, log.log_date FROM log,user,activity WHERE user.user_id=log.user_id ".$log_field_filter.$log_date_filter.$log_activity_filter." AND activity.activity_id=log.activity_id ORDER BY log.log_date,activity.activity_name";
	$result = mysqli_query($dbh,$query1);
	$registered_activities=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		array_push($registered_activities,getUserInitials($row[0]).",".$row[1].",".$row[2]);
	}
	
	$query2="SELECT DISTINCT user.user_name, measurement.measurement_name, measurement.measurement_category, measurement.measurement_subcategory, log.log_date FROM log,user,measurement WHERE user.user_id=log.user_id ".$log_field_filter.$log_date_filter.$log_measurement_filter." AND measurement.measurement_id=log.measurement_id ORDER BY log.log_date,measurement.measurement_name";
	$result = mysqli_query($dbh,$query2);
	$registered_measurements=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$measurement_string=strval($row[1])." (".strval($row[2])." - ".strval($row[3]).")";
		array_push($registered_measurements,getUserInitials($row[0]).",".$measurement_string.",".$row[4]);
	}
	
	$filename="timeline".date("Y-m-d").".csv";
	
	$files = glob('generated/*'); 
	foreach($files as $file){ 
		if(is_file($file))
			unlink($file);
	}
	$df = fopen("generated/".$filename, 'w');
	
	//title
	$fname=getFieldNameFromId($dbh,$_POST['field']);
	$title=array("Field: ".$fname);
	fputcsv($df, $title);
	$title=array("Period: ".date("Y/m/d",$date1)." to ".date("Y/m/d",$date2));
	fputcsv($df, $title);
	
	//row header
	$current_date=$date1;
	$date_array=array();
	$date_array_display=array();
	array_push($date_array,"");
	array_push($date_array_display,"");
	while($date2>=$current_date){
		array_push($date_array,date('Y-m-d',date($current_date)));
		array_push($date_array_display,date('M j',date($current_date)));
		$current_date=strtotime(date('Y-m-d',$current_date).' + 1 day');
	}
	fputcsv($df, $date_array_display);
	
	if(isset($_POST['toggle_activity'])){
		$title=array("Activities");
		fputcsv($df, $title);
	
		//activities
		for($i=0;$i<sizeof($all_activities);$i++){
			$activity_row=array();
			array_push($activity_row,$all_activities[$i]);
			for($j=1;$j<sizeof($date_array);$j++){
				$current_date=$date_array[$j];
				$found=false;
				for($k=0;$k<sizeof($registered_activities);$k++){
					$registered_activities_parts=explode(",",$registered_activities[$k]);
					if(($registered_activities_parts[2]==$current_date) && ($registered_activities_parts[1]==$all_activities[$i])){
						array_push($activity_row,$registered_activities_parts[0]);
						$found=true;
						break;
					}
				}
				if(!$found){
					array_push($activity_row,"");
				}
			}
			fputcsv($df, $activity_row);
		}
	}
	
	
	if(isset($_POST['toggle_measurement'])){
		$title=array("Measurements");
		fputcsv($df, $title);
	
		//measurements
	
		for($i=0;$i<sizeof($all_measurements);$i++){
			$measurement_row=array();
			array_push($measurement_row,$all_measurements[$i]);
			for($j=1;$j<sizeof($date_array);$j++){
				$current_date=$date_array[$j];
				$found=false;
				for($k=0;$k<sizeof($registered_measurements);$k++){
					$registered_measurements_parts=explode(",",$registered_measurements[$k]);
					if(($registered_measurements_parts[2]==$current_date) && ($registered_measurements_parts[1]==$all_measurements[$i])){
						array_push($measurement_row,$registered_measurements_parts[0]);
						$found=true;
						break;
					}
				}
				if(!$found){
					array_push($measurement_row,"");
				}
			}
			fputcsv($df, $measurement_row);
		}
	}
	
	fclose($df);
	header("Location: get_report.php?name=$filename");
	
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	$currentYear=date("Y");
	
	$query="SELECT DISTINCT measurement_id, measurement_name, measurement_category, measurement_subcategory FROM measurement ORDER BY measurement_category, measurement_subcategory, measurement_name";
	$result = mysqli_query($dbh,$query);
	$measurements_array=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$measurement=$row[0].','.$row[1].','.$row[2].','.$row[3];
		array_push($measurements_array,$measurement);
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
<script language=Javascript>
       <!--
		
		function toggleUser(){
			var cb = document.getElementById("toggle_user");
			var s = document.getElementById("user");
			s.disabled = !cb.checked;
		}
		
		function toggleDates(){
			var cb = document.getElementById("toggle_dates");
			var dd1 = document.getElementById("dd1");
			var mm1 = document.getElementById("mm1");
			var yy1 = document.getElementById("yy1");
			dd1.disabled = !cb.checked;
			mm1.disabled = !cb.checked;
			yy1.disabled = !cb.checked;
			dd2.disabled = !cb.checked;
			mm2.disabled = !cb.checked;
			yy2.disabled = !cb.checked;
		}
		
		function toggleActivity(){
			var cb = document.getElementById("toggle_activity");
			var s = document.getElementById("activity");
			s.disabled = !cb.checked;
		}
		
		function toggleMeasurement(){
			var cb = document.getElementById("toggle_measurement");
			var s = document.getElementById("measurement");
			s.disabled = !cb.checked;
		}
		
		function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
	   
	   function validateForm(){
		   
		   var newDate1;
		   var newDate2;
		   var today = new Date();
		   var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
			
			var day = parseInt(document.getElementById("dd1").value,10);
			var month = parseInt(document.getElementById("mm1").value,10);
			var year = parseInt(document.getElementById("yy1").value);
			
			if(year<2017 || year>parseInt(today.getFullYear())){
				alert("Date 1 out of valid range");
				return false;
			} else {
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;
				if(day > monthLength[month - 1]){
					alert("Invalid date 1");
					return false;
				} else {
					newDate1 = new Date(document.getElementById("mm1").value+"/"+document.getElementById("dd1").value+"/"+document.getElementById("yy1").value);
					if(newDate1 > today){
						alert("Date 1 must be in the past");
						return false;
					}
				}
			}
			
			day = parseInt(document.getElementById("dd2").value,10);
			month = parseInt(document.getElementById("mm2").value,10);
			year = parseInt(document.getElementById("yy2").value);
			
			if(year<2017 || year>parseInt(today.getFullYear())){
				alert("Date 2 out of valid range");
				return false;
			} else {
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;
				if(day > monthLength[month - 1]){
					alert("Invalid date 2");
					return false;
				} else {
					newDate2 = new Date(document.getElementById("mm2").value+"/"+document.getElementById("dd2").value+"/"+document.getElementById("yy2").value);
					if(newDate2 > today){
						alert("Date 2 must be in the past");
						return false;
					}
				}
			}
			
			if(newDate1 > newDate2){
				alert("Date 1 must be earlier than date 2");
				return false;
			}
		
	   }
       //-->
</script>
</head>
<body class="w3-small">
<div class="w3-container w3-card-4">
<h2 class="w3-green">Timeline report</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm()">
<p>
<label class="w3-text-green">Field</label>
<select class="w3-select w3-text-green" name="field" id="field">
<?php
$fields=getFields($dbh);
$field_aggregate="";
$prev_field="";
for($i=0;$i<sizeof($fields);$i++){
	$field=$fields[$i];
	if(($field[1]!=$prev_field) && $prev_field!=""){
		$selected = ($i==0) ? 'selected' : '';
		echo('<option value="'.$field_aggregate.'"'.$selected.'>'.$prev_field.' (ALL)</option>');
		$prev_field=$field[1];
		$field_aggregate="";
	}
	$prev_field=$field[1];
	if($field_aggregate==""){
		$field_aggregate=$field[0];
	} else {
		$field_aggregate.=",".$field[0];
	}
	$selected = ($i==0) ? 'selected' : '';
	echo('<option value="'.$field[0].'"'.$selected.'>'.$field[1].' R'.$field[2].'</option>');
}
if($field_aggregate!=""){
	$selected = ($field_aggregate==$_SESSION['field']) ? 'selected' : '';
	echo('<option value="'.$field_aggregate.'"'.$selected.'>'.$prev_field.' (ALL)</option>');
}
?>
</select>
</p>
<p>
<input class="w3-check" type="checkbox" id="toggle_dates" name="toggle_dates" onclick="toggleDates()"><label class="w3-text-green">Dates</label>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd1" id="dd1" disabled>
		<option value="" selected disabled>Day</option>
		<?php
		for($i=1;$i<=31;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			$selected = ($n==$dd1) ? 'selected' : '';
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="mm1" id="mm1" disabled>
		<option value="" selected disabled>Month</option>
		<?php
		for($i=1;$i<=12;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			$selected = ($n==$mm1) ? 'selected' : '';
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy1" id="yy1" value="<?php echo($currentYear); ?>" onkeypress="return isNumberKey(event)" disabled>
  </div>
</div>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd2" id="dd2" disabled>
		<option value="" selected disabled>Day</option>
		<?php
		for($i=1;$i<=31;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			$selected = ($n==$dd2) ? 'selected' : '';
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="mm2" id="mm2" disabled>
		<option value="" selected disabled>Month</option>
		<?php
		for($i=1;$i<=12;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			$selected = ($n==$mm2) ? 'selected' : '';
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy2" id="yy2" value="<?php echo($currentYear); ?>" onkeypress="return isNumberKey(event)" disabled>
  </div>
</div>
</p>
<p>
<input class="w3-check" type="checkbox" id="toggle_activity" name="toggle_activity" onclick="toggleActivity()"><label class="w3-text-green">Specify activity</label>
<select class="w3-select w3-text-green" name="activity[]" id="activity" disabled multiple>
<?php
$activities=getActivities($dbh);
for($i=0;$i<sizeof($activities);$i++){
	$activity=$activities[$i];
	echo('<option value="'.$activity[0].'">'.$activity[1].'</option>');
}
?>
</select>
</p>
<p>
<input class="w3-check" type="checkbox" id="toggle_measurement" name="toggle_measurement" onclick="toggleMeasurement()"><label class="w3-text-green">Specify measurement</label>
<select class="w3-select w3-text-green" name="measurement[]" id="measurement" disabled multiple>
<?php
	$last_category="";
	$last_subcategory="";
	for($i=0;$i<sizeof($measurements_array);$i++){
		$measurement=explode(",",$measurements_array[$i]);
		$id=$measurement[0];
		$name=$measurement[1];
		$category=$measurement[2];
		$subcategory=$measurement[3];
		if($category!=$last_category){
			$last_category=$category;
			$last_subcategory="";
			echo('<option class="w3-green w3-text-white" value="" disabled>---- Category: '.$category.' ----</option>');
		}
		if($subcategory!=$last_subcategory){
			$last_subcategory=$subcategory;
			echo('<option class="w3-green w3-text-white" value="" disabled>'.$subcategory.'</option>');
		}
		echo('<option value="'.$id.'">'.$name.'</option>');
	}
?>
</select>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="generate" name="generate">Generate</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</form>
</div>
</body>
</html>
<?php
}
?>