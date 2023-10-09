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
	
	$copy=$_POST['copy_to_replications'];
	
	$dd=$_POST['dd'];
	$mm=$_POST['mm'];
	$yyyy=$_POST['yyyy'];
	$date=$yyyy."-".$mm."-".$dd;
	$age=intval($_POST['age']);
	$origin=$_POST['origin'];
	$variety=$_POST['variety'];
	$units=$_POST['units'];
	$quantity=floatval($_POST['quantity']);
	$cost=floatval($_POST['cost']);
	$comments=$_POST['comments'];
	if(isset($_FILES['input_picture']['name'])){
		$image_file=$_FILES['input_picture']['name'];
		$upload = "images/".$image_file;
		if(is_uploaded_file($_FILES['input_picture']['tmp_name'])) {
			move_uploaded_file($_FILES['input_picture']['tmp_name'],$upload);
			$picture=$upload;
		} else {
			$picture="";
		}
	} else {
		$picture="";
	}
	
	if($copy=="1"){
		$fields=getAllReplications($field,$dbh);
		for($i=0;$i<sizeof($fields);$i++){
			$f=$fields[$i];
			if($f==$field){
				$query="INSERT INTO input_log (crop_id, user_id, field_id, plots, input_log_date, input_age, input_origin, input_crop_variety, input_units, input_quantity, input_cost, input_comments, input_picture) VALUES ($id,$user,$field,'$plots','$date',$age,'$origin','$variety','$units',$quantity,$cost,'$comments','$picture')";
				$result = mysqli_query($dbh,$query);
			} else {
				$copied_comments=$comments." (copied)";
				$copied_plots=getEquivalentPlots($field,$plots,$f,$dbh);
				$query="INSERT INTO input_log (crop_id, user_id, field_id, plots, input_log_date, input_age, input_origin, input_crop_variety, input_units, input_quantity, input_cost, input_comments, input_picture) VALUES ($id,$user,$f,'$copied_plots','$date',$age,'$origin','$variety','$units',$quantity,$cost,'$copied_comments','$picture')";
				$result = mysqli_query($dbh,$query);
			}
		}
	} else {
		$query="INSERT INTO input_log (crop_id, user_id, field_id, plots, input_log_date, input_age, input_origin, input_crop_variety, input_units, input_quantity, input_cost, input_comments, input_picture) VALUES ($id,$user,$field,'$plots','$date',$age,'$origin','$variety','$units',$quantity,$cost,'$comments','$picture')";
		$result = mysqli_query($dbh,$query);
	}
	
	echo "<script type='text/javascript'>";
	echo "window.opener.location.reload(false);";
	echo "window.close();";
	echo "</script>";
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	
	$id=$_GET['id'];
	$field=$_GET['field'];
	
	$query="SELECT crop_name FROM crop WHERE crop_id=$id";
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	$crop_name = $row[0];

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
<script>
       <!--
	   
	   var selected_plots=[];
	   
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
			
			var q = document.getElementById("quantity");
			if(q.value==""){
				alert("Quantity must not be empty");
				return false;
			} else {
				var qNumber = parseInt(q.value,10);
				if(qNumber<=0 || qNumber>1000){
					alert("Invalid quantity");
					return false;
				}
			}
		
	   }
       //-->
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Add crop input</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm()">
<input name="id" type="hidden" id="id" value="<?php echo($id); ?>">
<input name="field" type="hidden" id="field" value="<?php echo($field); ?>">
<input name="plots" type="hidden" id="plots" value="">
<input name="plot_labels" type="hidden" id="plot_labels" value="">
<p><div class="w3-text-green">
<b>Registered by:</b> <?php echo(getUserNameFromId($dbh,$_SESSION['user_id'])); ?><br>
<b>Field:</b> <?php echo(getFieldNameFromId($dbh,$field)); ?><br>
<b>Plots (click on a plot to remove):</b> <span id="included_plots"></span><br>
<?php
$plots=getRemainingPlots($dbh,$field,"",($id*-1),"ic");
if($plots!=""){
	$plot_list=explode(",",$plots);
	$plot_labels=calculatePlotLabels($dbh,$field,$plots);
	$plot_labels_list=explode(",",$plot_labels);
?>
<span id="available_plots">
<select class="w3-select w3-text-green" name="a_plots" id="a_plots" onchange="addPlot();">
  <option value="" selected>Add a plot:</option>
  <option value="*">All</option>
<?php

for($i=0;$i<sizeof($plot_list);$i++){
	echo('<option value="'.$plot_list[$i].'">'.$plot_labels_list[$i].'</option>');
}
?>
</select>
</span><br>
<?php }
?>
<br>
<b>Crop:</b> <?php echo($crop_name); ?><br><br>
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
<br><b>Age (days):</b> <input class="w3-input w3-border-green w3-text-green" name="age" type="text" value="" onkeypress="return isNumberKey(event)">
<b>Origin:</b> <input class="w3-input w3-border-green w3-text-green" name="origin" type="text" maxlength="200" value="">
<b>Variety:</b> <input class="w3-input w3-border-green w3-text-green" name="variety" type="text" maxlength="200" value="">
<b>Units:</b> <input class="w3-input w3-border-green w3-text-green" name="units" id="units" type="text" maxlength="200" value="kg">
<b>Quantity:</b> <input class="w3-input w3-border-green w3-text-green" name="quantity" id="quantity" type="text" value="" onkeypress="return isNumberKey(event)">
<b>Cost (local currency):</b> <input class="w3-input w3-border-green w3-text-green" name="cost" type="text" value="" onkeypress="return isNumberKey(event)">
<br>
<b>Image:</b> <input class="w3-input w3-border-green w3-text-green" name="input_picture" type="file" id="input_picture" accept=".jpg,.png">
<b>Comments:</b> <input class="w3-input w3-border-green w3-text-green" name="comments" type="text" value="">
<br><b><input class="w3-check" type="checkbox" id="copy_to_replications" name="copy_to_replications" value="1"> Copy to other replications</b>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="add" name="add">Add</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br></form>
<?php
} 
?>