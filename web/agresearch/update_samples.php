<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

$id=$_GET['id'];
$numbers=$_GET['numbers'];
$values=$_GET['values'];

$numbers_list=explode(",",$numbers);
$values_list=explode(",",$values);
$samples_string="";
for($i=0;$i<sizeof($numbers_list);$i++){
	if($samples_string==""){
		$samples_string=$numbers_list[$i]."*".$values_list[$i];
	} else {
		$samples_string.="*".$numbers_list[$i]."*".$values_list[$i];
	}
}

if($id>=0){
	$query="UPDATE log SET log_value_text='$samples_string' WHERE log_id=$id";
	$result = mysqli_query($dbh,$query);
} else {
	$_SESSION['values']=$samples_string;
	$values=parseSampleValues($samples_string);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script>
	function bye(values){
		window.opener.updateSamples(values);
		window.close();
	}
</script>
</head>
<?php
if($id==-1){
?>
<body onload="javascript:bye('<?php echo($values); ?>');">
<?php } else {
?>	
<body onload="javascript:window.close();">
<?php
}
?>
</body>
</html>