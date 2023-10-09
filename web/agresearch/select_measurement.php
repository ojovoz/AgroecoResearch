<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

$proceed=false;

if(isset($_POST['enter'])){
	$id=$_POST['measurement'];
	$field=$_POST['field'];
	$_SESSION['values']="";
	header("Location: add_lm.php?id=$id&field=$field");
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	$query="SELECT measurement_id, measurement_name, measurement_category, measurement_subcategory FROM measurement ORDER BY measurement_category, measurement_subcategory, measurement_name";
	$result = mysqli_query($dbh,$query);
	$cats="";
	$cats_php="";
	$measurements="";
	$measurements_array=array();
	$last_cat="";
	$default_list="";
	$n=-1;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		if($row[2]!=$last_cat){
			$last_cat=$row[2];
			if($cats==""){
				$cats='"'.$row[2].'"';
				$cats_php=$row[2];
			} else {
				$cats.=',"'.$row[2].'"';
				$cats_php.=",".$row[2];
			}
			if($measurements!=""){
				array_push($measurements_array,$measurements);
				$measurements="";
			}
			$n++;
		}
		if($measurements==""){
			$measurements=$row[0].',"'.$row[1].'","'.$row[3].'"';
		} else {
			$measurements.=','.$row[0].',"'.$row[1].'","'.$row[3].'"';
		}
		if($n==0){
			if($default_list==""){
				$default_list=$row[0].",".$row[1].",".$row[3];
			} else {
				$default_list.=",".$row[0].",".$row[1].",".$row[3];
			}
		}
	}
	if($measurements!=""){
		array_push($measurements_array,$measurements);
	}
	$proceed=true;
}

if($proceed){
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script>
	<?php
	echo("var categories = [".$cats."];\n");
	echo("var measurements = [");
	for($i=0;$i<sizeof($measurements_array);$i++){
		if($i==0){	
			echo("[".$measurements_array[$i]."]");
		} else {
			echo(",[".$measurements_array[$i]."]");
		}
	}
	echo("]\n");
	?>
	
	function updateMeasurementDropdown(){
		var m = document.getElementById("measurement");
		while(m.firstChild){
			m.removeChild(m.firstChild);
		}
		
		var c = document.getElementById("category");
		var cId = c.options[c.selectedIndex].value;
		
		var list = measurements[cId];
		
		var last_category="";
		for(var i=0;i<list.length;i+=3){
			var id = list[i];
			var name = list[i+1];
			var category = list[i+2];
			if(category!=last_category){
				var o = document.createElement("option");
				o.text = category;
				o.value = "";
				o.className = "w3-green w3-text-white";
				o.disabled = true;
				m.add(o);
				last_category=category;
			}
			var o = document.createElement("option");
			o.text = name;
			o.value = id;
			if(i==0){
				o.selected=true;
			}
			m.add(o);
		}
	}
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Add measurement</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p><div class="w3-text-green">
<b>Choose field:</b>
<select class="w3-select w3-text-green" name="field">
<?php
$fields=getFields($dbh);
for($i=0;$i<sizeof($fields);$i++){
	$field=$fields[$i];
	$field_id=$field[0];
	$field_name=$field[1]." R".$field[2];
	echo('<option value="'.$field_id.'">'.$field_name.'</option>');
}
?>
</select>
<b>Choose measurement category:</b>
<select class="w3-select w3-text-green" name="category" id="category" onChange="updateMeasurementDropdown();">
<?php
$cats_array=explode(",",$cats_php);
for($i=0;$i<sizeof($cats_array);$i++){
	echo('<option value="'.$i.'">'.$cats_array[$i].'</option>');
}
?>
</select>
<b>Choose measurement:</b>
<select class="w3-select w3-text-green" name="measurement" id="measurement">
<?php
$default_array=explode(",",$default_list);
$last_category="";
for($i=0;$i<sizeof($default_array);$i+=3){
	$id=$default_array[$i];
	$name=$default_array[$i+1];
	$category=$default_array[$i+2];
	if($category!=$last_category){
		$last_category=$category;
		echo('<option class="w3-green w3-text-white" value="" disabled>'.$category.'</option>');
	}
	if($i==0){
		$selected="selected";
	} else {
		$selected="";
	}
	echo('<option value="'.$id.'" '.$selected.'>'.$name.'</option>');
}
?>
</select>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="enter" name="enter">Enter measurement</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br></form>
<?php
}
?>