<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['continue'])){
		$calculate=true;
		$create=true;
		$proceed=false;
		$field_id=$_POST['id'];
		$fname=$_POST['fname'];
		$method=$_POST['method'];
		if($method=="calculate"){
			$crop1=$_POST['crop1'];
			$crop2=$_POST['crop2'];
			if(!is_numeric($crop1) && !is_numeric($crop2)){
				$calculate=false;
				$proceed=true;
				$error_message="Error: you must choose at least one crop.";
			} else if ($crop1==$crop2){
				$calculate=false;
				$proceed=true;
				$error_message="Error: crops must be different.";
			} else if (!is_numeric($crop1) || !is_numeric($crop2)){
				$ncrops=1;
				if(is_numeric($crop1)){
					$crop_1=$crop1;
				} else {
					$crop_1=$crop2;
				}
			} else {
				$ncrops=2;
				$crop_1=$crop1;
				$crop_2=$crop2;
			}
			
			if(isset($_POST['intercropping'])){
				$intercropping=true;
				$intercropping_crop=$_POST['intercropping_crop'];
				if(!is_numeric($intercropping_crop)){
					$calculate=false;
					$proceed=true;
					$error_message="Error: you must choose a crop for intercropping.";
				}
			} else {
				$intercropping=false;
			}
			if(isset($_POST['soil_management'])){
				$soil_management=true;
			} else {
				$soil_management=false;
			}
			if(isset($_POST['pest_control'])){
				$pest_control=true;
			} else {
				$pest_control=false;
			}
			if($calculate){
				$proceed=false;
				if($ncrops==1 && !$intercropping && !$soil_management && !$pest_control){
					$configuration="F=(1,0,0,0);G=(1,1);P=($crop_1,0,0,0);";
				} else if($ncrops==2 && !$intercropping && !$soil_management && !$pest_control){
					$configuration="F=(2,0,0,0);G=(1,2);P=($crop_1,0,0,0);P=($crop_2,0,0,0)";
				} else if($ncrops==1 && $intercropping && !$soil_management && !$pest_control){
					$configuration="F=(1,1,0,0);G=(1,2);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0)";
				} else if($ncrops==1 && !$intercropping && $soil_management && !$pest_control){
					$configuration="F=(1,0,1,0);G=(1,2);P=($crop_1,0,0,0);P=($crop_1,0,1,0)";
				} else if($ncrops==1 && !$intercropping && !$soil_management && $pest_control){
					$configuration="F=(1,0,0,1);G=(1,2);P=($crop_1,0,0,0);P=($crop_1,0,0,1)";
				} else if($ncrops==2 && $intercropping && !$soil_management && !$pest_control){
					$configuration="F=(2,1,0,0);G=(1,4);P=($crop_1,0,0,0);P=($crop_2,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_2,$intercropping_crop,0,0);";
				} else if($ncrops==1 && $intercropping && $soil_management && !$pest_control){
					$configuration="F=(1,1,1,0);G=(2,2);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_1,0,1,0);P=($crop_1,$intercropping_crop,1,0);";
				} else if($ncrops==1 && !$intercropping && $soil_management && $pest_control){
					$configuration="F=(1,0,1,1);G=(2,2);P=($crop_1,0,0,0);P=($crop_1,0,1,0);P=($crop_1,0,0,1);P=($crop_1,0,1,1);";
				} else if($ncrops==2 && !$intercropping && $soil_management && !$pest_control){
					$configuration="F=(2,0,1,0);G=(2,2);P=($crop_1,0,0,0);P=($crop_2,0,0,0);P=($crop_1,0,1,0);P=($crop_2,0,1,0);";
				} else if($ncrops==2 && !$intercropping && !$soil_management && $pest_control){
					$configuration="F=(2,0,0,1);G=(2,2);P=($crop_1,0,0,0);P=($crop_2,0,0,0);P=($crop_1,0,0,1);P=($crop_2,0,0,1);";
				} else if($ncrops==1 && $intercropping && !$soil_management && $pest_control){
					$configuration="F=(1,1,0,1);G=(2,2);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_1,0,0,1);P=($crop_1,$intercropping_crop,0,1);";
				} else if($ncrops==2 && $intercropping && $soil_management && !$pest_control){
					$configuration="F=(2,1,1,0);G=(2,4);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_1,0,1,0);P=($crop_1,$intercropping_crop,1,0);P=($crop_2,$intercropping_crop,0,0);P=($crop_2,0,0,0);P=($crop_2,$intercropping_crop,1,0);P=($crop_2,0,1,0);";
				} else if($ncrops==1 && $intercropping && $soil_management && $pest_control){
					$configuration="F=(1,1,1,1);G=(2,4);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_1,0,1,0);P=($crop_1,$intercropping_crop,1,0);P=($crop_1,0,0,1);P=($crop_1,$intercropping_crop,0,1);P=($crop_1,0,1,1);P=($crop_1,$intercropping_crop,1,1);";
				} else if($ncrops==2 && !$intercropping && $soil_management && $pest_control){
					$configuration="F=(2,0,1,1);G=(2,4);P=($crop_1,0,0,0);P=($crop_2,0,0,0);P=($crop_1,0,1,0);P=($crop_2,0,1,0);P=($crop_1,0,0,1);P=($crop_2,0,0,1);P=($crop_1,0,1,1);P=($crop_2,0,1,1);";
				} else if($ncrops==2 && $intercropping && !$soil_management && $pest_control){
					$configuration="F=(2,1,0,1);G=(2,4);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_1,0,0,1);P=($crop_1,$intercropping_crop,0,1);P=($crop_2,$intercropping_crop,0,1);P=($crop_2,0,0,1);P=($crop_2,$intercropping_crop,0,1);P=($crop_2,0,0,1);";
				} else if($ncrops==2 && $intercropping && $soil_management && $pest_control){
					$configuration="F=(2,1,1,1);G=(4,4);P=($crop_1,0,0,0);P=($crop_1,$intercropping_crop,0,0);P=($crop_1,0,1,0);P=($crop_1,$intercropping_crop,1,0);P=($crop_2,$intercropping_crop,0,0);P=($crop_2,0,0,0);P=($crop_2,$intercropping_crop,1,0);P=($crop_2,0,1,0);P=($crop_1,0,0,1);P=($crop_1,$intercropping_crop,0,1);P=($crop_1,0,1,1);P=($crop_1,$intercropping_crop,1,1);P=($crop_2,$intercropping_crop,0,1);P=($crop_2,0,0,1);P=($crop_2,$intercropping_crop,1,1);P=($crop_2,0,1,1);";
				}
					
				//echo($configuration);
				//break;
				
				$query="UPDATE field SET field_configuration='$configuration' WHERE field_id=$field_id";
				$result = mysqli_query($dbh,$query);
				
				header("Location: configure_plots.php?id=".$field_id."&fname=".$fname);
			}	
		} else if($method=="create"){
			$rows=$_POST['rows'];
			if($rows<=0 || $rows>=4){
				$create=false;
				$proceed=true;
				$create_error_message="Error: number of rows must be between 1 and 4.";
			}
			$columns=$_POST['columns'];
			if($columns<=0 || $columns>=4){
				$create=false;
				$proceed=true;
				$create_error_message="Error: number of columns must be between 1 and 4.";
			}
			
			if($create){
				$n=$rows*$columns;
				$plots="";
				for($i=0;$i<$n;$i++){
					if($plots==""){
						$plots="P=(0,0,0,0)";
					} else {
						$plots=$plots.";P=(0,0,0,0)";
					}
				}
				$configuration="F=(0,0,0,0);G=($rows,$columns);".$plots;
				
				//echo($configuration);
				//break;
				
				$query="UPDATE field SET field_configuration='$configuration' WHERE field_id=$field_id";
				$result = mysqli_query($dbh,$query);
				
				header("Location: configure_plots.php?id=".$field_id."&fname=".$fname);
			}
		}
	} else if (isset($_POST['cancel'])){
		header("Location: fields.php");
	}
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$field_id=$_GET['id'];
	$fname=$_GET['fname'];
	
	if(fieldHasConfiguration($field_id,$dbh)){
		$proceed=false;
		$goto_plots=true;
	} else {
		$goto_plots=false;
		$proceed=true;
	}
	
} else {
	$proceed=false;
}

