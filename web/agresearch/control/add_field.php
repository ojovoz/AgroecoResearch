<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "./../includes/init_database.php";
include_once "./../includes/variables.php";
include_once "./../includes/functions.php";
$dbh = initDB();

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['add_field'])){
		$user_id=$_SESSION['user_id'];
		$n_replications=$_POST['n_replications'];
		if(!is_numeric($n_replications)){
			$n_replications=1;
		}
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
		$parent_field_id=-1;
		for($i=1;$i<=$n_replications;$i++){
			if($i==1){
				$query="INSERT INTO field (user_id, field_date_created, field_name, field_replication_number, field_lat, field_lng) VALUES ($user_id, '$field_date_created', '$field_name', $i, '$field_lat', '$field_lng')";
				$result = mysqli_query($dbh,$query);
				$parent_field_id=mysqli_insert_id($dbh);
				$query="UPDATE field SET parent_field_id=$parent_field_id WHERE field_id=$parent_field_id";
				$result = mysqli_query($dbh,$query);
			} else {
				$query="INSERT INTO field (parent_field_id, user_id, field_date_created, field_name, field_replication_number, field_lat, field_lng) VALUES ($parent_field_id, $user_id, '$field_date_created', '$field_name', $i, '$field_lat', '$field_lng')";
				$result = mysqli_query($dbh,$query);
			}
		}
		header("Location: fields.php");
	} else if(isset($_POST['cancel'])){
		header("Location: fields.php");
	}
} else if(isset($_SESSION['admin']) && $_SESSION['admin']==true) {
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
<h2 class="w3-green">Add field</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<p>      
<label class="w3-text-green">Field name:</label>
<input class="w3-input w3-border-green w3-text-green" name="field_name" type="text" maxlength="30"></p>
<p><label class="w3-text-green">Date created:</label></p>
<div class="w3-row-padding">
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="dd">
		<option value="" disabled selected>Day</option>
		<?php
		for($i=1;$i<=31;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			echo('<option value="'.$n.'">'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <select class="w3-select w3-text-green" name="mm">
		<option value="" disabled selected>Month</option>
		<?php
		for($i=1;$i<=12;$i++){
			if($i<10){
				$n="0".$i;
			} else {
				$n=$i;
			}
			echo('<option value="'.$n.'">'.$n.'</option>');
		}
		?>
	</select>
  </div>
  <div class="w3-third">
    <input class="w3-input w3-border-teal w3-text-green" type="text" name="yyyy" value="<?php echo(date('Y')); ?>">
  </div>
</div>
<p>      
<label class="w3-text-green">Choose location or enter lat / lng:</label>
<div id="map_field" style="height: 400px"></div>
<div class="w3-row-padding">
<div class="w3-half"><input class="w3-input w3-border-green w3-text-green" name="field_lat" id="field_lat" type="text" maxlength="30" value="-10.71667"></div>
<div class="w3-half"><input class="w3-input w3-border-green w3-text-green" name="field_lng" id="field_lng" type="text" maxlength="30" value="38.8"></div>
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
	var coords = L.latLng(-10.71667, 38.8);
	var latI = coords.lat.toString();
	var lngI = coords.lng.toString();
	popupDestination	
		.setLatLng(coords)
		.setContent("Lat: " + latI + ", Lng:" + lngI)
		.openOn(ovMapField);
	
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
<p><select class="w3-select w3-text-green" name="n_replications">
  <option value="" disabled selected>Number of replications:</option>
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
</select></p>
<br><br><button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="add_field" name="add_field">Add field</button> <button class="w3-button w3-padding-large w3-green w3-round w3-border w3-border-green" id="cancel" name="cancel">Cancel</button></form><br>
<br><br></div>
</body>
</html>
<?php
} else {
        header("Location: index.php");
}
?>