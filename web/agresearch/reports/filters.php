<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['apply'])){
	$filter_reminder="";
	if(isset($_POST['toggle_field'])){
		if(substr_count($_POST['field'],',')>0){
			$_SESSION['report_log_field_filter']=" AND log.field_id IN(".$_POST['field'].") ";
		} else {
			$_SESSION['report_log_field_filter']=" AND log.field_id=".$_POST['field']." ";
		}
		$_SESSION['report_field']=$_POST['field'];
		$filter_reminder="Location";
	} else {
		$_SESSION['report_log_field_filter']=" ";
		$_SESSION['report_field']=-1;
	}
	
	if(isset($_POST['toggle_dates'])){
		$dd1=$_POST['dd1'];
		$mm1=$_POST['mm1'];
		$yy1=$_POST['yy1'];
		$dd2=$_POST['dd2'];
		$mm2=$_POST['mm2'];
		$yy2=$_POST['yy2'];
		if(checkdate($mm1,$dd1,$yy1) && checkdate($mm2,$dd2,$yy2)){
			if($filter_reminder==""){
				$filter_reminder="Date";
			} else {
				$filter_reminder.=", Date";
			}
			$date1 = strtotime($yy1."-".$mm1."-".$dd1);
			$date2 = strtotime($yy2."-".$mm2."-".$dd2);
			if($date1>$date2){
				$_SESSION['report_log_date_filter']=" AND (log.log_date BETWEEN '".$yy2."-".$mm2."-".$dd2."' AND '".$yy1."-".$mm1."-".$dd1."') ";
				$_SESSION['report_date1']=$yy2."-".$mm2."-".$dd2;
				$_SESSION['report_date2']=$yy1."-".$mm1."-".$dd1;
			} else if($date1<$date2) {
				$_SESSION['report_log_date_filter']=" AND (log.log_date BETWEEN '".$yy1."-".$mm1."-".$dd1."' AND '".$yy2."-".$mm2."-".$dd2."') ";
				$_SESSION['report_date1']=$yy1."-".$mm1."-".$dd1;
				$_SESSION['report_date2']=$yy2."-".$mm2."-".$dd2;
			} else {
				$_SESSION['report_log_date_filter']=" AND log.log_date = '".$yy1."-".$mm1."-".$dd1."' ";
				$_SESSION['report_date1']=$yy1."-".$mm1."-".$dd1;
				$_SESSION['report_date2']=$yy1."-".$mm1."-".$dd1;
			}
		} else {
			$_SESSION['report_log_date_filter']=" ";
			$_SESSION['report_date1']="";
			$_SESSION['report_date2']="";
		}
	} else {
		$_SESSION['report_log_date_filter']=" ";
		$_SESSION['report_date1']="";
		$_SESSION['report_date2']="";
	}
	
	if(isset($_POST['toggle_measurement'])){
		$_SESSION['report_log_measurement_filter']=$_POST['measurement'];
		$_SESSION['report_measurement_category_filter']=$_POST['measurement_category']+1;
		if($filter_reminder==""){
			$filter_reminder="Parameter";
		} else {
			$filter_reminder.=", Parameter";
		}
	} else {
		unset($_SESSION['report_log_measurement_filter']);
		unset($_SESSION['report_measurement_category_filter']);
	}
	
	$_SESSION['report_max_messages']=$_POST['max_messages'];
	$_SESSION['report_reset']=true;
	$_SESSION['report_filter_reminder']=$filter_reminder;
	
	echo "<script type='text/javascript'>";
	echo "window.opener.location='measurement_report.php?from=0&selected=';";
	echo "window.close();";
	echo "</script>";
	
} else if(isset($_POST['remove'])){
	
	$_SESSION['report_log_field_filter']=" ";
	$_SESSION['report_field']=-1;
	
	$_SESSION['report_log_date_filter']=" ";
	$_SESSION['report_date1']="";
	$_SESSION['report_date2']="";
	
	unset($_SESSION['report_log_measurement_filter']);
	unset($_SESSION['report_measurement_category_filter']);
	
	$_SESSION['report_max_messages']=$max_log_items_per_page;
	$_SESSION['report_reset']=true;
	$_SESSION['report_filter_reminder']="";
	
	echo "<script type='text/javascript'>";
	echo "window.opener.location='measurement_report.php?from=0&selected=';";
	echo "window.close();";
	echo "</script>";
	
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	$currentYear=date("Y");
	
	if(!isset($_SESSION['report_max_messages'])){
		$max_messages=$max_log_items_per_page;
	} else {
		$max_messages=$_SESSION['report_max_messages'];
	}
	
	if($_SESSION['report_date1']!="" && $_SESSION['report_date2']!=""){
		$date1_parts=explode("-",$_SESSION['report_date1']);
		$yy1=$date1_parts[0];
		$mm1=$date1_parts[1];
		$dd1=$date1_parts[2];
		$date2_parts=explode("-",$_SESSION['report_date2']);
		$yy2=$date2_parts[0];
		$mm2=$date2_parts[1];
		$dd2=$date2_parts[2];
	} else {
		$yy1="";
		$mm1="";
		$dd1="";
		$yy2="";
		$mm2="";
		$dd2="";
	}
	
	$query="SELECT measurement_id, measurement_name, measurement_category, measurement_subcategory FROM measurement ORDER BY measurement_category, measurement_subcategory, measurement_name";
	$result = mysqli_query($dbh,$query);
	$cats="";
	$cats_php="";
	$measurements="";
	$measurements_array=array();
	$last_cat="";
	$default_list="";
	$n=-1;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		if($row[2]!=$last_cat){
			$last_cat=$row[2];
			if($cats==""){
				$cats='"'.$row[2].'"';
				$cats_php=$row[2];
			} else {
				$cats.=',"'.$row[2].'"';
				$cats_php.=",".$row[2];
			}
			if($measurements!=""){
				array_push($measurements_array,$measurements);
				$measurements="";
			}
			$n++;
		}
		if($measurements==""){
			$measurements=$row[0].',"'.$row[1].'","'.$row[3].'"';
		} else {
			$measurements.=','.$row[0].',"'.$row[1].'","'.$row[3].'"';
		}
		if(($n+1)==$_SESSION['report_measurement_category_filter']){
			if($default_list==""){
				$default_list="-1,ALL,,".$row[0].",".$row[1].",".$row[3];
			} else {
				$default_list.=",".$row[0].",".$row[1].",".$row[3];
			}
		}
	}
	if($measurements!=""){
		array_push($measurements_array,$measurements);
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
	   <?php
		echo("var categories = [".$cats."];\n");
		echo("var measurements = [");
		for($i=0;$i<sizeof($measurements_array);$i++){
			if($i==0){	
				echo("[".$measurements_array[$i]."]");
			} else {
				echo(",[".$measurements_array[$i]."]");
			}
		}
		echo("]\n");
		?>
	
		function updateMeasurementDropdown(){
			var m = document.getElementById("measurement");
			while(m.firstChild){
				m.removeChild(m.firstChild);
			}
			m.disabled=false;
		
			var c = document.getElementById("measurement_category");
			var cId = c.options[c.selectedIndex].value;
		
			var list = measurements[cId];
		
			var last_category="";
			
			var o = document.createElement("option");
			o.text = "ALL";
			o.value = "-1";
			o.selected = true;
			m.add(o);
			
			for(var i=0;i<list.length;i+=3){
				var id = list[i];
				var name = list[i+1];
				var category = list[i+2];
				if(category!=last_category){
					var o = document.createElement("option");
					o.text = category;
					o.value = "";
					o.className = "w3-green w3-text-white";
					o.disabled = true;
					m.add(o);
					last_category=category;
				}
				var o = document.createElement("option");
				o.text = name;
				o.value = id;
				m.add(o);
			}
		}
	   
		function toggleField(){
			var cb = document.getElementById("toggle_field");
			var s = document.getElementById("field");
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
		
		function toggleMeasurement(){
			var cb = document.getElementById("toggle_measurement");
			var s = document.getElementById("measurement_category");
			var ss = document.getElementById("measurement");
			s.disabled = !cb.checked;
			ss.disabled = !cb.checked;
			if(cb.checked){
				document.getElementById("toggle_activity").checked=false;
				toggleActivity();
			}
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
<h2 class="w3-green">Filters</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm()">
<p>
<input class="w3-check" type="checkbox" id="toggle_field" name="toggle_field" onclick="toggleField()" <?php echo(($_SESSION['report_field']>0) ? 'checked' : ''); ?>><label class="w3-text-green">Location</label>
<select class="w3-select w3-text-green" name="field" id="field" <?php echo(($_SESSION['report_field']>0) ? '' : 'disabled'); ?>>
<?php
$fields=getFieldsAllVersions($dbh);
$field_aggregate="";
$prev_field="";
for($i=0;$i<sizeof($fields);$i++){
	$field_row=$fields[$i];
	$field_parts=explode(";",$field_row);
	$field_name=$field_parts[0];
	if(($field_name!=$prev_field) && $prev_field!=""){
		$selected = ($field_aggregate==$_SESSION['report_field']) ? 'selected' : '';
		echo('<option value="'.$field_aggregate.'"'.$selected.'>'.$prev_field.'</option>');
		$prev_field=$field_name;
		$field_aggregate="";
	}
	$prev_field=$field_name;
	if($field_aggregate==""){
		$field_aggregate=$field_parts[2];
	} else {
		$field_aggregate.=",".$field_parts[2];
	}
	$selected = ($field_parts[2]==$_SESSION['report_field']) ? 'selected' : '';
	//echo('<option value="'.$field_parts[2].'"'.$selected.'>'.$field_name.$field_parts[1].'</option>');
}
if($field_aggregate!=""){
	$selected = ($field_aggregate==$_SESSION['report_field']) ? 'selected' : '';
	echo('<option value="'.$field_aggregate.'"'.$selected.'>'.$prev_field.'</option>');
}
?>
</select>
</p>
<p>
<input class="w3-check" type="checkbox" id="toggle_dates" name="toggle_dates" onclick="toggleDates()" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? 'checked' : ''); ?>><label class="w3-text-green">Dates</label>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd1" id="dd1" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? '' : 'disabled'); ?>>
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
    <select class="w3-select w3-text-green" name="mm1" id="mm1" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? '' : 'disabled'); ?>>
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
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy1" id="yy1" value="<?php echo(($yy1=="") ? $currentYear : $yy1); ?>" onkeypress="return isNumberKey(event)" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? '' : 'disabled'); ?>>
  </div>
</div>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd2" id="dd2" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? '' : 'disabled'); ?>>
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
    <select class="w3-select w3-text-green" name="mm2" id="mm2" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? '' : 'disabled'); ?>>
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
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yy2" id="yy2" value="<?php echo(($yy2=="") ? $currentYear : $yy2); ?>" onkeypress="return isNumberKey(event)" <?php echo(($_SESSION['report_date1']!="" && $_SESSION['report_date2']!="") ? '' : 'disabled'); ?>>
  </div>