if($proceed){
	$cropsNI=getCrops($dbh,0);
	$cropsI=getCrops($dbh,1);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Configure <?php echo($fname); ?></h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="config">
<input name="method" id="method" type="hidden" value="calculate">
<input name="id" id="id" type="hidden" value="<?php  echo($field_id); ?>">
<input name="fname" id="fname" type="hidden" value="<?php  echo($fname); ?>">
<input class="w3-radio" type="radio" name="mode" id="mode" value="calculate" checked>
<label class="w3-validate w3-text-green">Caclulate field</label>
<p><div class="w3-row-padding"><div class="w3-half w3-text-green"><select class="w3-select w3-text-green" name="crop1" id="crop1">
<option value="c1" disabled selected>Crop 1</option><?php
	for($i=0;$i<sizeof($cropsNI);$i++){
		$selected="";
		$parts=explode(",",$cropsNI[$i]);
		$c_id=$parts[0];
		$c_name=$parts[1];
		if($crop1==$c_id){
			$selected=" selected";
		}
		echo('<option value="'.$c_id.'"'.$selected.'>'.$c_name.'</option>');
	}
?></select></div>
<div class="w3-half w3-text-green"><select class="w3-select w3-text-green" name="crop2" id="crop2">
<option value="c2" disabled selected>Crop 2</option><?php
	for($i=0;$i<sizeof($cropsNI);$i++){
		$parts=explode(",",$cropsNI[$i]);
		$c_id=$parts[0];
		$c_name=$parts[1];
		if($crop2==$c_id){
			$selected=" selected";
		}
		echo('<option value="'.$c_id.'"'.$selected.'>'.$c_name.'</option>');
	}
?></select></div>
</div><div align="center" class="w3-text-red" id="crop_error_message"><?php if(isset($error_message)){ echo($error_message); } ?></div></p>
<script type="text/javascript">
	function compareCrops(){
		if(document.getElementById("crop1").value==document.getElementById("crop2").value){
			document.getElementById("crop_error_message").innerHTML='Error: crops must be different.';
		} else {
			document.getElementById("crop_error_message").innerHTML='';
		}
	}
	document.getElementById("crop1").onchange = function () {
		compareCrops();
	}
	document.getElementById("crop2").onchange = function () {
		compareCrops();
	}
</script>
<p><div class="w3-row-padding"><div class="w3-half w3-text-green"><input class="w3-check" type="checkbox" value="1" name="intercropping" id="intercropping" <?php if($intercropping){ echo("checked"); } ?>>
<label class="w3-validate w3-text-green">Intercropping</label></div><div class="w3-half w3-text-green" id="intercropping_select"></div>
</div></p>
<script type="text/javascript">
	function showIntercroppingCrops(){
		if(document.getElementById("intercropping").checked){
			document.getElementById("intercropping_select").innerHTML='<select class="w3-select w3-text-green" name="intercropping_crop" id="intercropping_crop"><option value="" disabled selected>Intercropping crop:</option><?php 
			for($i=0;$i<sizeof($cropsI);$i++){ 
				$selected="";
				$parts=explode(",",$cropsI[$i]);
				$id=$parts[0];
				$name=$parts[1];
				if($intercropping_crop==$id){
					$selected=" selected";
				}
			?><option value="<?php echo($id); ?>"<?php echo($selected); ?>><?php echo($name); ?></option><?php
			} ?></select>';
		} else {
			document.getElementById("intercropping_select").innerHTML='';
		}
	}
	
	document.getElementById("intercropping").onclick = function () {
		showIntercroppingCrops();
	}
	
	if(document.getElementById("intercropping").checked){
		showIntercroppingCrops();
	}
</script>
<p><div class="w3-row-padding"><div class="w3-half w3-text-green"><input class="w3-check" type="checkbox" value="1" name="soil_management" id="soil_management" <?php if($soil_management){ echo("checked"); } ?>>
<label class="w3-validate w3-text-green">Soil management</label></div></div></p>
<p><div class="w3-row-padding"><div class="w3-half w3-text-green"><input class="w3-check" type="checkbox" value="1" name="pest_control" id="pest_control" <?php if($pest_control){ echo("checked"); } ?>>
<label class="w3-validate w3-text-green">Pest control</label></div></div></p><hr class="w3-border-green">
<input class="w3-radio" type="radio" name="mode" id="mode" value="create">
<label class="w3-validate w3-text-green">Create field</label>
<p><div class="w3-row-padding"><div class="w3-half w3-text-green"><label class="w3-text-green">Rows:</label><input class="w3-input w3-border-green w3-text-green" name="rows" id="rows" type="text" disabled></div><div class="w3-half w3-text-green"><label class="w3-text-green">Columns:</label><input class="w3-input w3-border-green w3-text-green" name="columns" id="columns" type="text" disabled></div>
</div><div align="center" class="w3-text-red" id="crop_error_message"><?php if(isset($create_error_message)){ echo($create_error_message); } ?></div></p>
<script type="text/javascript">
	var mode = document.forms["config"].elements["mode"];
	for (var i=0, len=mode.length; i<len; i++) {
		mode[i].onclick = function () {
			if(this.value=="create"){
				document.getElementById("rows").disabled=false;
				document.getElementById("columns").disabled=false;
				document.getElementById("crop1").disabled=true;
				document.getElementById("crop2").disabled=true;
				document.getElementById("intercropping").disabled=true;
				document.getElementById("intercropping").checked=false;
				document.getElementById("soil_management").disabled=true;
				document.getElementById("pest_control").disabled=true;
				document.getElementById("crop_error_message").innerHTML='';
				document.getElementById("intercropping_select").innerHTML='';
				document.getElementById("method").value='create';
			} else {
				document.getElementById("rows").disabled=true;
				document.getElementById("columns").disabled=true;
				document.getElementById("crop1").disabled=false;
				document.getElementById("crop2").disabled=false;
				document.getElementById("intercropping").disabled=false;
				document.getElementById("soil_management").disabled=false;
				document.getElementById("pest_control").disabled=false;
				document.getElementById("method").value='calculate';
			}
		}
	}
</script>
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="continue" name="continue">Continue</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button>
</form><br><br>
</div>
</body>
</html>
<?php
} 

if($goto_plots){
	header("Location: configure_plots.php?id=".$field_id."&fname=".$fname);
}
?>