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
		
	$dd1='01';
	$mm1=$_POST['mm'];
	$yy1=$_POST['yy'];
	
	$monthLength = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if($yy1 % 400 == 0 || ($yy1 % 100 != 0 && $yy1 % 4 == 0)){
		$monthLength[1] = 29;
	}
	
	$dd2=$monthLength[(intval($mm1)-1)];
	$mm2=$mm1;
	$yy2=$yy1;
	
	$date1 = strtotime($yy1."-".$mm1."-".$dd1);
	$date2 = strtotime($yy2."-".$mm2."-".$dd2);
	$log_date_filter=" AND (log.log_date BETWEEN '".$yy1."-".$mm1."-".$dd1."' AND '".$yy2."-".$mm2."-".$dd2."') ";
	
	//generate report
	
	$query_activities="SELECT activity_name FROM activity ORDER BY activity_category, activity_name";
	$result = mysqli_query($dbh,$query_activities);
	$all_activities=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		array_push($all_activities,$row[0]);
	}
	
	$query_measurements="SELECT measurement_name, measurement_category, measurement_subcategory FROM measurement ORDER BY measurement_category, measurement_subcategory, measurement_name";
	$result = mysqli_query($dbh,$query_measurements);
	$all_measurements=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$measurement_string=strval($row[0])." (".strval($row[1])." - ".strval($row[2]).")";
		array_push($all_measurements,$measurement_string);
	}
	
	$query1="SELECT DISTINCT user.user_name, activity.activity_name, log.log_date FROM log,user,activity WHERE user.user_id=log.user_id ".$log_field_filter.$log_date_filter." AND activity.activity_id=log.activity_id ORDER BY log.log_date,activity.activity_name";
	$result = mysqli_query($dbh,$query1);
	$registered_activities=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		array_push($registered_activities,getUserInitials($row[0]).",".$row[1].",".$row[2]);
	}
	
	$query2="SELECT DISTINCT user.user_name, measurement.measurement_name, measurement.measurement_category, measurement.measurement_subcategory, log.log_date FROM log,user,measurement WHERE user.user_id=log.user_id ".$log_field_filter.$log_date_filter." AND measurement.measurement_id=log.measurement_id ORDER BY log.log_date,measurement.measurement_name";
	$result = mysqli_query($dbh,$query2);
	$registered_measurements=array();
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$measurement_string=strval($row[1])." (".strval($row[2])." - ".strval($row[3]).")";
		array_push($registered_measurements,getUserInitials($row[0]).",".$measurement_string.",".$row[4]);
	}
	
	$filename="monthly".date("Y-m",$date1).".csv";
	
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
	$title=array("Month: ".date("Y/m",$date1));
	fputcsv($df, $title);
	
	//row header
	$current_date=$date1;
	$date_array=array();
	$date_array_display=array();
	array_push($date_array,"Day");
	array_push($date_array_display,"Day");
	while($date2>=$current_date){
		array_push($date_array,date('Y-m-d',date($current_date)));
		array_push($date_array_display,date('d',date($current_date)));
		$current_date=strtotime(date('Y-m-d',$current_date).' + 1 day');
	}
	fputcsv($df, $date_array_display);
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
	
	fclose($df);
	header("Location: get_report.php?name=$filename");
	
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	$currentYear=date("Y");
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
<script language=Javascript>
       <!--
		
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
		   var today = new Date();
			
			var day = 1;
			var month = parseInt(document.getElementById("mm").value,10);
			var year = parseInt(document.getElementById("yy").value);
			
			if(year<2017 || year>parseInt(today.getFullYear()) || isNaN(month)){
				alert("Date out of valid range");
				return false;
			} else {
				newDate = new Date(document.getElementById("mm").value+"/01/"+document.getElementById("yy").value);
				if(newDate > today){
					alert("Month must be in the past");
					return false;
				}
			}
		
	   }
	   
       //-->
</script>
</head>
<body class="w3-small">
<div class="w3-container w3-card-4">
<h2 class="w3-green">Monthly report</h2>
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
<label class="w3-text-green">Dates</label>
<div class="w3-row-padding">
  <div class="w3-half">
    <select class="w3-select w3-text-green" name="mm" id="mm">
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
  <div class="w3-half">
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy" id="yy" value="<?php echo($currentYear); ?>" onkeypress="return isNumberKey(event)">
  </div>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="generate" name="generate">Generate</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</form>
</div>
</body>
</html>
<?php
}
?>