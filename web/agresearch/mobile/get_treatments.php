<?php
include_once "./../includes/init_database.php";
include_once "./../includes/functions.php";
$dbh = initDB();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])){
	$user_id=$_GET['user_id'];
	$u=getUserRole($dbh,$user_id);
	if($u>=0){
		$df = fopen("php://output", 'w');
		$query="SELECT treatment_id, treatment_name, treatment_category, primary_crop_id, intercropping_crop_id FROM treatment ORDER BY treatment_category, treatment_name";
		$result = mysqli_query($dbh,$query);
		while($row = mysqli_fetch_array($result,MYSQL_NUM)){
			fputcsv($df, $row);			
		}
		fclose($df);
	}
}

?>