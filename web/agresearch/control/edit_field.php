<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['edit_field'])){
		$field_id=$_POST['field_id'];
		$user_id=$_SESSION['user_id'];
		$dd = $_POST['dd'];
		$mm = $_POST['mm'];
		$yyyy = $_POST['yyyy'];
		if((!is_numeric($dd)) || (!is_numeric($mm)) || (!is_numeric($yyyy))){
			$field_date_created=date('Y-m-d');
		} else {
			$field_date_created = $yyyy."-".$mm."-".$dd;
		}
		$field_name=normalize($_POST['field_name']);
		$field_lat=$_POST['field_lat'];
		$field_lng=$_POST['field_lng'];
		$query="UPDATE field SET field_date_created='$field_date_created', field_name='$field_name', field_lat='$field_lat', field_lng='$field_lng' WHERE field_id=$field_id";
		$result = mysqli_query($dbh,$query);
		header("Location: fields.php");
	} else if(isset($_POST['cancel'])){
		header("Location: fields.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
	$field_id=$_GET['id'];
	$fname=$_GET['fname'];
	$query="SELECT field_date_created, field_name, field_lat, field_lng FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_date_created=$row[0];
		$date_parts=explode("-",$field_date_created);
		$yy=$date_parts[0];
		$mm=$date_parts[1];
		$dd=$date_parts[2];
		$field_name=$row[1];
		$field_lat=$row[2];
		$field_lng=$row[3];
	}
	
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<link rel="stylesheet" href="./../includes/leaflet/leaflet.css" />
<script src="./../includes/leaflet/leaflet.js"></script>
<title>Agroeco Research</title>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Edit field -- <?php echo($fname); ?></h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input name="field_id" type="hidden" id="field_id" value="<? echo($field_id); ?>">
<p>      
<label class="w3-text-green">Field name:</label>
<input class="w3-input w3-border-green w3-text-green" name="field_name" type="text" maxlength="30" value="<?php echo($field_name); ?>"></p>
<p><label class="w3-text-green">Date created:</label></p>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd">
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
    <select class="w3-select w3-text-green" name="mm">
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
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yyyy" value="<?php echo($yy); ?>">
  </div>
</div>
<p>      
<label class="w3-text-green">Choose location or enter lat / lng:</label>
<div id="map_field" style="height: 400px"></div>
<div class="w3-row-padding">
<div class="w3-half"><input class="w3-input w3-border-green w3-text-green" name="field_lat" id="field_lat" type="text" maxlength="30" value="<?php echo($field_lat); ?>"></div>
<div class="w3-half"><input class="w3-input w3-border-green w3-text-green" name="field_lng" id="field_lng" type="text" maxlength="30" value="<?php echo($field_lng); ?>"></div>
</div>
<script type="text/javascript">
	var ovMapField = L.map('map_field').setView([-10.71667, 38.8], 13);
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1Ijoib2pvdm96IiwiYSI6ImNpcDN2ZGNldzAwMml2d20ycXVjZzFxMjEifQ.qs9pRNpko3M9Nt1XA77S5g', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
			'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery (C) <a href="http://mapbox.com">Mapbox</a>',
		id: 'ojovoz.0b1kh07o',
		accessToken: 'pk.eyJ1Ijoib2pvdm96IiwiYSI6ImNpcDN2ZGNldzAwMml2d20ycXVjZzFxMjEifQ.qs9pRNpko3M9Nt1XA77S5g'
	}).addTo(ovMapField);
	
	var popupDestination = L.popup();
	<?php
	if($field_lat!="" && $field_lng!=""){
	?>
	var coords = L.latLng(<?php echo($field_lat); ?>, <?php echo($field_lng); ?>);
	var latI = coords.lat.toString();
	var lngI = coords.lng.toString();
	popupDestination	
		.setLatLng(coords)
		.setContent("Lat: " + latI + ", Lng:" + lngI)
		.openOn(ovMapField);
	<?php
	}
	?>
	
	function onMapClick(e) {
		var lat;
		var lng;
		lat=e.latlng.lat.toString();
		lng=e.latlng.lng.toString();
		popupDestination
			.setLatLng(e.latlng)
			.setContent("Lat: " + lat + ", Lng:" + lng)
			.openOn(ovMapField);
			
		document.getElementById('field_lat').value=lat;
		document.getElementById('field_lng').value=lng;
	}

	ovMapField.on('click', onMapClick);
</script>
</p>
<br><br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="edit_field" name="edit_field">Edit field</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>