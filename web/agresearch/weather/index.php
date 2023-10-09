<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_POST['menu'])){
	header("Location: ./../menu.php");
} else if(isset($_POST['download'])){
	$id=$_POST['weather_data_file'];
	if($id>0){
		$query="SELECT filename FROM weather_data WHERE weather_data_id=$id";
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			$filename="files/".$row[0];
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($filename).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			flush(); // Flush system output buffer
			readfile($filename);
		}
	}
} else if(isset($_POST['upload'])){
	$field=$_POST['field'];
	$field_id=getFieldIdFromName($dbh,$field);
	if(isset($_FILES['uploaded_file']['name'])){
		$filename=$_FILES['uploaded_file']['name'];
		$filename_parts=explode(".",$filename);
		$filename_parts[0]=$filename_parts[0].getMaxWeatherFilenameId($dbh);
		$filename=implode(".",$filename_parts);
		$upload = "files/".$filename;
		if(is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {
			move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$upload);
			$dates=getStartEndDatesFromWeatherDataFile($upload);
			if($dates!=""){
				$date_parts=explode(",",$dates);
				$start_date=date('Y-m-d',strtotime($date_parts[0]));
				$end_date=date('Y-m-d',strtotime($date_parts[1]));
			} else {
				$start_date=date('Y-m-d');
				$end_date=date('Y-m-d');
			}
			$query="INSERT INTO weather_data(field_id, start_date, end_date, filename) VALUES ($field_id, '$start_date', '$end_date', '$filename')";
			$result = mysqli_query($dbh,$query);
		} 
	}
} 

if(isset($_SESSION['admin']) && $_SESSION['admin']==true){

	$query="SELECT weather_data_id, field_name, start_date, end_date FROM weather_data, field WHERE field.field_id = weather_data.field_id ORDER BY field_name ASC, start_date DESC";
	$result = mysqli_query($dbh,$query);

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
<h2 class="w3-green">Weather data</h2>
<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm()">
<label class="w3-text-green">Available weather data files:</label>
<select class="w3-select w3-text-green" name="weather_data_file" id="weather_data_file" size="10">
<?php
$prev_field="";
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	if($row[1]!=$prev_field){
		echo('<option class="w3-green w3-text-white" value="" disabled>'.$row[1].'</option>');
		$prev_field=$row[1];
	}
	echo('<option class="w3-text-green" value="'.$row[0].'">Data from '.$row[2].' to '.$row[3].'</option>');
}
?>
</select><br><br>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="download" name="download">Download selected</button><br><br>
<h4 class="w3-green">Upload a weather data file</h4>
<select class="w3-select w3-text-green" name="field" id="field">
<option value="" disabled selected>Choose field:</option>
<?php
$query="SELECT COUNT(field_id), field_name FROM field GROUP BY field_name ORDER BY field_name";
$result = mysqli_query($dbh,$query);
while($row = mysqli_fetch_array($result,MYSQL_NUM)){
	echo('<option value="'.$row[1].'">'.$row[1].'</option>');
}
?>
</select><br><br>
<label class="w3-text-green">File:</label> <input class="w3-input w3-border-green w3-text-green" name="uploaded_file" type="file" id="uploaded_file" accept=".txt"><br>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="upload" name="upload">Upload</button><br><br>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="menu" name="menu">Menu</button><br><br>
</form></div>
</body>
</html>
<?php
}
?>