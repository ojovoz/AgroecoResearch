<?php
include_once "./../includes/init_database.php";
include_once "./../includes/functions.php";
$dbh = initDB();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])){
	$user_id=$_GET['user_id'];
	$df = fopen("php://output", 'w');
	$query="SELECT notification_id, $user_id, user_name, notification_date, notification_text FROM user, notification WHERE (receiver_id=$user_id OR receiver_id=-1) AND notification_sent=0 AND user_id=sender_id ORDER BY notification_date DESC";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		markNotificationAsSent($dbh,$row[0]);
		fputcsv($df, $row);			
	}
	fclose($df);

}

?>