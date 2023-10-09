<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
$dbh = initDB();
session_start();

function getColorAssocValue($hex,$colors,$color_hex){
	$ret="";
	for($i=0;$i<sizeof($colors);$i++){
		if($color_hex[$i]==$hex){
			$ret=$colors[$i];
			break;
		}
	}
	return $ret;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['update_colors'])){
		$no_treatment_color=$_POST['no_treatment'];
		$no_treatment_color_w3=getColorAssocValue($no_treatment_color,$colors,$color_hex);
		$no_treatment_color_app=getColorAssocValue($no_treatment_color,$color_code_app,$color_hex);
		$soil_management_color=$_POST['soil_management'];
		$soil_management_color_w3=getColorAssocValue($soil_management_color,$colors,$color_hex);
		$soil_management_color_app=getColorAssocValue($soil_management_color,$color_code_app,$color_hex);
		$pest_control_color=$_POST['pest_control'];
		$pest_control_color_w3=getColorAssocValue($pest_control_color,$colors,$color_hex);
		$pest_control_color_app=getColorAssocValue($pest_control_color,$color_code_app,$color_hex);
		$both_treatments_color=$_POST['both_treatments'];
		$both_treatments_color_w3=getColorAssocValue($both_treatments_color,$colors,$color_hex);
		$both_treatments_color_app=getColorAssocValue($both_treatments_color,$color_code_app,$color_hex);
		$query="UPDATE treatment_color SET color='$no_treatment_color_w3', color_hex='$no_treatment_color', color_code_app='$no_treatment_color_app' WHERE treatment_color_id=1";
		$result = mysqli_query($dbh,$query);
		$query="UPDATE treatment_color SET color='$soil_management_color_w3', color_hex='$soil_management_color', color_code_app='$soil_management_color_app' WHERE treatment_color_id=2";
		$result = mysqli_query($dbh,$query);
		$query="UPDATE treatment_color SET color='$pest_control_color_w3', color_hex='$pest_control_color', color_code_app='$pest_control_color_app' WHERE treatment_color_id=3";
		$result = mysqli_query($dbh,$query);
		$query="UPDATE treatment_color SET color='$both_treatments_color_w3', color_hex='$both_treatments_color', color_code_app='$both_treatments_color_app' WHERE treatment_color_id=4";
		$result = mysqli_query($dbh,$query);
		header("Location: treatments.php");
	} else if(isset($_POST['back'])){
		header("Location: treatments.php");
	} 
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$query="SELECT treatment_color_id, color, color_hex FROM treatment_color ORDER BY treatment_color_id";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		switch($row[0]){
			case 1:
				$no_treatment_color=$row[2];
				$no_treatment_color_w3=$row[1];
				break;
			case 2:
				$soil_management_color=$row[2];
				$soil_management_color_w3=$row[1];
				break;
			case 3:
				$pest_control_color=$row[2];
				$pest_control_color_w3=$row[1];
				break;
			case 4:
				$both_treatments_color=$row[2];
				$both_treatments_color_w3=$row[1];
		}
	}
	
	$js_colors="";
	for($i=0;$i<sizeof($colors);$i++){
		if($js_colors==""){
			$js_colors='"'.$colors[$i].','.$color_hex[$i].'"';
		} else {
			$js_colors.=',"'.$colors[$i].','.$color_hex[$i].'"';
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<title>Agroeco Research</title>
<script type="text/javascript">
var colors=[<?php echo($js_colors); ?>];

function getColor(v){
	var ret="";
	for(var i=0;i<colors.length;i++){
		var parts=colors[i].split(",");
		if(v==parts[1]){
			ret=" "+parts[0];
			if(v=="000000"){
				ret+=" w3-text-white";
			} else {
				ret+=" w3-text-black";
			}
			break;
		}
	}
	return ret;
}

function changeSelectClass(n){
	switch(n){
		case 0:
			var e=document.getElementById("no_treatment");
			break;
		case 1:
			var e=document.getElementById("soil_management");
			break;
		case 2:
			var e=document.getElementById("pest_control");
			break;
		case 3:
			var e=document.getElementById("both_treatments");
	}
	var v=e.options[e.selectedIndex].value;
	var c=getColor(v);
	e.className="w3-select"+c;
}
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Treatment colors</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<?php if($no_treatment_color=="000000"){
	$text=" w3-text-white";
} else {
	$text=" w3-text-black";
}
?>
<p><label class="w3-text-green">No treatment:</label><select class="w3-select <?php echo($no_treatment_color_w3.$text); ?>" name="no_treatment" id="no_treatment" onchange="changeSelectClass(0);">
<?php
  $selected="";
  $text="";
  for($i=0;$i<sizeof($colors);$i++){
	  if($color_hex[$i]==$no_treatment_color) {
		  $selected=" selected";
	  } else {
		  $selected="";
	  }
	  if($color_hex[$i]=="000000"){
		  $text=" w3-text-white";
	  } else {
		  $text=" w3-text-black";
	  }
?>
<option class="<?php echo($colors[$i].$text); ?>" value="<?php echo($color_hex[$i]); ?>"<?php echo($selected); ?>><?php echo($color_names[$i]); ?></option>
<?php
}
?>
</select></p>
<?php if($soil_management_color=="000000"){
	$text=" w3-text-white";
} else {
	$text=" w3-text-black";
}
?>
<p><label class="w3-text-green">Soil management:</label><select class="w3-select <?php echo($soil_management_color_w3.$text); ?>" name="soil_management" id="soil_management" onchange="changeSelectClass(1);">
<?php
  $selected="";
  $text="";
  for($i=0;$i<sizeof($colors);$i++){
	  if($color_hex[$i]==$soil_management_color) {
		  $selected=" selected";
	  } else {
		  $selected="";
	  }
	  if($color_hex[$i]=="000000"){
		  $text=" w3-text-white";
	  } else {
		  $text=" w3-text-black";
	  }
?>
<option class="<?php echo($colors[$i].$text); ?>" value="<?php echo($color_hex[$i]); ?>"<?php echo($selected); ?>><?php echo($color_names[$i]); ?></option>
<?php
}
?>
</select></p>
<?php if($pest_control_color=="000000"){
	$text=" w3-text-white";
} else {
	$text=" w3-text-black";
}
?>
<p><label class="w3-text-green">Pest control:</label><select class="w3-select <?php echo($pest_control_color_w3.$text); ?>" name="pest_control" id="pest_control" onchange="changeSelectClass(2);">
<?php
  $selected="";
  $text="";
  for($i=0;$i<sizeof($colors);$i++){
	  if($color_hex[$i]==$pest_control_color) {
		  $selected=" selected";
	  } else {
		  $selected="";
	  }
	  if($color_hex[$i]=="000000"){
		  $text=" w3-text-white";
	  } else {
		  $text=" w3-text-black";
	  }
?>
<option class="<?php echo($colors[$i].$text); ?>" value="<?php echo($color_hex[$i]); ?>"<?php echo($selected); ?>><?php echo($color_names[$i]); ?></option>
<?php
}
?>
</select></p>
<?php if($both_treatments_color=="000000"){
	$text=" w3-text-white";
} else {
	$text=" w3-text-black";
}
?>
<p><label class="w3-text-green">Soil management and pest control:</label><select class="w3-select <?php echo($both_treatments_color_w3.$text); ?>" name="both_treatments" id="both_treatments" onchange="changeSelectClass(3);">
<?php
  $selected="";
  $text="";
  for($i=0;$i<sizeof($colors);$i++){
	  if($color_hex[$i]==$both_treatments_color) {
		  $selected=" selected";
	  } else {
		  $selected="";
	  }
	  if($color_hex[$i]=="000000"){
		  $text=" w3-text-white";
	  } else {
		  $text=" w3-text-black";
	  }
?>
<option class="<?php echo($colors[$i].$text); ?>" value="<?php echo($color_hex[$i]); ?>"<?php echo($selected); ?>><?php echo($color_names[$i]); ?></option>
<?php
}
?>
</select></p>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="update_colors" name="update_colors">Update colors</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="back" name="back">Back</button>
</form><br></div>
</body>
</html>

<?php
} else {
        header("Location: index.php");
}
?>