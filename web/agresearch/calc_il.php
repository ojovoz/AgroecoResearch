<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true){
	if(isset($_GET['id']) && isset($_GET['task'])){
		$id=$_GET['id'];
		$task=$_GET['task'];
		$query="SELECT crop_id, treatment_id FROM input_log WHERE input_log_id=$id";
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			if($row[0]>0){
				if($task=="details"){
					header("Location: details_ilc.php?id=$id");
				} else {
					header("Location: edit_ilc.php?id=$id");
				}
			} else if($row[1]>0){
				if($task=="details"){
					header("Location: details_ilt.php?id=$id");
				} else {
					header("Location: edit_ilt.php?id=$id");
				} 
			}
		} 
	} 
} 
?>