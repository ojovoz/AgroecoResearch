<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_activity'])){
		$activity_id=$_POST['activity_id'];
		$activity_name=normalize($_POST['activity_name']);
		$c=$_POST['activity_category'];
		if($c=="-1"){
			$activity_category=normalize($_POST['other_activity_category']);
		} else {
			$activity_category=normalize($c);
		}
		$activity_periodicity=$_POST['activity_periodicity'];
		$activity_measurement_units=$_POST['activity_measurement_units'];
		$activity_description=normalize($_POST['activity_description']);
		$query="UPDATE activity SET activity_name='$activity_name', activity_category='$activity_category', activity_periodicity=$activity_periodicity, activity_measurement_units='$activity_measurement_units', activity_description='$activity_description' WHERE activity_id=$activity_id";
		$result = mysqli_query($dbh,$query);
		header("Location: activities.php");
	} else if(isset($_POST['cancel'])){
		header("Location: activities.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$activity_id=$_GET['id'];
	$query="SELECT activity_name, activity_category, activity_periodicity, activity_measurement_units, activity_description FROM activity WHERE activity_id=$activity_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$activity_name=$row[0];
		$activity_category=$row[1];
		$activity_periodicity=$row[2];
		$activity_measurement_units=$row[3];
		$activity_description=stripslashes($row[4]);
	}
	$activity_categories_catalog=getActivityCategories($dbh);
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
<h2 class="w3-green">Edit activity</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="activity_id" type="hidden" id="activity_id" value="<? echo($activity_id); ?>">
<p>      
<label class="w3-text-green">Activity name:</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_name" type="text" maxlength="100" value="<?php echo("$activity_name"); ?>"></p>
<p><select class="w3-select w3-text-green" name="activity_category" id="activity_category">
  <option value="" disabled>Category:</option>
<?php
for($i=0;$i<sizeof($activity_categories_catalog);$i++){
	if($activity_category==$activity_categories_catalog[$i]){
		echo('<option value="'.$activity_categories_catalog[$i].'" selected>'.$activity_categories_catalog[$i].'</option>');
	} else {
		echo('<option value="'.$activity_categories_catalog[$i].'">'.$activity_categories_catalog[$i].'</option>');
	}
}
?>
<option value="-1">Other</option>
</select>
<div id="otherfield"></div></p>
<script type="text/javascript">
	document.getElementById("activity_category").onclick = function () {
		if(document.getElementById("activity_category").value=="-1"){
			document.getElementById("otherfield").innerHTML='<label class="w3-text-green">Enter category:</label><input class="w3-input w3-border-green w3-text-green" name="other_activity_category" type="text" maxlength="30">';
		} else {
			document.getElementById("otherfield").innerHTML='';
		}
    };
</script>
<p><label class="w3-text-green">Periodicity in days: (Enter '0' if variable)</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_periodicity" type="text" maxlength="10" value="<?php echo($activity_periodicity); ?>"></p>
<p><label class="w3-text-green">Measurement units:</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_measurement_units" type="text" maxlength="30" value="<?php echo($activity_measurement_units); ?>"></p>
<p>      
<label class="w3-text-green">Activity description:</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_description" type="text" value="<?php echo("$activity_description"); ?>"></p>
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_activity" name="edit_activity">Edit activity</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>