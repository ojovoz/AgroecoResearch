<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

$success=false;

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['login'])){
		$user_alias=$_POST['user_alias'];
		$user_password=$_POST['user_password'];
		$u=validateUser($dbh,$user_alias,$user_password);
		if($u!=-1){
			$parts=explode(",",$u);
			if($parts[1]==2){
				$success=true;
				$_SESSION['admin']=true;
				$_SESSION['superadmin']=true;
				$_SESSION['collector']=false;
				$_SESSION['user_id']=$parts[0];
				header("Location: menu.php");
			} else if($parts[1]==1) {
				$success=true;
				$_SESSION['admin']=true;
				$_SESSION['superadmin']=false;
				$_SESSION['collector']=false;
				$_SESSION['user_id']=$parts[0];
				header("Location: menu.php");
			} else if($parts[1]==0) {
				$success=true;
				$_SESSION['admin']=true;
				$_SESSION['superadmin']=false;
				$_SESSION['collector']=true;
				$_SESSION['user_id']=$parts[0];
				header("Location: menu.php");
			}
		}
	}
} 

if(!$success){
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Login</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">User alias:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_alias" type="text" maxlength="10"></p>
<p>      
<label class="w3-text-green">Password:</label>
<input class="w3-input w3-border-green w3-text-green" name="user_password" type="password" maxlength="30"></p>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="login" name="login">Log in</button></form><br><br>
</div>
</body>
</html>
<?php
}
?>