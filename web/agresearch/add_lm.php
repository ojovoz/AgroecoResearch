<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['add'])){
	
	$id=$_POST['id'];
	$field=$_POST['field'];
	$plots=$_POST['plots'];
	$user=$_SESSION['user_id'];
	
	$type=$_POST['type'];
	$has_samples=$_POST['has_samples'];
	$dd=$_POST['dd'];
	$mm=$_POST['mm'];
	$yyyy=$_POST['yyyy'];
	$date=$yyyy."-".$mm."-".$dd;
	$comments=$_POST['comments'];
	if(isset($_FILES['log_picture']['name'])){
		$image_file=$_FILES['log_picture']['name'];
		$upload = "images/".$image_file;
		if(is_uploaded_file($_FILES['log_picture']['tmp_name'])) {
			move_uploaded_file($_FILES['log_picture']['tmp_name'],$upload);
			$picture=$upload;
		} else {
			$picture="";
		}
	} else {
		$picture="";
	}
	
	if($type=="0" || $has_samples=="1"){
		if($has_samples=="0"){
			$value=$_POST['value'];
		} else {
			$value=$_SESSION['values'];
			$units=$_POST['units_original'];
		}
		$query="INSERT INTO log (measurement_id, user_id, field_id, plots, log_date, log_value_text, log_value_units, log_comments, log_picture) VALUES ($id,$user,$field,'$plots','$date','$value','$units','$comments','$picture')";
	} else if($type=="1") {
		$value=floatval($_POST['value']);
		$units=$_POST['units'];
		$query="INSERT INTO log (measurement_id, user_id, field_id, plots, log_date, log_value_number, log_value_units, log_comments, log_picture) VALUES ($id,$user,$field,'$plots','$date',$value,'$units','$comments','$picture')";
	} else if($type=="2"){
		
	}
	
	$result = mysqli_query($dbh,$query);
	echo "<script type='text/javascript'>";
	echo "window.opener.location.reload(false);";
	echo "window.close();";
	echo "</script>";
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	
	$id=$_GET['id'];
	$field=$_GET['field'];
	
	$values=$_SESSION['values'];
	
	$query="SELECT measurement_name, measurement_category, measurement_type, measurement_has_sample_number, measurement_categories, measurement_units FROM measurement WHERE measurement_id=$id";
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	$measurement_name = $row[0];
	$measurement_category = $row[1];
	$measurement_type = $row[2];
	$measurement_has_samples = $row[3];
	$measurement_categories = $row[4];
	$measurement_units = $row[5];

	$yy=date('Y');
	$mm=date('m');
	$dd=date('d');
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script language="Javascript">
       <!--
       function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
	   
	   function showPopup(url,width,height) {
			newwindow=window.open(url,'newname','height=' + height +',width=' + width + ',top=50,left=50,screenX=50,screenY=50');
			if (window.focus) {newwindow.focus()}
		}
		
		function updateSamples(values) {
			var samples = document.getElementById("samples");
			samples.innerHTML=values;
		}
		
		function updateHealthReport(values) {
			var health = document.getElementById("health");
			health.innerHTML=values;
		}
	   
	   function addPlot(){
		var e = document.getElementById("a_plots");
		var plot_id = e.options[e.selectedIndex].value;
		if(plot_id!=""){
			var plot_label = e.options[e.selectedIndex].text;
			var p = document.getElementById("plots");
			var pl = document.getElementById("plot_labels");
			if(p.value==""){
				p.value=plot_id;
				pl.value=plot_label;
			} else {
				p.value+=","+plot_id;
				pl.value+=", "+plot_label;
			}
			var ip = document.getElementById("included_plots");
			ip.innerHTML=formatPlotsHTML(pl.value,p.value);
		
			e.remove(e.selectedIndex);
			e.options[0].text="Plot added";
			e.disabled=true;
		}
	   }
	   
	   function formatPlotsHTML(plots,ids){
		   var plot_labels_list = plots.split(",");
		   var plot_ids_list = ids.split(",");
		   var stringHTML="";
		   for(var i=0;i<plot_ids_list.length;i++){
			   if(stringHTML==""){
				   stringHTML='<a href="javascript:removePlot(\''+ plot_labels_list[i].trim() +'\','+ plot_ids_list[i] +')">'+ plot_labels_list[i].trim() +'</a>';
			   } else {
				   stringHTML+=', <a href="javascript:removePlot(\''+ plot_labels_list[i].trim() +'\','+ plot_ids_list[i] +')">'+ plot_labels_list[i].trim() +'</a>';
			   }
		   }
		   return stringHTML;
	   }
	   
	   function removePlot(plot,id){
		    
		    var e = document.getElementById("a_plots");
			var o = document.createElement("option");
			o.text = plot;
			o.value = id;
			e.add(o);
			e.options[0].text="Add a plot:";
			e.disabled=false;
			
			var p = document.getElementById("plots");
			var pl = document.getElementById("plot_labels");
			var plot_list = p.value.split(",");
			var plot_labels_list = pl.value.split(",");
			var string_ids="";
			var string_labels="";
			for (var i=0; i<plot_list.length; i++){
				if(plot_list[i]==id){
					
				} else {
					if(string_ids==""){
						string_ids=plot_list[i];
						string_labels=plot_labels_list[i];
					} else {
						string_ids+=","+plot_list[i];
						string_labels+=", "+plot_labels_list[i];
					}
				}
			}
			p.value=string_ids;
			pl.value=string_labels;
			var ip = document.getElementById("included_plots");
			ip.innerHTML=formatPlotsHTML(pl.value,p.value);
	   }
	   
	   function validateForm(type,has_samples){
		  
			var p = document.getElementById("plots");
			if(p.value==""){
				alert("You must choose one plot");
				return false;
			}
			
			var day = parseInt(document.getElementById("dd").value,10);
			var month = parseInt(document.getElementById("mm").value,10);
			var year = parseInt(document.getElementById("yyyy").value);
			var today = new Date();
			if(year<2017 || year>parseInt(today.getFullYear())){
				alert("Date out of valid range");
				return false;
			} else {
				var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;
				if(day > monthLength[month - 1]){
					alert("Invalid date");
					return false;
				} else {
					var newDate = new Date(document.getElementById("mm").value+"/"+document.getElementById("dd").value+"/"+document.getElementById("yyyy").value);
					if(newDate > today){
						alert("Date must be in the past");
						return false;
					}
				}
			}
			
			if(type==1 && has_samples==0){
			
				var u = document.getElementById("units");
				if(u.value==""){
					alert("Units not specified");
					return false;
				}
			
				var v = document.getElementById("value");
				if(v.value==""){
					alert("Value must not be empty");
					return false;
				}
			} else if(has_samples==1){
				if(type!=2){
					var s = document.getElementById("samples");
					if(s.innerHTML=="" || s.innerHTML==":"){
						alert("You must add at least one sample");
						return false;
					}
				} else {
					var h = document.getElementById("health");
					if(h.innerHTML==""){
						alert("You must add at least one sample");
						return false;
					}
				}
			}
		
	   }
       //-->
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Add measurement</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm(<?php echo($measurement_type.",".$measurement_has_samples); ?>)">
<input name="id" type="hidden" id="id" value="<?php echo($id); ?>">
<input name="type" type="hidden" id="type" value="<?php echo($measurement_type); ?>">
<input name="has_samples" type="hidden" id="has_samples" value="<?php echo($measurement_has_samples); ?>">
<input name="units_original" type="hidden" id="units_original" value="<?php echo($measurement_units); ?>">
<input name="field" type="hidden" id="field" value="<?php echo($field); ?>">
<input name="plots" type="hidden" id="plots" value="">
<input name="plot_labels" type="hidden" id="plot_labels" value="">
<p><div class="w3-text-green">
<b>Registered by:</b> <?php echo(getUserNameFromId($dbh,$row[15])); ?><br>
<b>Field:</b> <?php echo(getFieldNameFromId($dbh,$field)); ?><br>
<b>Plot (click on plot name to remove):</b> <span id="included_plots"></span><br>
<?php
$plots=getRemainingPlots($dbh,$field,array(),($id*-1),"lm");
if($plots!=""){
	$plot_list=explode(",",$plots);
	$plot_labels=calculatePlotLabels($dbh,$field,$plots);
	$plot_labels_list=explode(",",$plot_labels);
?>
<span id="available_plots">
<select class="w3-select w3-text-green" name="a_plots" id="a_plots" onchange="addPlot();">
  <option value="" selected>Add a plot:</option>
<?php

for($i=0;$i<sizeof($plot_list);$i++){
	echo('<option value="'.$plot_list[$i].'">'.$plot_labels_list[$i].'</option>');
}
?>
</select>
</span><br>
<?php }
?><br>
<b>Measurement:</b> <?php echo($measurement_name." (".$measurement_category.")"); ?><br><br>
<b>Date:</b>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd" id="dd">
		<option value="" disabled>Day</option>
		<?php
		for($i=1;$i<=31;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			if($n==$dd){
				$selected=" selected";
			} else {
				$selected="";
			}
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="mm" id="mm">
		<option value="" disabled>Month</option>
		<?php
		for($i=1;$i<=12;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			if($n==$mm){
				$selected=" selected";
			} else {
				$selected="";
			}
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yyyy" id="yyyy" value="<?php echo($yy); ?>" onkeypress="return isNumberKey(event)">
  </div>
</div>
<?php 
if($measurement_has_samples==0){
	if($measurement_type==0){ ?>
<b>Value:</b>
<select class="w3-select w3-text-green" name="value" id="value">
<?php
		$categories=explode(",",$measurement_categories);
		for($i=0;$i<sizeof($categories);$i++){
			echo('<option value="'.$categories[$i].'">'.$categories[$i].'</option>');
		}
?>
</select>
<?php	
	} else {
?>
<b>Units:</b> <input class="w3-input w3-border-green w3-text-green" name="units" id="units" type="text" value="<?php echo($measurement_units); ?>">
<b>Value:</b> <input class="w3-input w3-border-green w3-text-green" name="value" id="value" type="text" value="" onkeypress="return isNumberKey(event)">
<?php	
	}
} else {
	$values=parseSampleValues($values);
	if($measurement_type==0){
		?>
<b><br>Values (sample:value)</b> <span id="samples"><?php echo($values); ?></span><br><a href="javascript:showPopup('edit_samples.php?id=-1&m_id=<?php echo($id); ?>',800,700);">Add/edit samples</a><br><br>
<?php	
	} else if($measurement_type==1) {
	?>
<b><br>Values (sample:value in <?php echo($measurement_units); ?>)</b> <span id="samples"><?php echo($values); ?></span><br><a href="javascript:showPopup('edit_samples.php?id=-1&m_id=<?php echo($id); ?>',800,700);">Add/edit samples</a><br><br>
<?php
	} else {
		$values=parseHealthReportValues($dbh,$values);
	?>
<b><br>Health report (sample #:problems)</b><br> <span id="health"><?php echo($values); ?></span><br><a href="javascript:showPopup('edit_health_report.php?id=-1&m_id=<?php echo($id); ?>',800,700);">Add/edit health report</a><br><br>
<?php
	}
}
?>
<b>Image:</b> <input class="w3-input w3-border-green w3-text-green" name="input_picture" type="file" id="input_picture" accept=".jpg,.png">
<b>Comments:</b> <input class="w3-input w3-border-green w3-text-green" name="comments" type="text" value="">
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="add" name="add">Add</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br></form>
<?php
} 
?>