</div>
</p>
<p>
<input class="w3-check" type="checkbox" id="toggle_measurement" name="toggle_measurement" onclick="toggleMeasurement()" <?php echo(($_SESSION['report_log_measurement_filter']>0 || $_SESSION['report_log_measurement_filter']==-1) ? 'checked' : ''); ?>><label class="w3-text-green">Measurement</label>
<div class="w3-row-padding">
<div class="w3-half">
<select class="w3-select w3-text-green" name="measurement_category" id="measurement_category" onChange="updateMeasurementDropdown();" <?php echo(($_SESSION['report_log_measurement_filter']>0 || $_SESSION['report_log_measurement_filter']==-1) ? '' : 'disabled'); ?>>
<option value="" disabled selected>Choose category:</option>
<?php
$cats_array=explode(",",$cats_php);
for($i=0;$i<sizeof($cats_array);$i++){
	$selected = ($_SESSION['report_measurement_category_filter']==($i+1)) ? 'selected' : '';
	echo('<option value="'.$i.'" '.$selected.'>'.$cats_array[$i].'</option>');
}
?>
</select>
</div>
<div class="w3-half">
<select class="w3-select w3-text-green" name="measurement" id="measurement" <?php echo(($_SESSION['report_log_measurement_filter']>0 || $_SESSION['report_log_measurement_filter']==-1) ? '' : 'disabled'); ?>>
<option value="" disabled selected>Choose measurement:</option>
<?php
if(isset($_SESSION['report_log_measurement_filter'])){
	$default_array=explode(",",$default_list);
	$last_category="";
	for($i=0;$i<sizeof($default_array);$i+=3){
		$id=$default_array[$i];
		$name=$default_array[$i+1];
		$category=$default_array[$i+2];
		if($category!=$last_category){
			$last_category=$category;
			echo('<option class="w3-green w3-text-white" value="" disabled>'.$category.'</option>');
		}
		if($_SESSION['report_log_measurement_filter']==$id){
			$selected="selected";
		} else {
			$selected="";
		}
		echo('<option value="'.$id.'" '.$selected.'>'.$name.'</option>');
	}
}
?>
</select>
</div>
</div>
</p>
<p>
<label class="w3-text-green">Items per page</label><input class="w3-input w3-border-teal w3-text-green" type="text" name="max_messages" id="max_messages" value="<?php echo($max_messages); ?>" onkeypress="return isNumberKey(event)">
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="apply" name="apply">Apply</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="remove" name="remove">Remove all</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</form>
</div>
</body>
</html>
<?php
}
?>