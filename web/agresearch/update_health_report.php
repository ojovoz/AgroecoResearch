<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

$id=$_GET['id'];
$numbers=$_GET['numbers'];
$values=str_replace(";","#",$_GET['values']);
$values=str_replace("_"," ",$values);

$numbers_list=explode(",",$numbers);
$values_list=explode(",",$values);

for($i=0;$i<sizeof($values_list);$i++){
	$values_list_parts=explode("#",$values_list[$i]);
	for($j=0;$j<sizeof($values_list_parts);$j++){
		if(is_numeric($values_list_parts[$j])){
			$values_list_parts[$j]=getHealthReportItemCategory($dbh,$j,$values_list_parts[$j]);
		}
	}
	$values_list[$i]=implode("#",$values_list_parts);
}


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
	//echo($query);
	$result = mysqli_query($dbh,$query);
} else {
	$_SESSION['values']=$samples_string;
	$values=parseHealthReportValues($dbh,$samples_string);
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
		window.opener.updateHealthReport(values);
		window.close();
	}
</script>
</head>
<?php
if($id==-1){
?>
<body onload="javascript:bye('<?php echo($values); ?>');">
<?php } else {
	// <body onload="javascript:window.close();">
?>	
<body onload="javascript:window.close();">
<?php
}
?>
</body>
</html>