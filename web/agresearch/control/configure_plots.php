<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();
$proceed=true;
$update_error="";

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$field_id=$_POST['id'];
	$fname=$_POST['fname'];
	$config=$_POST['fieldConfig'];
	$config=recalculateConfig($config);
	if(isset($_POST['edit'])){
		$query="UPDATE field SET field_configuration='$config' WHERE field_id=$field_id";
		$result = mysqli_query($dbh,$query);
		header("Location: fields.php");
		$proceed=false;
	} else if(isset($_POST['update'])){
		$update_error=updateFieldConfiguration($dbh,$field_id,$config);
		if($update_error==""){
			header("Location: fields.php");
			$proceed=false;
		}
	} else if(isset($_POST['cancel'])){
		header("Location: fields.php");
		$proceed=false;
	} 
} 

if(isset($_SESSION['admin']) && $_SESSION['admin']==true && $proceed) {
	if(isset($_GET['id'])) {
		$field_id=$_GET['id'];
	}
	if(isset($_GET['fname'])){
		$fname=$_GET['fname'];
	}
	$config=getFieldConfiguration($field_id,$dbh);
	$config_parts=explode(";",$config);
	$general=parseConfig($config_parts[0]);
	$grid=parseConfig($config_parts[1]);
	$rows=$grid[0];
	$columns=$grid[1];
	$cropsNI=getCrops($dbh,0);
	$cropsI=getCrops($dbh,1);
	
	$cell_color=getTreatmentColors($dbh);
	
	function getCropSymbol($id,$crops){
		$ret="C";
		for($i=0;$i<sizeof($crops);$i++){
			$parts=explode(",",$crops[$i]);
			if($id==$parts[0]){
				$ret=$parts[2];
				break;
			}
		}
		return $ret;
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
<script type="text/javascript">
	<?php
		$js_crops_ni="";
		for($i=0;$i<sizeof($cropsNI);$i++){
			$parts=explode(",",$cropsNI[$i]);
			$id=$parts[0];
			$name=$parts[1];
			$symbol=$parts[2];
			if($js_crops_ni==""){
				$js_crops_ni='"'.$id.','.$name.','.$symbol.'"';
			} else {
				$js_crops_ni.=',"'.$id.','.$name.','.$symbol.'"';
			}
		}
		$js_crops_i="";
		for($i=0;$i<sizeof($cropsI);$i++){
			$parts=explode(",",$cropsI[$i]);
			$id=$parts[0];
			$name=$parts[1];
			if($js_crops_i==""){
				$js_crops_i='"'.$id.','.$name.','.$symbol.'"';
			} else {
				$js_crops_i.=',"'.$id.','.$name.','.$symbol.'"';
			}
		}
	?>
	var cropsNI=[<?php echo($js_crops_ni); ?>];
	var cropsI=[<?php echo($js_crops_i); ?>];
	var cellColors=[<?php for($i=0;$i<sizeof($cell_color);$i++){ if($i==0){ 
		echo("'".$cell_color[$i]."'"); 
	} else { 
		echo(",'".$cell_color[$i]."'"); 
	}
	}?>];
	var currentPlot=-1;
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Configure plots: <?php echo($fname); ?></h2><br>
<?php
if($update_error!=""){
	echo('<span class="w3-text-red">'.$update_error.'</span><br>');
}
?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="config">
<input name="id" id="id" type="hidden" value="<?php  echo($field_id); ?>">
<input name="fname" id="fname" type="hidden" value="<?php  echo($fname); ?>">
<input name="fieldConfig" id="fieldConfig" type="hidden" value="<?php  echo($config); ?>">
<p><div class="w3-row-padding"><div class="w3-half w3-text-green">
<table>
<?php
$p=2;
$n=0;
$c=0;
$crops=array();
$used_colors=array();
for($i=1;$i<=$rows;$i++){
	echo("<tr>");
	for($j=1;$j<=$columns;$j++){
		$plot=parseConfig($config_parts[$p]);
		if($plot[2]==0 && $plot[3]==0){
			$color=$cell_color[0];
		} else if($plot[2]==1 && $plot[3]==0){
			$color=$cell_color[1];
		} else if($plot[2]==0 && $plot[3]==1){
			$color=$cell_color[2];
		} else if($plot[2]==1 && $plot[3]==1){
			$color=$cell_color[3];
		}
		if(!in_array($color,$used_colors)){
			$used_colors[$c]=$color;
			$c++;
		}
		
		$content=getCropSymbol($plot[0],$cropsNI);
		if($plot[1]!=0){
			$content.="+L";
		}
		echo('<td id="cell'.($p-2).'" class="'.$color.'" style="height:100px; padding:3px; border: 3px solid white;"><div align="center"><strong><a class="w3-text-black" href="javascript:displayPlotConfiguration('.($p-2).',\''.implode(",",$plot).'\')">'.$content.'</a></strong></div></td>');
		$p++;
	}
	echo("</tr>");
}
?>
</table><br>
<table>
<?php
for($i=0;$i<sizeof($used_colors);$i++){
	echo("<tr>");
	echo('<td class="'.$used_colors[$i].'" style="width:10%; padding:3px; border: 3px solid white;">&nbsp;</td>');
	if($used_colors[$i]==$cell_color[0]){
		$legend="Control treatment";
	} else if($used_colors[$i]==$cell_color[1]) {
		$legend="Soil management";
	} else if($used_colors[$i]==$cell_color[2]) {
		$legend="Pest control";
	} else if($used_colors[$i]==$cell_color[3]) {
		$legend="Soil management AND Pest control";
	}
	echo('<td class="w3-text-black" style="padding:3px; border: 3px solid white;">'.$legend.'</td>');
	echo("</tr>");
}
?>
</table><br>
</div><div class="w3-half w3-text-green"><div id="plotNumber"></div><br><div id="plotCrop"></div><br>
<div id="plotIntercropping"></div><br>
<div id="plotIntercroppingCrop"></div><br>
<div id="plotSoilManagement"></div><br>
<div id="plotPestControl"></div><br>
<div id="updatePlotButton"></div><br>
<div id="plotUpdatedMsg" class="w3-text-green"></div><br>
</div>
</div>
<script type="text/javascript">
	function updateFieldConfiguration(plotConfig){
		plotN=currentPlot;
		fieldConfiguration=document.getElementById("fieldConfig").value;
		var fieldParts=fieldConfiguration.split(";");
		fieldParts[plotN+2]='P=('+ plotConfig +')';
		fieldConfiguration=fieldParts.join(";");
		document.getElementById("fieldConfig").value=fieldConfiguration;
		
		configParts=plotConfig.split(",");
		var cellClass='';
		if(configParts[2]==0 && configParts[3]==0){
			cellClass=cellColors[0];
		} else if(configParts[2]==1 && configParts[3]==0){
			cellClass=cellColors[1];
		} else if(configParts[2]==0 && configParts[3]==1){
			cellClass=cellColors[2];
		} else if(configParts[2]==1 && configParts[3]==1){
			cellClass=cellColors[3];
		}
	
		document.getElementById('cell'+plotN.toString()).className=cellClass;
		
		var symbol="C";
		var i;
		for(i=0;i<cropsNI.length;i++){
			parts=cropsNI[i].split(",");
			id=parts[0];
			if(configParts[0]==id){
				symbol=parts[2];
				break;
			}
		}
		var content=symbol;
		if(configParts[1]>0){
			content+='+L';
		}
		
		document.getElementById('cell'+plotN.toString()).innerHTML='<div align="center"><strong><a class="w3-text-black" href="javascript:displayPlotConfiguration('+ plotN +',\''+ plotConfig +'\')">'+ content +'</a></strong></div>';
	}
	
	function showIntercroppingCrops(configParts){
		options="";
		for(i=0;i<cropsI.length;i++){
			selected="";
			parts=cropsI[i].split(",");
			id=parts[0];
			name=parts[1];
			if(id==configParts[1]){
				selected=" selected";
			}
			options+='<option value="'+id+'"'+selected+'>'+name+'</option>';
		}
		document.getElementById("plotIntercroppingCrop").innerHTML='<select class="w3-select w3-text-green" name="intercropping_crop" id="intercropping_crop">'+ options +'</select>';
	}
	
	function displayPlotConfiguration(plotN,plotConfig){
		currentPlot=plotN;
		var configParts=plotConfig.split(",");
		document.getElementById("plotNumber").innerHTML='<span class="w3-green">&nbsp;Plot number '+ (plotN+1) +':&nbsp;</span>';
		var i;
		var options="";
		var selected="";
		var id;
		var name;
		for(i=0;i<cropsNI.length;i++){
			selected="";
			parts=cropsNI[i].split(",");
			id=parts[0];
			name=parts[1];
			if(id==configParts[0]){
				selected=" selected";
			}
			options+='<option value="'+id+'"'+selected+'>'+name+'</option>';
		}
		document.getElementById("plotCrop").innerHTML='<select class="w3-select w3-text-green" name="primary_crop" id="primary_crop">'+ options +'</select>';
		if(configParts[1]==0){
			document.getElementById("plotIntercropping").innerHTML='<input class="w3-check" type="checkbox" value="1" name="intercropping" id="intercropping"><label class="w3-validate w3-text-green">Intercropping</label>';
			document.getElementById("plotIntercroppingCrop").innerHTML='';
		} else {
			document.getElementById("plotIntercropping").innerHTML='<input class="w3-check" type="checkbox" value="1" name="intercropping" id="intercropping" checked><label class="w3-validate w3-text-green">Intercropping</label>';
			showIntercroppingCrops(configParts);
		}
		document.getElementById("intercropping").onclick = function(configParts){
			if(document.getElementById("intercropping").checked){
				showIntercroppingCrops(configParts);
			} else {
				document.getElementById("plotIntercroppingCrop").innerHTML='';
			}
		}
		if(configParts[2]==0){
			document.getElementById("plotSoilManagement").innerHTML='<input class="w3-check" type="checkbox" value="1" name="soil_management" id="soil_management"><label class="w3-validate w3-text-green">Soil management</label>';
		} else {
			document.getElementById("plotSoilManagement").innerHTML='<input class="w3-check" type="checkbox" value="1" name="soil_management" id="soil_management" checked><label class="w3-validate w3-text-green">Soil management</label>';
		}
		if(configParts[3]==0){
			document.getElementById("plotPestControl").innerHTML='<input class="w3-check" type="checkbox" value="1" name="pest_control" id="pest_control"><label class="w3-validate w3-text-green">Pest control</label>';
		} else {
			document.getElementById("plotPestControl").innerHTML='<input class="w3-check" type="checkbox" value="1" name="pest_control" id="pest_control" checked><label class="w3-validate w3-text-green">Pest control</label>';
		}
		document.getElementById("updatePlotButton").innerHTML='<button type="button" class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="updatePlot" name="updatePlot">Update plot</button>';
		document.getElementById("plotUpdatedMsg").innerHTML='';
		
		document.getElementById("updatePlot").onclick = function () {
			var primary_crop_id=document.getElementById("primary_crop").value;
			var intercropping=document.getElementById("intercropping").checked;
			if(intercropping){
				intercropping_crop_id=document.getElementById("intercropping_crop").value;
			} else {
				intercropping_crop_id=0;
			}
			var soil_managament=document.getElementById("soil_management").checked;
			if(soil_managament){
				soil_management=1;
			} else {
				soil_management=0;
			}
			var pest_control=document.getElementById("pest_control").checked;
			if(pest_control){
				pest_control=1;
			} else {
				pest_control=0;
			}
			var new_plot_config=primary_crop_id +','+ intercropping_crop_id +','+ soil_management +','+ pest_control;
			updateFieldConfiguration(new_plot_config);
			document.getElementById("plotUpdatedMsg").innerHTML='Plot updated successfully.';
		}
	}
</script>
</p><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit" name="edit">Edit configuration</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="update" name="update">Update configuration (as new field)</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button><br><br>
</div></form>
</body>
</html>
<?php
} else {
	if($proceed){
		header("Location: index.php");
	}
}
?>