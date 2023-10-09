<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();
session_start();

$proceed=false;

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['send'])){
		$sender_id=$_SESSION["user_id"];
		$receiver_id=$_POST['receiver'];
		$date=date('Y-m-d');
		$text=$_POST['message'];
		if(trim($text)!=""){
			$query="INSERT INTO notification (sender_id, receiver_id, notification_date, notification_text) VALUES ($sender_id, $receiver_id, '$date', '$text')";
			$result = mysqli_query($dbh,$query);
		}
		$proceed=true;
	} else if(isset($_POST['menu'])){
		header("Location: menu.php");
		exit();
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$proceed=true;
} 

if($proceed) {
	$current_user=$_SESSION["user_id"];
	$current_user_name=getUserNameFromId($dbh,$current_user);
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
<h2 class="w3-green">Notifications</h2><br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
From:<br><div class="w3-text-green"><?php echo($current_user_name); ?></div><br>
To:
<select class="w3-select w3-text-green" name="receiver" id="receiver">
<option value="-1" selected disabled>Select:</option>
<?php
$query="SELECT user_id, user_name FROM user ORDER BY user_name";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	echo('<option value="'.$row[0].'">'.$row[1].'</option>');
}
?>
</select><br><br>
Message:<br>
<textarea class="w3-input" style="resize:none" name="message" id="message"></textarea><br>
<button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="send" name="send">Send</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="menu" name="menu">Main menu</button></form><br>
Previous messages sent by <?php echo($current_user_name); ?>:<br><br>
<?php
$query="(SELECT user.user_name AS receiver, notification_date AS date, notification_text AS message, notification_sent AS sent, notification_id AS id FROM user, notification WHERE sender_id=$current_user AND user.user_id=receiver_id) UNION (SELECT 'All' AS receiver, notification_date AS date, notification_text AS message, notification_sent AS sent, notification_id AS id FROM notification WHERE sender_id=$current_user AND receiver_id=-1) ORDER BY date DESC";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	$sent = ($row[3]==0) ? "No" : "Yes";
	$id = $row[4];
	$delete_link = ($row[3]==0) ? ' <a class="w3-text-green" href="delete_notification.php?id='.$id.'">Delete</a>' : '';
	echo("To: ".$row[0]."<br>Date: ".$row[1]."<br>Message: ".$row[2]."<br>Received: ".$sent.$delete_link."<br><br>");
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