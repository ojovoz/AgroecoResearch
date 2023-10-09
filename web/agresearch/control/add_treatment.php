<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_treatment'])){
		$treatment_name=$_POST['treatment_name'];
		$c=$_POST['treatment_category'];
		$primary_crop_id="NULL";
		$intercropping_crop_id="NULL";
		if($c=="-1"){
			$treatment_category=normalize($_POST['other_treatment_category']);
		} else if($c=="Intercropping"){
			$treatment_category=$c;
			if(isset($_POST['primary_crop'])){
				$primary_crop_id=$_POST['primary_crop'];
			}
			if(isset($_POST['intercropping_crop'])){
				$intercropping_crop_id=$_POST['intercropping_crop'];
			}
		} else {
			$treatment_category=$c;
		}
		$query="INSERT INTO treatment (treatment_name, treatment_category, primary_crop_id, intercropping_crop_id) VALUES ('$treatment_name', '$treatment_category', $primary_crop_id, $intercropping_crop_id)";
		$result = mysqli_query($dbh,$query);
		header("Location: treatments.php");
	} else if(isset($_POST['cancel'])){
		header("Location: treatments.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
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
<h2 class="w3-green">Add treatment</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">Treatment name:</label>
<input class="w3-input w3-border-green w3-text-green" name="treatment_name" type="text" maxlength="40"></p>
<p><select class="w3-select w3-text-green" name="treatment_category" id="treatment_category">
  <option value="" disabled selected>Category:</option>
<?php
for($i=0;$i<sizeof($treatments);$i++){
	echo('<option value="'.$treatments[$i].'">'.$treatments[$i].'</option>');
}
?>
  <option value="-1">Other</option>
</select>
<div id="otherfield"></div></p>
<script type="text/javascript">
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
			document.getElementById("otherfield").innerHTML='<label class="w3-text-green">Enter treatment category:</label><input class="w3-input w3-border-green w3-text-green" name="other_treatment_category" type="text" maxlength="40">';
		} else {
			document.getElementById("otherfield").innerHTML='';
		}
    };
</script>
<br><p>      
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_treatment" name="add_treatment">Add treatment</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>