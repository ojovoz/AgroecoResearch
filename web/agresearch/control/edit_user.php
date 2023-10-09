<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_user'])){
		$user_id=$_POST['id'];
		$user_name=normalize($_POST['user_name']);
		$user_alias=normalize($_POST['user_alias']);
		$user_password=normalize($_POST['user_password']);
		$user_organization=$_POST['user_organization'];
		if($user_organization==-1){
			$user_organization=normalize($_POST['other_user_organization']);
		} else {
			$user_organization=$organizations[$user_organization];
		}
		$user_role=$_POST['user_role'];
		$query="UPDATE user SET user_name='$user_name', user_alias='$user_alias', user_password='$user_password', user_organization='$user_organization', user_role=$user_role WHERE user_id=$user_id";
		$result = mysqli_query($dbh,$query);
		header("Location: users.php");
	} else if(isset($_POST['cancel'])){
		header("Location: users.php");
	}
} else if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$user_id=$_GET['id'];
	$query="SELECT user_name, user_alias, user_password, user_organization, user_role FROM user WHERE user_id=$user_id";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$user_name=$row[0];
		$user_alias=$row[1];
		$user_password=$row[2];
		$user_organization=$row[3];
		$user_role=$row[4];
	}
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
<h2 class="w3-green">Edit user</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="id" type="hidden" id="id" value="<? echo($user_id); ?>">
<p>      
<label class="w3-text-green">User name:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_name" type="text" maxlength="30" value="<?php echo($user_name); ?>"></p>
<p>      
<label class="w3-text-green">User alias:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_alias" type="text" maxlength="10" value="<?php echo($user_alias); ?>"></p>
<p>      
<label class="w3-text-green">Password:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_password" type="text" maxlength="30" value="<?php echo($user_password); ?>"></p>
<p><select class="w3-select w3-text-green" name="user_organization" id="user_organization">
<?php
$b=false;
for($i=0;$i<sizeof($organizations);$i++){
	if($organizations[$i]==$user_organization){
		echo('<option value="'.$i.'" selected>'.$organizations[$i].'</option>');
		$b=true;
	} else {
		echo('<option value="'.$i.'">'.$organizations[$i].'</option>');
	}
}
if($b){
	echo('<option value="-1">Other organization</option>');
} else {
	echo('<option value="-1" selected>Organization: other</option>');
}
?>
</select>
<div id="otherfield"></div></p>
<script type="text/javascript">
	<?php
		if(!$b){
			echo('document.getElementById("otherfield").innerHTML=\'<label class="w3-text-green">Organization:</label><input class="w3-input w3-border-green w3-text-green" name="other_user_organization" type="text" maxlength="40" value="'.$user_organization.'">\';');
		}
	?>
	document.getElementById("user_organization").onclick = function () {
		if(document.getElementById("user_organization").value=="-1"){
			document.getElementById("otherfield").innerHTML='<label class="w3-text-green">Enter organization:</label><input class="w3-input w3-border-green w3-text-green" name="other_user_organization" type="text" maxlength="40" value="<?php echo($user_organization); ?>">';
		} else {
			document.getElementById("otherfield").innerHTML='';
		}
    };
</script>
<p><select class="w3-select w3-text-green" name="user_role">
<?php
for($i=0;$i<sizeof($user_roles);$i++){
	if($user_role==$i){
		echo('<option value="'.$i.'" selected>'.$user_roles[$i].'</option>');
	} else {
		echo('<option value="'.$i.'">'.$user_roles[$i].'</option>');
	}
}
?>
</select></p>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_user" name="edit_user">Edit user</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>