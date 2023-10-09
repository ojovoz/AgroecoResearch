<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_measurement'])){
		$measurement_id=$_POST['measurement_id'];
		$measurement_name=normalize($_POST['measurement_name']);
		$c=$_POST['measurement_category'];
		if($c=="-1"){
			$measurement_category=normalize($_POST['other_measurement_category']);
		} else {
			$measurement_category=normalize($c);
		}
		$s=$_POST['measurement_subcategory'];
		if($s=="-1"){
			$measurement_subcategory=normalize($_POST['other_measurement_subcategory']);
		} else {
			$measurement_subcategory=normalize($s);
		}
		$measurement_type=$_POST['measurement_type'];
		if($measurement_type==0){
			$measurement_range_min=0;
			$measurement_range_max=0;
			$measurement_units="";
			$measurement_categories=normalize($_POST['measurement_categories']);
		} else if($measurement_type==1) {
			$measurement_range_min=$_POST['measurement_range_min'];
			$measurement_range_max=$_POST['measurement_range_max'];
			$measurement_units=normalize($_POST['measurement_units']);
			$measurement_categories="";
		} else if($measurement_type==2) {
			$measurement_range_min=0;
			$measurement_range_max=0;
			$measurement_units="";
			$measurement_categories="";
		} else {
			$measurement_type=1;
			$measurement_range_min=0;
			$measurement_range_max=0;
			$measurement_units="";
			$measurement_categories="";
		}
		$measurement_periodicity=$_POST['measurement_periodicity'];
		if(isset($_POST['measurement_has_sample_number'])){
			$measurement_has_sample_number=$_POST['measurement_has_sample_number'];
		} else {
			$measurement_has_sample_number=0;
		}
		$measurement_common_complex=$_POST['measurement_common_complex'];
		$measurement_description=normalize($_POST['measurement_description']);
		$query="UPDATE measurement SET measurement_name='$measurement_name', measurement_category='$measurement_category', measurement_subcategory='$measurement_subcategory', measurement_type=$measurement_type, measurement_range_min=$measurement_range_min, measurement_range_max=$measurement_range_max, measurement_units='$measurement_units', measurement_categories='$measurement_categories', measurement_periodicity=$measurement_periodicity, measurement_has_sample_number=$measurement_has_sample_number, measurement_common_complex=$measurement_common_complex, measurement_description='$measurement_description' WHERE measurement_id=$measurement_id";
		$result = mysqli_query($dbh,$query);
		header("Location: measurements.php");
	} else if(isset($_POST['cancel'])){
		header("Location: measurements.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$measurement_id=$_GET['id'];
	$query="SELECT measurement_name, measurement_category, measurement_subcategory, measurement_type, measurement_range_min, measurement_range_max, measurement_units, measurement_categories, measurement_periodicity, measurement_has_sample_number, measurement_common_complex, measurement_description FROM measurement WHERE measurement_id=$measurement_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$measurement_name=$row[0];
		$measurement_category=$row[1];
		$measurement_subcategory=$row[2];
		$measurement_type=$row[3];
		$measurement_range_min=$row[4];
		$measurement_range_max=$row[5];
		$measurement_units=$row[6];
		$measurement_categories=$row[7];
		$measurement_periodicity=$row[8];
		$measurement_has_sample_number=$row[9];
		$measurement_common_complex=$row[10];
		$measurement_description=stripslashes($row[11]);
	}
	$measurement_categories_catalog=getMeasurementCategories($dbh);
	$measurement_subcategories_catalog=getMeasurementSubcategories($dbh);
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
<h2 class="w3-green">Edit measurement</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="measurement_id" type="hidden" id="measurement_id" value="<? echo($measurement_id); ?>">
<p>      
<label class="w3-text-green">Measurement name:</label>
<input class="w3-input w3-border-green w3-text-green" name="measurement_name" type="text" maxlength="100" value="<?php echo("$measurement_name"); ?>"></p>
<p><select class="w3-select w3-text-green" name="measurement_category" id="measurement_category">
  <option value="" disabled>Category:</option>
<?php
for($i=0;$i<sizeof($measurement_categories_catalog);$i++){
	if($measurement_category==$measurement_categories_catalog[$i]){
		echo('<option value="'.$measurement_categories_catalog[$i].'" selected>'.$measurement_categories_catalog[$i].'</option>');
	} else {
		echo('<option value="'.$measurement_categories_catalog[$i].'">'.$measurement_categories_catalog[$i].'</option>');
	}
}
?>
<option value="-1">Other</option>
</select>
<div id="otherfield"></div></p>
<script type="text/javascript">
	document.getElementById("measurement_category").onclick = function () {
		if(document.getElementById("measurement_category").value=="-1"){
			document.getElementById("otherfield").innerHTML='<label class="w3-text-green">Enter category:</label><input class="w3-input w3-border-green w3-text-green" name="other_measurement_category" type="text" maxlength="30">';
		} else {
			document.getElementById("otherfield").innerHTML='';
		}
    };
</script>
<p><select class="w3-select w3-text-green" name="measurement_subcategory" id="measurement_subcategory">
  <option value="" disabled>Subcategory:</option>
<?php
for($i=0;$i<sizeof($measurement_subcategories_catalog);$i++){
	if($measurement_subcategory==$measurement_subcategories_catalog[$i]){
		echo('<option value="'.$measurement_subcategories_catalog[$i].'" selected>'.$measurement_subcategories_catalog[$i].'</option>');
	} else {
		echo('<option value="'.$measurement_subcategories_catalog[$i].'">'.$measurement_subcategories_catalog[$i].'</option>');
	}
}
?>
<option value="-1">Other</option>
</select>
<div id="otherfieldsub"></div></p>
<script type="text/javascript">
	document.getElementById("measurement_subcategory").onclick = function () {
		if(document.getElementById("measurement_subcategory").value=="-1"){
			document.getElementById("otherfieldsub").innerHTML='<label class="w3-text-green">Enter subcategory:</label><input class="w3-input w3-border-green w3-text-green" name="other_measurement_subcategory" type="text" maxlength="40">';
		} else {
			document.getElementById("otherfieldsub").innerHTML='';
		}
    };
</script>
<p><select class="w3-select w3-text-green" name="measurement_type" id="measurement_type">
  <option value="" disabled>Type:</option>
  <option value="0" <?php echo($measurement_type == 0 ? 'selected' : ''); ?>>Qualitative</option>
  <option value="1" <?php echo($measurement_type == 1 ? 'selected' : ''); ?>>Quantitative</option>
  <option value="2" <?php echo($measurement_type == 2 ? 'selected' : ''); ?>>Health report</option>
</select>
<div id="quantiquali"></div></p>
<script type="text/javascript">
	document.getElementById("quantiquali").innerHTML='<?php
		if($measurement_type==0) {
			echo('<label class="w3-text-green">Enter categories (separated by commas):</label><input class="w3-input w3-border-green w3-text-green" name="measurement_categories" type="text" value="'.$measurement_categories.'">');
		} else if($measurement_type==1) {
			echo('<div class="w3-row-padding"><div class="w3-third w3-text-green">Min range:<input class="w3-input w3-border-green w3-text-green" name="measurement_range_min" type="text" value="'.$measurement_range_min.'"></div><div class="w3-third w3-text-green">Max range:<input class="w3-input w3-border-green w3-text-green" name="measurement_range_max" type="text" value="'.$measurement_range_max.'"></div><div class="w3-third w3-text-green">Units:<input class="w3-input w3-border-green w3-text-green" name="measurement_units" type="text" maxlenght="30" value="'.$measurement_units.'"></div></div>');
		}
	?>';
	document.getElementById("measurement_type").onclick = function () {
		if(document.getElementById("measurement_type").value=="0"){
			document.getElementById("quantiquali").innerHTML='<label class="w3-text-green">Enter categories (separated by commas):</label><input class="w3-input w3-border-green w3-text-green" name="measurement_categories" type="text">';
		} else if(document.getElementById("measurement_type").value=="1"){
			document.getElementById("quantiquali").innerHTML='<div class="w3-row-padding"><div class="w3-third w3-text-green">Min range:<input class="w3-input w3-border-green w3-text-green" name="measurement_range_min" type="text"></div><div class="w3-third w3-text-green">Max range:<input class="w3-input w3-border-green w3-text-green" name="measurement_range_max" type="text"></div><div class="w3-third w3-text-green">Units:<input class="w3-input w3-border-green w3-text-green" name="measurement_units" type="text" maxlenght="30"></div></div>';
		} else {
			document.getElementById("quantiquali").innerHTML='';
		}
    };
</script> 
<p><label class="w3-text-green">Periodicity in days: (Enter '0' if variable)</label>
<input class="w3-input w3-border-green w3-text-green" name="measurement_periodicity" type="text" maxlength="10" value="<?php echo($measurement_periodicity); ?>"></p>
<p><label class="w3-validate w3-text-green">Needs sample number</label>
<input class="w3-check" type="checkbox" value="1" name="measurement_has_sample_number" id="measurement_has_sample_number" <?php if($measurement_has_sample_number==1) echo('checked'); ?>></p>
<p><select class="w3-select w3-text-green" name="measurement_common_complex" id="measurement_common_complex">
  <option value="0" <?php echo($measurement_common_complex == 0 ? 'selected' : ''); ?>>Common measurement</option>
  <option value="1" <?php echo($measurement_common_complex == 1 ? 'selected' : ''); ?>>Complex measurement</option>
</select></p>
<p>      
<label class="w3-text-green">Measurement description:</label>
<input class="w3-input w3-border-green w3-text-green" name="measurement_description" type="text" value="<?php echo("$measurement_description"); ?>"></p>
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_measurement" name="edit_measurement">Edit measurement</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>