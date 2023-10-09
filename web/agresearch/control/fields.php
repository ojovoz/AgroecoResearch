<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
$dbh = initDB();
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_field'])){
		header("Location: add_field.php");
	} else if(isset($_POST['menu'])){
		header("Location: menu.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
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
<h2 class="w3-green">Fields</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_field" name="add_field">Add field</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="menu" name="menu">Main menu</button></form><br>
<?php
$query="SELECT field_id, field_name, field_replication_number, field_date_created FROM field WHERE field_is_active = 1 ORDER BY field_name, field_replication_number";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	$fname="Field: ".$row[1]." -- Replication ".$row[2];
	echo($fname."<br>");
	echo("Created/Modified: ".$row[3]."<br>");
?><a class="w3-text-green" href="edit_field.php?id=<?php echo($row[0]); ?>&fname=<?php echo($fname); ?>">Edit</a> -- <a class="w3-text-green" href="delete_field.php?id=<?php echo($row[0]); ?>&fname=<?php echo($fname); ?>">Delete</a> -- <a class="w3-text-green" href="configure_field.php?id=<?php echo($row[0]); ?>&fname=<?php echo($fname); ?>">Configure</a><br><br>
<?php
}
?>
<br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>