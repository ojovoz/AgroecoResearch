<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_measurement'])){
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
		if($measurement_periodicity==""){
			$measurement_periodicity=0;
		}
		if(isset($_POST['measurement_has_sample_number'])){
			$measurement_has_sample_number=$_POST['measurement_has_sample_number'];
		} else {
			$measurement_has_sample_number=0;
		}
		$measurement_common_complex=$_POST['measurement_common_complex'];
		$measurement_description=normalize($_POST['measurement_description']);
		$query="INSERT INTO measurement (measurement_name, measurement_category, measurement_subcategory, measurement_type, measurement_range_min, measurement_range_max, measurement_units, measurement_categories, measurement_has_sample_number, measurement_periodicity, measurement_common_complex, measurement_description) VALUES ('$measurement_name', '$measurement_category', '$measurement_subcategory', $measurement_type, $measurement_range_min, $measurement_range_max, '$measurement_units', '$measurement_categories', $measurement_has_sample_number, $measurement_periodicity, $measurement_common_complex, '$measurement_description')";
		$result = mysqli_query($dbh,$query);
		header("Location: measurements.php");
	} else if(isset($_POST['cancel'])){
		header("Location: measurements.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$measurement_categories=getMeasurementCategories($dbh);
	$measurement_subcategories=getMeasurementSubcategories($dbh);
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
<h2 class="w3-green">Add measurement</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">Measurement name:</label>
<input class="w3-input w3-border-green w3-text-green" name="measurement_name" type="text" maxlength="100"></p>
<p><select class="w3-select w3-text-green" name="measurement_category" id="measurement_category">
  <option value="" disabled selected>Category:</option>
<?php
for($i=0;$i<sizeof($measurement_categories);$i++){
	echo('<option value="'.$measurement_categories[$i].'">'.$measurement_categories[$i].'</option>');
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
  <option value="" disabled selected>Subcategory:</option>
<?php
for($i=0;$i<sizeof($measurement_subcategories);$i++){
	echo('<option value="'.$measurement_subcategories[$i].'">'.$measurement_subcategories[$i].'</option>');
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
  <option value="" disabled selected>Type:</option>
  <option value="0">Qualitative</option>
  <option value="1">Quantitative</option>
  <option value="2">Health report</option>
</select>
<div id="quantiquali"></div></p>
<script type="text/javascript">
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
<input class="w3-input w3-border-green w3-text-green" name="measurement_periodicity" type="text" maxlength="10"></p>
<p><label class="w3-validate w3-text-green">Needs sample number</label>
<input class="w3-check" type="checkbox" value="1" name="measurement_has_sample_number" id="measurement_has_sample_number"></p>
<p><select class="w3-select w3-text-green" name="measurement_common_complex" id="measurement_common_complex">
  <option value="0" selected>Common measurement</option>
  <option value="1">Complex measurement</option>
</select></p>
<p>      
<label class="w3-text-green">Measurement description:</label>
<input class="w3-input w3-border-green w3-text-green" name="measurement_description" type="text"></p>
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_measurement" name="add_measurement">Add measurement</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>