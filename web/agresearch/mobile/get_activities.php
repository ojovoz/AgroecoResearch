<?php
include_once "./../includes/init_database.php";
include_once "./../includes/functions.php";
$dbh = initDB();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])){
	$user_id=$_GET['user_id'];
	$u=getUserRole($dbh,$user_id);
	if($u>=0){
		$df = fopen("php://output", 'w');
		$query="SELECT activity_id, activity_name, activity_category, activity_periodicity, activity_measurement_units, activity_description FROM activity ORDER BY activity_category, activity_name";
		$result = mysqli_query($dbh,$query);
		while($row = mysqli_fetch_array($result,MYSQL_NUM)){
			fputcsv($df, $row);			
		}
		fclose($df);
	}
}

?>