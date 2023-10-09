<?php
include_once "./../includes/init_database.php";
include_once "./../includes/functions.php";
include_once "./../includes/variables.php";
$dbh = initDB();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_alias']) && isset($_GET['user_password'])){
	$user_alias=$_GET['user_alias'];
	$user_password=$_GET['user_password'];
	$u=validateUser($dbh,$user_alias,$user_password);
	echo($u.",".$email.",".$password.",".$smtp_server.",".$smtp_server_port);
} else {
	echo("-1");
}
?>