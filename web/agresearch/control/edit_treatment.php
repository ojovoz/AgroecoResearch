<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_treatment'])){
		$treatment_id=$_POST['id'];
		$treatment_name=$_POST['treatment_name'];
		$c=$_POST['treatment_category'];
		$primary_crop_id="NULL";
		$intercropping_crop_id="NULL";
		if($c=="-1"){
			$treatment_category=normalize($_POST['other_treatment_category']);
		} else {
			$treatment_category=normalize($c);
		}
		if(strcmp($treatment_category,"Intercropping")==0){
			if(isset($_POST['primary_crop'])){
				$primary_crop_id=$_POST['primary_crop'];
			}
			if(isset($_POST['intercropping_crop'])){
				$intercropping_crop_id=$_POST['intercropping_crop'];
			}
		}
		$query="UPDATE treatment SET treatment_name='$treatment_name', treatment_category='$treatment_category', primary_crop_id=$primary_crop_id, intercropping_crop_id=$intercropping_crop_id WHERE treatment_id=$treatment_id";
		$result = mysqli_query($dbh,$query);
		header("Location: treatments.php");
	} else if(isset($_POST['cancel'])){
		header("Location: treatments.php");
	}
} else if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$treatment_id=$_GET['id'];
	$query="SELECT treatment_name, treatment_category, primary_crop_id, intercropping_crop_id FROM treatment WHERE treatment_id=$treatment_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$treatment_name=$row[0];
		$treatment_category=$row[1];
		$primary_crop_id=$row[2];
		$intercropping_crop_id=$row[3];
	} 
	$cropsI=getCrops($dbh,1);
	$cropsNI=getCrops($dbh,0);
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
<h2 class="w3-green">Edit treatment</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="id" type="hidden" id="id" value="<? echo($treatment_id); ?>">
<p>      
<label class="w3-text-green">Treatment name:</label>
<input class="w3-input w3-border-green w3-text-green" name="treatment_name" type="text" maxlength="40" value="<?php echo($treatment_name); ?>"></p>
<p><select class="w3-select w3-text-green" name="treatment_category" id="treatment_category">
<?php
$b=false;
$intercropping = (strcmp($treatment_category,"Intercropping")==0) ? true : false;
for($i=0;$i<sizeof($treatments);$i++){
	if($treatments[$i]==$treatment_category){
		echo('<option value="'.$treatments[$i].'" selected>'.$treatments[$i].'</option>');
		$b=true;
	} else {
		echo('<option value="'.$treatments[$i].'">'.$treatments[$i].'</option>');
	}
}
if($b){
	echo('<option value="-1">Other</option>');
} else {
	echo('<option value="-1" selected>Other</option>');
}
?>
</select>
<div id="otherfield"></div></p>
<script type="text/javascript">
	<?php
		if(!$b){
			echo('document.getElementById("otherfield").innerHTML=\'<label class="w3-text-green">Treatment:</label><input class="w3-input w3-border-green w3-text-green" name="other_treatment_category" type="text" maxlength="40" value="'.$treatment_category.'">\';');
		} 
		if($intercropping){
			echo('document.getElementById("otherfield").innerHTML=\'<select class="w3-select w3-text-green" name="primary_crop" id="primary_crop"><option value="" disabled selected>Primary crop:</option>'); 
			for($i=0;$i<sizeof($cropsNI);$i++){ 
				$parts=explode(",",$cropsNI[$i]);
				$id=$parts[0];
				$name=$parts[1];
				if($id==$primary_crop_id){
					echo('<option value="'.$id.'" selected>'.$name.'</option>');
				} else {
					echo('<option value="'.$id.'">'.$name.'</option>');
				}
			}
			echo('</select><br><br>');
			echo('<select class="w3-select w3-text-green" name="intercropping_crop" id="intercropping_crop"><option value="" disabled selected>Intercropping crop:</option>'); 
			for($i=0;$i<sizeof($cropsI);$i++){ 
				$parts=explode(",",$cropsI[$i]);
				$id=$parts[0];
				$name=$parts[1];
				if($id==$intercropping_crop_id){
					echo('<option value="'.$id.'" selected>'.$name.'</option>');
				} else {
					echo('<option value="'.$id.'">'.$name.'</option>');
				}
			}
			echo('</select><br>\';');
		}
	?>
	
	document.getElementById("treatment_category").onclick = function () {
		if(document.getElementById("treatment_category").value=="Intercropping"){
			document.getElementById("otherfield").innerHTML='<select class="w3-select w3-text-green" name="primary_crop" id="primary_crop"><option value="" disabled selected>Primary crop:</option><?php 
			for($i=0;$i<sizeof($cropsNI);$i++){ 
				$parts=explode(",",$cropsNI[$i]);
				$id=$parts[0];
				$name=$parts[1];
			?><option value="<?php echo($id); ?>"><?php echo($name); ?></option><?php
			} ?></select><br><br><select class="w3-select w3-text-green" name="intercropping_crop" id="intercropping_crop"><option value="" disabled selected>Intercropping crop:</option><?php 
			for($i=0;$i<sizeof($cropsI);$i++){ 
				$parts=explode(",",$cropsI[$i]);
				$id=$parts[0];
				$name=$parts[1];
			?><option value="<?php echo($id); ?>"><?php echo($name); ?></option><?php
			} ?></select><br>';
		} else if(document.getElementById("treatment_category").value=="-1") {
			document.getElementById("otherfield").innerHTML='<label class="w3-text-green">Enter treatment name:</label><input class="w3-input w3-border-green w3-text-green" name="other_treatment_category" type="text" maxlength="40">';
		} else {
			document.getElementById("otherfield").innerHTML='';
		}
    };
</script>
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_treatment" name="edit_treatment">Edit treatment</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>