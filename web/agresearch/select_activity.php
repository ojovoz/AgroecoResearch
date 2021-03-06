<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

$proceed=false;

if(isset($_POST['enter'])){
	$id=$_POST['activity'];
	$field=$_POST['field'];
	header("Location: add_la.php?id=$id&field=$field");
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	$query="SELECT activity_id, activity_name, activity_category FROM activity ORDER BY activity_category, activity_name";
	$result_activities = mysqli_query($dbh,$query);
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
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Add activity</h2>
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
<b>Choose activity:</b>
<select class="w3-select w3-text-green" name="activity">
  <option class="w3-green w3-text-white" value="" disabled>Crops</option>
<?php
$last_category="";
while($row=mysqli_fetch_array($result_activities,MYSQL_NUM)){
	$id=$row[0];
	$name=$row[1];
	$category=$row[2];
	if($category!=$last_category){
		$last_category=$category;
		echo('<option class="w3-green w3-text-white" value="" disabled>'.$category.'</option>');
	}
	echo('<option value="'.$id.'">'.$name.'</option>');
}
?>
</select>
</div>
</p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="enter" name="enter">Enter activity</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br></form>
<?php
}
?>