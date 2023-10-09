<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_user'])){
		$user_name=normalize($_POST['user_name']);
		$user_alias=normalize($_POST['user_alias']);
		$user_password=normalize($_POST['user_password']);
		$user_organization=$_POST['user_organization'];
		if($user_organization==-1){
			$user_organization=normalize($_POST['other_user_organization']);
		} else if ($user_organization!=""){
			$user_organization=$organizations[$user_organization];
		} else {
			$user_organization="";
		}
		$user_role=$_POST['user_role'];
		if($user_role=="") { $user_role=0; }
		$query="INSERT INTO user (user_name, user_alias, user_password, user_organization, user_role) VALUES ('$user_name', '$user_alias', '$user_password', '$user_organization', $user_role)";
		$result = mysqli_query($dbh,$query);
		header("Location: users.php");
	} else if(isset($_POST['cancel'])){
		header("Location: users.php");
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
<h2 class="w3-green">Add user</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">User name:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_name" type="text" maxlength="30"></p>
<p>      
<label class="w3-text-green">User alias:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_alias" type="text" maxlength="10"></p>
<p>      
<label class="w3-text-green">Password:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_password" type="text" maxlength="30"></p>
<p><select class="w3-select w3-text-green" name="user_organization" id="user_organization">
  <option value="" disabled selected>Organization:</option>
<?php
for($i=0;$i<sizeof($organizations);$i++){
	echo('<option value="'.$i.'">'.$organizations[$i].'</option>');
}
?>
  <option value="-1">Other</option>
</select>
<div id="otherfield"></div></p>
<script type="text/javascript">
	document.getElementById("user_organization").onclick = function () {
		if(document.getElementById("user_organization").value=="-1"){
			document.getElementById("otherfield").innerHTML='<label class="w3-text-green">Enter organization:</label><input class="w3-input w3-border-green w3-text-green" name="other_user_organization" type="text" maxlength="40">';
		} else {
			document.getElementById("otherfield").innerHTML='';
		}
    };
</script>
<p><select class="w3-select w3-text-green" name="user_role">
  <option value="" disabled selected>Role:</option>
<?php
for($i=0;$i<sizeof($user_roles);$i++){
	echo('<option value="'.$i.'">'.$user_roles[$i].'</option>');
}
?>
</select></p>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_user" name="add_user">Add user</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>