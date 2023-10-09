<?php
include_once "./../includes/init_database.php";
include_once "./../includes/functions.php";
$dbh = initDB();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])){
	$user_id=$_GET['user_id'];
	$u=getUserRole($dbh,$user_id);
	if($u>=0){
		$df = fopen("php://output", 'w');
		$query="SELECT field_id, parent_field_id, user_id, field_date_created, field_name, field_replication_number, field_lat, field_lng, field_configuration FROM field WHERE field_is_active=1 ORDER BY field_name, field_replication_number";
		$result = mysqli_query($dbh,$query);
		while($row = mysqli_fetch_array($result,MYSQL_NUM)){
			fputcsv($df, $row);		
		}
		fclose($df);
	}
}

?>