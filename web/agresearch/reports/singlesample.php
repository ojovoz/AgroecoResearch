<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['generate'])){
	$fields=explode(",",$_POST['field']);
	
	$date_in_label = (isset($_POST['date_in_label']));
		
	if(isset($_POST['dd1']) && isset($_POST['mm1']) && isset($_POST['yy1']) && isset($_POST['dd2']) && isset($_POST['mm2']) && isset($_POST['yy2'])){
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
				$date_title="from $dd2/$mm2/$yy2 to $dd1/$mm1/$yy1";
				$multiple_dates=true;
			} else if($date1<$date2) {
				$log_date_filter=" AND (log.log_date BETWEEN '".$yy1."-".$mm1."-".$dd1."' AND '".$yy2."-".$mm2."-".$dd2."') ";
				$date_title="from $dd1/$mm1/$yy1 to $dd2/$mm2/$yy2";
				$multiple_dates=true;
			} else {
				$log_date_filter=" AND log.log_date = '".$yy1."-".$mm1."-".$dd1."' ";
				$date_title="$dd1/$mm1/$yy1";
				$multiple_dates=false;
			}
		} else {
			$log_date_filter=" ";
			$date_title="undefined";
		}
	} else {
		$log_date_filter=" ";
		$date_title="undefined";
	}
	
	if(isset($_POST['measurement'])){
		$measurement=$_POST['measurement'];
	} else {
		$measurement=-1;
	}
	
	//generate report
	$filename="singlesample".date("Y-m-d").".csv";
	
	$files = glob('generated/*'); 
	foreach($files as $file){ 
		if(is_file($file))
			unlink($file);
	}
	$df = fopen("generated/".$filename, 'w');
	
	//title
	$fname=getFieldNameFromId($dbh,$_POST['field']);
	$title=array("Location: ".$fname);
	fputcsv($df, $title);
	$title=array("Period: ".$date_title);
	fputcsv($df, $title);
	$mname=getMeasurementNameFromIdWithUnits($dbh,$measurement);
	$title=array("Parameter: ".$mname);
	fputcsv($df, $title);
	$title=array(" ");
	fputcsv($df, $title);
	
	//header
	$plot_name_row=array();
	$date_row=array();
	$data_block=array();
	
	//results
	$result_block=array();
	$mean_row=array("Mean");
	$std_dev_row=array("Standard deviation");
	$std_dev_values=array();
	
	$found_plots_dates=array();
	$data_row=array();
	
	for($i=0;$i<sizeof($fields);$i++){
		$query="SELECT DISTINCT plots, log_value_number, log_date FROM log WHERE measurement_id=$measurement AND field_id=".$fields[$i]." ".$log_date_filter." ORDER BY plots, log_date";
		$result = mysqli_query($dbh,$query);
		
		$target_order=array("Control","PSL","SL","PS","PL","S","L","P");
		$db_result_unordered=array();
		$db_result_ordered=array();
		$found_dates=array();
		while($row=mysqli_fetch_array($result,MYSQL_NUM)){
			$label=calculatePlotLabelsWithoutCrop($dbh,$fields[$i],$row[0]);
			array_push($db_result_unordered,array($label,$row[1],$row[2]));
			if(!in_array($row[2],$found_dates)){
				array_push($found_dates,$row[2]);
			}
		}
		
		for($j=0;$j<sizeof($target_order);$j++){
			$found=false;
			for($k=0;$k<sizeof($db_result_unordered);$k++){
				if($db_result_unordered[$k][0]==$target_order[$j]){
					array_push($db_result_ordered,$db_result_unordered[$k]);
					$found=true;
				}
			}
			if(!$found){
				for($k=0;$k<sizeof($found_dates);$k++){
					array_push($db_result_ordered,array($target_order[$j]," ",$found_dates[$k]));
				}
			}
		}
		
		array_push($data_row,array());
		
		for($j=0;$j<sizeof($db_result_ordered);$j++){
			
			$row=$db_result_ordered[$j];
			$label=$row[0];
			
			if($multiple_dates && $date_in_label){
				$label=$label." ".$row[2];
			}
			$sample=$row[1];
			if(!in_array($label,$found_plots_dates)){
				array_push($found_plots_dates,$label);
			}
			$index=array_search($label,$found_plots_dates);
			$x=sizeof($data_row[$i]);
			if($index==$x){
				array_push($data_row[$i],$sample);
			} else if($index>$x){
				for($j=0;$j<($index-$x);$j++){
					array_push($data_row[$i],"*");
				}
				array_push($data_row[$i],$sample);
			} else {
				$data_row[$i][$index]=$sample;
			}		
		}
		array_unshift($data_row[$i],getFieldNameFromId($dbh,$fields[$i]));
		array_push($data_block,$data_row[$i]);
	}
		
	for($i=0;$i<sizeof($found_plots_dates);$i++){
		if($multiple_dates && $date_in_label){
			if($i==0){
				array_push($plot_name_row," ");
				array_push($date_row,"Field");
			}
			$label_parts=explode(" ",$found_plots_dates[$i]);
			array_push($plot_name_row,$label_parts[0]);
			array_push($date_row,$label_parts[1]);
		} else {
			if($i==0){
				array_push($plot_name_row,"Field");
			}
			array_push($plot_name_row,$found_plots_dates[$i]);
		}
	}

	if($multiple_dates && $date_in_label){
		fputcsv($df,$plot_name_row);
		fputcsv($df,$date_row);
	} else {
		fputcsv($df, $plot_name_row);
	}
	
	for($i=0;$i<sizeof($data_block);$i++){
		$row=$data_block[$i];
		fputcsv($df, $row);
	}
	
	for($i=1;$i<sizeof($plot_name_row);$i++){
		$mean_sum=0;
		$std_dev_values=array();
		$divisor=sizeof($data_block);
		for($j=0;$j<sizeof($data_block);$j++){
			if($data_block[$j][$i]==" " || $data_block[$j][$i]==""){
				$divisor--;
			} else {
				$mean_sum+=$data_block[$j][$i];
				array_push($std_dev_values,$data_block[$j][$i]);
			}
		}
		if($divisor>0){
			$mean=$mean_sum/$divisor;
			array_push($mean_row,$mean);
			array_push($std_dev_row,my_standard_deviation($std_dev_values));
		} else {
			array_push($mean_row,0);
			array_push($std_dev_row,0);
		}
		
	}
	array_push($result_block,$mean_row);
	array_push($result_block,$std_dev_row);
	
	fputcsv($df,array(" "));
	
	for($i=0;$i<sizeof($result_block);$i++){
		$row=$result_block[$i];
		fputcsv($df,$row);
	}
	
	fclose($df);
	header("Location: get_report.php?name=$filename");
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	$currentYear=date("Y");
	
	$query="SELECT DISTINCT measurement_id, measurement_name, measurement_category, measurement_subcategory FROM measurement WHERE measurement_has_sample_number=0 AND measurement_type<>2 ORDER BY measurement_category, measurement_subcategory, measurement_name";
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
		   var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
			
			var day = parseInt(document.getElementById("dd1").value,10);
			var month = parseInt(document.getElementById("mm1").value,10);
			var year = parseInt(document.getElementById("yy1").value);
			
			if(year<2017 || year>parseInt(today.getFullYear())){
				alert("Date out of valid range");
				return false;
			} else {
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;
				if(day > monthLength[month - 1]){
					alert("Invalid date 1");
					return false;
				} else {
					if(document.getElementById("mm1").value=="" || document.getElementById("dd1").value==""){
						alert("Day or month not specified");
						return false
					} else {
						newDate1 = new Date(document.getElementById("mm1").value+"/"+document.getElementById("dd1").value+"/"+document.getElementById("yy1").value);
						if(newDate1 > today){
							alert("Date must be in the past");
							return false;
						}
					}
				}
			}
			
			day = parseInt(document.getElementById("dd2").value,10);
			month = parseInt(document.getElementById("mm2").value,10);
			year = parseInt(document.getElementById("yy2").value);
			
			if(year<2017 || year>parseInt(today.getFullYear())){
				alert("Date out of valid range");
				return false;
			} else {
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;
				if(day > monthLength[month - 1]){
					alert("Invalid date 1");
					return false;
				} else {
					if(document.getElementById("mm2").value=="" || document.getElementById("dd2").value==""){
						alert("Day or month not specified");
						return false
					} else {
						newDate2 = new Date(document.getElementById("mm2").value+"/"+document.getElementById("dd2").value+"/"+document.getElementById("yy2").value);
						if(newDate2 > today){
							alert("Date must be in the past");
							return false;
						}
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
<h2 class="w3-green">Plot-based measurement report</h2>
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
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd1" id="dd1">
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
    <select class="w3-select w3-text-green" name="mm1" id="mm1">
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
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy1" id="yy1" value="<?php echo($currentYear); ?>" onkeypress="return isNumberKey(event)">
  </div>
</div>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd2" id="dd2">
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
    <select class="w3-select w3-text-green" name="mm2" id="mm2">
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
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy2" id="yy2" value="<?php echo($currentYear); ?>" onkeypress="return isNumberKey(event)">
  </div>
</div>
</p>
<p>
<label class="w3-text-green">Measurement</label>
<select class="w3-select w3-text-green" name="measurement" id="measurement">
<?php
	$last_category="";
	for($i=0;$i<sizeof($measurements_array);$i++){
		$measurement=explode(",",$measurements_array[$i]);
		$id=$measurement[0];
		$name=$measurement[1];
		$category=$measurement[2];
		if($category!=$last_category){
			$last_category=$category;
			echo('<option class="w3-green w3-text-white" value="" disabled>---- Category: '.$category.' ----</option>');
		}
		echo('<option value="'.$id.'">'.$name.'</option>');
	}
?>
</select>
</p>
<p>
<input class="w3-check" type="checkbox" id="date_in_label" name="date_in_label" checked><label class="w3-text-green">Include date in labels</label>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="generate" name="generate">Generate</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</form>
</div>
</body>
</html>
<?php
}
?>