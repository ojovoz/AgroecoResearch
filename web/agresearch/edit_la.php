<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['edit'])){
	
	$id=$_POST['id'];
	$plots=$_POST['plots'];
	$dd=$_POST['dd'];
	$mm=$_POST['mm'];
	$yyyy=$_POST['yyyy'];
	$date=$yyyy."-".$mm."-".$dd;
	$value=floatval($_POST['value']);
	$units=$_POST['units'];
	$laborers=$_POST['laborers'];
	$cost=floatval($_POST['cost']);
	$comments=$_POST['comments'];
	if(isset($_FILES['log_picture']['name'])){
		$image_file=$_FILES['log_picture']['name'];
		$upload = "images/".$image_file;
		if(is_uploaded_file($_FILES['log_picture']['tmp_name'])) {
			move_uploaded_file($_FILES['log_picture']['tmp_name'],$upload);
			$update_picture=", log_picture='$upload'";
		} else {
			$update_picture="";
		}
	} else {
		$update_picture="";
	}
	
	$query="UPDATE log SET log_date='$date', log_value_number=$value, log_value_units='$units', log_number_of_laborers='$laborers', log_cost='$cost', log_comments='$comments'".$update_picture.", plots='$plots' WHERE log_id=$id";
	$result = mysqli_query($dbh,$query);
	echo "<script type='text/javascript'>";
	echo "window.opener.location.reload(false);";
	echo "window.close();";
	echo "</script>";
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	
	$id=$_GET['id'];
	$query="SELECT log_id, log_date, field_name, field_replication_number, plots, activity_name, log_value_units, log_value_number, log_comments, log_picture, log_number_of_laborers, log_cost, field.field_id, log.user_id, log.activity_id FROM log, field, activity WHERE log_id=$id AND field.field_id = log.field_id AND activity.activity_id = log.activity_id";
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	
	$plot_labels = calculatePlotLabels($dbh,$row[12],$row[4]);
	$plot_labels_list = explode(",",$plot_labels);
	$plot_ids_list = explode(",",$row[4]);
	$formatted_plot_labels = "";
	for($i=0;$i<sizeof($plot_labels_list);$i++){
		if($formatted_plot_labels==""){
			$formatted_plot_labels='<a href="javascript:removePlot(\''.trim($plot_labels_list[$i]).'\','.$plot_ids_list[$i].')">'.$plot_labels_list[$i].'</a>';
		} else {
			$formatted_plot_labels.=', <a href="javascript:removePlot(\''.trim($plot_labels_list[$i]).'\','.$plot_ids_list[$i].')">'.$plot_labels_list[$i].'</a>';
		}
	}
	
	$field=$row[12];
	
	$date=$row[1];
	$date_parts=explode("-",$date);
	$yy=$date_parts[0];
	$mm=$date_parts[1];
	$dd=$date_parts[2];
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
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
	   
	   function addPlot(){
		var e = document.getElementById("a_plots");
		var plot_id = e.options[e.selectedIndex].value;
		if(plot_id!=""){
			if(plot_id=="*"){
				for(var i=2;i<e.length;i++){
					var plot_label = e.options[i].text;
					var this_plot_id = e.options[i].value;
					var p = document.getElementById("plots");
					var pl = document.getElementById("plot_labels");
					if(p.value==""){
						p.value=this_plot_id;
						pl.value=plot_label;
					} else {
						p.value+=","+this_plot_id;
						pl.value+=", "+plot_label;
					}
					var ip = document.getElementById("included_plots");
					ip.innerHTML=formatPlotsHTML(pl.value,p.value);
		
				}
				
				while(e.length>2){
					e.remove(2);
				}
				
				e.options[0].text="All plots added";
				e.selectedIndex="0";
				e.disabled=true;
			} else {
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
				if(e.length==2){
					e.options[0].text="All plots added";
					e.disabled=true;
				} else {
					e.options[0].text="Add a plot:";
					e.disabled=false;
				}
			}
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
			e.selectedIndex="0";
			
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
						string_labels=plot_labels_list[i].trim();
					} else {
						string_ids+=","+plot_list[i];
						string_labels+=", "+plot_labels_list[i].trim();
					}
				}
			}
			p.value=string_ids;
			pl.value=string_labels;
			var ip = document.getElementById("included_plots");
			ip.innerHTML=formatPlotsHTML(pl.value,p.value);
	   }
	   
	   function validateForm(){
			var p = document.getElementById("plots");
			if(p.value==""){
				alert("You must choose at least one plot");
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
		
	   }
       //-->
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Edit item</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm()">
<input name="id" type="hidden" id="id" value="<?php echo($id); ?>">
<input name="plots" type="hidden" id="plots" value="<?php echo($row[4]); ?>">
<input name="plot_labels" type="hidden" id="plot_labels" value="<?php echo($plot_labels); ?>">
<p><div class="w3-text-green">
<b>Registered by:</b> <?php echo(getUserNameFromId($dbh,$row[13])); ?><br>
<b>Field:</b> <?php echo($row[2]." R".$row[3]); ?><br>
<b>Plots (click on a plot to remove):</b> <span id="included_plots"><?php echo($formatted_plot_labels); ?></span><br>
<?php
$plots=getRemainingPlots($dbh,$field,$plot_ids_list,($row[14]*-1),"la");
$plot_list=explode(",",$plots);
$plot_labels=calculatePlotLabels($dbh,$field,$plots);
$plot_labels_list=explode(",",$plot_labels);
?>
<span id="available_plots">
<select class="w3-select w3-text-green" name="a_plots" id="a_plots" onchange="addPlot();">
  <option value="" selected>Add a plot:</option>
  <option value="*">All</option>
<?php
if($plots!=""){
	for($i=0;$i<sizeof($plot_list);$i++){
		echo('<option value="'.$plot_list[$i].'">'.$plot_labels_list[$i].'</option>');
	}
}
?>
</select>
</span><br>
<br>
<b>Activity:</b> <?php echo($row[5]); ?><br><br>
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
<b>Units:</b> <input class="w3-input w3-border-green w3-text-green" name="units" id="units" type="text" value="<?php echo($row[6]); ?>">
<b>Value:</b> <input class="w3-input w3-border-green w3-text-green" name="value" id="value" type="text" value="<?php echo($row[7]); ?>" onkeypress="return isNumberKey(event)">
<b>Number of laborers:</b> <input class="w3-input w3-border-green w3-text-green" name="laborers" type="text" value="<?php echo($row[10]); ?>">
<b>Cost:</b> <input class="w3-input w3-border-green w3-text-green" name="cost" type="text" value="<?php echo($row[11]); ?>">
<?php
if($row[9]!=""){
	$filename=$row[9];
	list($width, $height)=getimagesize($filename);
	$w=$width*(150/$height);
	$h=150;
?>
<br><img src="<?php echo($filename); ?>" width="<?php echo($w); ?>" height="<?php echo($h); ?>"><br>
<b>Replace image:</b> 
<?php	
} else {
?>
<b>Add an image:</b> 
<?php	
}
?>
<input class="w3-input w3-border-green w3-text-green" name="log_picture" type="file" id="log_picture" accept=".jpg,.png">
<b>Comments:</b> <input class="w3-input w3-border-green w3-text-green" name="comments" type="text" value="<?php echo($row[8]); ?>">
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="edit" name="edit">Edit</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br></form>
<?php
} 
?>