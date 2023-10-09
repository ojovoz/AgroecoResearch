<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_activity_applied'])){
		$activity_id=$_POST['activity_id'];
		$aname=$_POST['aname'];
		if(isset($_POST['crop_id']) && $_POST['crop_id']>=0){
			$crop_id=$_POST['crop_id'];
			$query="INSERT INTO activity_x_crop_or_treatment (activity_id, crop_id) VALUES ($activity_id, $crop_id)";
			$result = mysqli_query($dbh,$query);
		}
		if(isset($_POST['treatment_id']) && $_POST['treatment_id']>=0){
			$treatment_id=$_POST['treatment_id'];
			$query="INSERT INTO activity_x_crop_or_treatment (activity_id, treatment_id) VALUES ($activity_id, $treatment_id)";
			$result = mysqli_query($dbh,$query);
		}
		header("Location: activity_applied.php?id=".$activity_id."&aname=".$aname);
	} else if(isset($_POST['cancel'])){
		header("Location: activities.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$activity_id=$_GET['id'];
	$aname=$_GET['aname'];
	if(isset($_GET['rc_id'])){
		$rc_id=$_GET['rc_id'];
		$query="DELETE FROM activity_x_crop_or_treatment WHERE activity_id=$activity_id AND crop_id=$rc_id";
		$result = mysqli_query($dbh,$query);
	} else if(isset($_GET['rt_id'])){
		$rt_id=$_GET['rt_id'];
		$query="DELETE FROM activity_x_crop_or_treatment WHERE activity_id=$activity_id AND treatment_id=$rt_id";
		$result = mysqli_query($dbh,$query);
	}
	$crops=getCrops($dbh,-1);
	$treatments=getTreatments($dbh);
	$query="SELECT crop_id, treatment_id FROM activity_x_crop_or_treatment WHERE activity_id=$activity_id";
	$result = mysqli_query($dbh,$query);
	$crop_ids=array();
	$treatment_ids=array();
	$c=0;
	$t=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		if(!is_null($row[0])){
			$crop_ids[$c]=$row[0];
			$c++;
		} else if(!is_null($row[1])){
			$treatment_ids[$t]=$row[1];
			$t++;
		}
	}
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
<h2 class="w3-green">Activity application</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="activity_id" type="hidden" id="activity_id" value="<? echo($activity_id); ?>">
<input name="aname" type="hidden" id="aname" value="<? echo($aname); ?>">
<p>      
<label class="w3-text-green">Activity <?php echo($aname); ?> applies to:</label><br>
<div class="w3-row-padding"><div class="w3-half w3-text-green"><select class="w3-select w3-text-green" name="crop_id" id="crop_id">
<option value="" disabled selected>Crop</option><?php
	for($i=0;$i<sizeof($crops);$i++){
		$parts=explode(",",$crops[$i]);
		$c_id=$parts[0];
		$c_name=$parts[1];
		if(!in_array($c_id,$crop_ids)){
			echo('<option value="'.$c_id.'">'.$c_name.'</option>');
		}
	}
?></select><br><br><div id="associated_crops"></div><?php
	$a_c="";
	for($i=0;$i<sizeof($crops);$i++){
		$parts=explode(",",$crops[$i]);
		$c_id=$parts[0];
		$c_name=$parts[1];
		if(in_array($c_id,$crop_ids)){
			if($a_c==""){
				$a_c='Associated crops:<br>'.$c_name.' <a href="activity_applied.php?id='.$activity_id.'&mname='.$aname.'&rc_id='.$c_id.'">Remove</a>';
			} else {
				$a_c=$a_c.'<br>'.$c_name.' <a href="activity_applied.php?id='.$activity_id.'&aname='.$aname.'&rc_id='.$c_id.'">Remove</a>';
			}
		}
	}
	if($a_c!=""){
?>
<script type="text/javascript">
	document.getElementById("associated_crops").innerHTML='<?php echo($a_c); ?>';
</script>
<?php		
	}
?></div>
<div class="w3-half w3-text-green"><select class="w3-select w3-text-green" name="treatment_id" id="treatment_id">
<option value="" disabled selected>Treatment</option><?php
	for($i=0;$i<sizeof($treatments);$i++){
		$parts=explode(",",$treatments[$i]);
		$t_id=$parts[0];
		$t_name=$parts[1];
		if(!in_array($t_id,$treatment_ids)){
			echo('<option value="'.$t_id.'">'.$t_name.'</option>');
		}
	}
?></select><br><br><div id="associated_treatments"></div><?php
	$a_t="";
	for($i=0;$i<sizeof($treatments);$i++){
		$parts=explode(",",$treatments[$i]);
		$t_id=$parts[0];
		$t_name=$parts[1];
		if(in_array($t_id,$treatment_ids)){
			if($a_t==""){
				$a_t='Associated treatments:<br>'.$t_name.' <a href="activity_applied.php?id='.$activity_id.'&aname='.$aname.'&rt_id='.$t_id.'">Remove</a>';
			} else {
				$a_t=$a_t.'<br>'.$t_name.' <a href="activity_applied.php?id='.$activity_id.'&aname='.$aname.'&rt_id='.$t_id.'">Remove</a>';
			}
		}
	}
	if($a_t!=""){
?>
<script type="text/javascript">
	document.getElementById("associated_treatments").innerHTML='<?php echo($a_t); ?>';
</script>
<?php		
	}
?></div>
</div></p><br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_activity_applied" name="edit_activity_applied">Associate</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Back</button>
</form><br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>