<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['add'])){
	
	$field_id=$_POST['field_id'];
	$user_id=$_SESSION['user_id'];
	$dd=$_POST['dd'];
	$mm=$_POST['mm'];
	$yyyy=$_POST['yyyy'];
	$date=$yyyy."-".$mm."-".$dd;
	$comment=preg_replace("/\r\n|\r|\n/",'<br/>',$_POST['comment']);
	$category=$_POST['category'];
	if(isset($_FILES['image']['name'])){
		$image_file=$_FILES['image']['name'];
		$upload = "images/".$image_file;
		if(is_uploaded_file($_FILES['image']['tmp_name'])) {
			move_uploaded_file($_FILES['image']['tmp_name'],$upload);
			$image=$upload;
		} else {
			$image="";
		}
	} else {
		$image="";
	}
	
	$query="INSERT INTO general_observation (field_id, user_id, category, date, comments, image) VALUES ($field_id, $user_id,'$category','$date','$comment','$image')";
	$result = mysqli_query($dbh,$query);

	echo "<script type='text/javascript'>";
	echo "window.opener.location.reload(false);";
	echo "window.close();";
	echo "</script>";
	
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$yy=date('Y');
	$mm=date('m');
	$dd=date('d');
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script language="Javascript">
       <!--
		function validateForm(){
			
			var day = parseInt(document.getElementById("dd").value,10);
			var month = parseInt(document.getElementById("mm").value,10);
			var year = parseInt(document.getElementById("yyyy").value);
			var today = new Date();
			if(year<2017 || year>parseInt(today.getFullYear())){
				alert("Date out of valid range");
				return false;
			} else {
				var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;
				if(day > monthLength[month - 1]){
					alert("Invalid date");
					return false;
				} else {
					var newDate = new Date(document.getElementById("mm").value+"/"+document.getElementById("dd").value+"/"+ document.getElementById("yyyy").value);
					if(newDate > today){
						alert("Date must be in the past");
						return false;
					}
				}
			}
			
			var category = document.getElementById("category").value;
			if(category==""){
				alert("You must choose a category");
				return false;
			}
			
			var comment = document.getElementById("comment").value;
			if(comment==""){
				alert("You must enter comments");
				return false;
			}
			
			var field = document.getElementById("field_id").value;
			if(field==""){
				alert("You must choose a field");
				return false;
			}
	   }
	   
	   function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
       //-->
</script>

</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Add comment</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm();">
<p><div class="w3-text-green">
<b>Entered by:</b> <?php echo(getUserNameFromId($dbh,$_SESSION["user_id"])); ?><br>
<select class="w3-select w3-text-green" name="field_id" id="field_id">
  <option value="" disabled selected>Select field:</option>
<?php
$fields=getFields($dbh);
$field_aggregate="";
$prev_field="";
for($i=0;$i<sizeof($fields);$i++){
	$field=$fields[$i];
	if($field[1]!=$prev_field){
		$prev_field=$field[1];
		echo('<option value="'.$field[0].'">'.$prev_field.'</option>');
		
	}
}
?>  
</select><br>
<b>Date:</b>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd" id="dd">
		<option value="" disabled>Day</option>
		<?php
		for($i=1;$i<=31;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			if($n==$dd){
				$selected=" selected";
			} else {
				$selected="";
			}
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="mm" id="mm">
		<option value="" disabled>Month</option>
		<?php
		for($i=1;$i<=12;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			if($n==$mm){
				$selected=" selected";
			} else {
				$selected="";
			}
			echo('<option value="'.$n.'"'.$selected.'>'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yyyy" id="yyyy" value="<?php echo($yy); ?>" onkeypress="return isNumberKey(event)">
  </div>
</div><br>
<b>Comment:</b><br><textarea class="w3-input" style="resize:none" name="comment" id="comment"></textarea><br>
<select class="w3-select w3-text-green" name="category" id="category">
  <option value="" disabled selected>Category:</option>
<?php
for($i=0;$i<sizeof($comment_categories);$i++){
	echo('<option value="'.$comment_categories[$i].'">'.$comment_categories[$i].'</option>');
}
?>
</select><br>
<b>Photo:</b> <input class="w3-input w3-border-green w3-text-green" name="image" type="file" id="image" accept=".jpg,.png">
</div></p>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="add" name="add">Add</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br></form></div>
</body>
</html>
<?php
} 
?>