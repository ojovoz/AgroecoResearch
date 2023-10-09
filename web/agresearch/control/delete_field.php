<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['delete_field'])){
		$field_id=$_POST['id'];
		$query="DELETE FROM field WHERE field_id=$field_id";
		$result = mysqli_query($dbh,$query);
		header("Location: fields.php");
	} else if(isset($_POST['delete_field_replications'])){
		$field_id=$_POST['id'];
		$parent_field_id=getParentField($field_id,$dbh);
		$query="DELETE FROM field WHERE parent_field_id=$parent_field_id";
		$result = mysqli_query($dbh,$query);
		header("Location: fields.php");
	} else if(isset($_POST['cancel'])){
		header("Location: fields.php");
	}
} else if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$field_id=$_GET['id'];
	$fname=$_GET['fname'];
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
<h2 class="w3-green">Delete field</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="id" type="hidden" id="id" value="<? echo($field_id); ?>">
<span class="w3-text-red">Are you sure you want to delete field <?php echo($fname) ?>?</span><br><br>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="delete_field" name="delete_field">Delete field</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="delete_field_replications" name="delete_field_replications">Delete field and replications</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button>
</form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>