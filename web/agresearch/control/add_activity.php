<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_activity'])){
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
		$query="INSERT INTO activity (activity_name, activity_category, activity_periodicity, activity_measurement_units, activity_description) VALUES ('$activity_name', '$activity_category', $activity_periodicity, '$activity_measurement_units','$activity_description')";
		$result = mysqli_query($dbh,$query);
		header("Location: activities.php");
	} else if(isset($_POST['cancel'])){
		header("Location: activities.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$activity_categories=getActivityCategories($dbh);
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
<h2 class="w3-green">Add activity</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">Activity name:</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_name" type="text" maxlength="100"></p>
<p><select class="w3-select w3-text-green" name="activity_category" id="activity_category">
  <option value="" disabled selected>Category:</option>
<?php
for($i=0;$i<sizeof($activity_categories);$i++){
	echo('<option value="'.$activity_categories[$i].'">'.$activity_categories[$i].'</option>');
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
<input class="w3-input w3-border-green w3-text-green" name="activity_periodicity" type="text" maxlength="10"></p>
<p><label class="w3-text-green">Measurement units</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_measurement_units" type="text" maxlength="30"></p>
<p>      
<label class="w3-text-green">Activity description:</label>
<input class="w3-input w3-border-green w3-text-green" name="activity_description" type="text"></p>
<br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_activity" name="add_activity">Add activity</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>