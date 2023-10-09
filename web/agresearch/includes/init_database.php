<?
//initialize dbConnection
function initDB() {
	$host="localhost";
	$db="agresearch";
	$db_user="xxx";
	$db_pass="xxx";
	$dbh=mysqli_connect($host, $db_user, $db_pass, $db);
	return $dbh;
}

//
?>