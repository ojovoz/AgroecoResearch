<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

$proceed=false;

if(isset($_POST['edit'])){
	$id=$_POST['id'];
	$task=$_POST['task'];
	$plots=$_POST['plots'];
	if($task=="lm" || $task=="la"){
		
		$query="UPDATE log SET plots='".$plots."' WHERE log_id=$id";
		$result = mysqli_query($dbh,$query);
		
	} else {
		
		$query="UPDATE input_log SET plots='".$plot_list."' WHERE input_log_id=$id";
		$result = mysqli_query($dbh,$query);
		
	}
	
	if($task=="lm"){
		header("Location: edit_lm.php?id=$id");
	} else if ($task=="la"){
		header("Location: edit_la.php?id=$id");
	} else if ($task=="it"){
		header("Location: edit_ilt.php?id=$id");
	} else if ($task=="ic"){
		header("Location: edit_ilc.php?id=$id");
	}
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	$id=$_GET['id'];
	$task=$_GET['task'];
	if($task=="lm" || $task=="la"){
		$query="SELECT plots, field_id FROM log WHERE log_id=$id";
	} else {
		$query="SELECT plots, field_id FROM input_log WHERE input_log_id=$id";
	}
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	$plot_list = $row[0];
	$field_id=$row[1];
	$plots = explode(",",$plot_list);
	$plot_labels="";
	for($i=0;$i<sizeof($plots);$i++){
		$plot_name=calculatePlotLabels($dbh,$field_id,$plots[$i]);
		if($plot_labels==""){
			$plot_labels=$plot_name;
		} else {
			$plot_labels.=", ".$plot_name;
		}
	}
	$proceed=true;
	
}

if($proceed){
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script>
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
			if(e.length==1){
				e.options[0].text="All plots added";
				e.disabled=true;
			} else {
				e.options[0].text="Add a plot:";
				e.disabled=false;
			}
		
		}
	}
	
	function formatPlotsHTML(plots,ids){
		var plot_labels_list = plots.split(",");
		var plot_ids_list = ids.split(",");
		var stringHTML="";
		for(var i=0;i<plot_ids_list.length;i++){
			if(plot_labels_list[i].trim()!=""){
				if(stringHTML==""){
					stringHTML=plot_labels_list[i]+' <a href="javascript:removePlot(\''+ plot_labels_list[i].trim() +'\','+ plot_ids_list[i] +')">remove</a>';
				} else {
					stringHTML+='<br>'+ plot_labels_list[i] +' <a href="javascript:removePlot(\''+ plot_labels_list[i].trim() +'\','+ plot_ids_list[i] +')">remove</a>';
				}
			}
		}
		stringHTML+="<br>";
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
	
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Edit plots</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="id" type="hidden" id="id" value="<?php echo($id); ?>">
<input name="task" type="hidden" id="task" value="<?php echo($task); ?>">
<input name="plots" type="hidden" id="plots" value="<?php echo($plot_list); ?>">
<input name="plot_labels" type="hidden" id="plot_labels" value="<?php echo($plot_labels); ?>">
<p><div class="w3-text-green">
<b><?php if($task=="lm") { echo("Affected plot"); } else { echo("Affected plots"); } ?>:</b><br>
<span id="included_plots">
<?php
	for($i=0;$i<sizeof($plots);$i++){
		$plot=$plots[$i];
		$plot_name=calculatePlotLabels($dbh,$field_id,$plot);
		if($task!="lm"){
			$remove_link='<a href="javascript:removePlot(\''.$plot_name.'\','.$plot.')">remove</a><br>';
		} else {
			$remove_link='';
		}
		echo($plot_name.' '.$remove_link);
	}
?></span><br>
<?php
	$remaining_plots=getRemainingPlots($dbh,$field_id,$plots,$id,$task);
	if($remaining_plots!=""){
?>
<b>Available plots:</b><br>
<select class="w3-select w3-text-green" name="a_plots" id="a_plots" onchange="addPlot();">
<option value="" selected><?php if($task=="lm") { echo("Change plot:"); } else { echo("Add a plot:"); } ?></option>
<?php
	$remaining_plots_list=explode(",",$remaining_plots);
	for($i=0;$i<sizeof($remaining_plots_list);$i++){
		$remaining_plot=$remaining_plots_list[$i];
		$plot_name=calculatePlotLabels($dbh,$field_id,$remaining_plot);
		echo('<option value="'.$remaining_plot.'">'.$plot_name.'</option>');
	}
?>
</select><br><br>
<?php
}
?>
</div>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="edit" name="edit">Edit</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</form>
</div>
</body>
</html>
<?php
} 
?>