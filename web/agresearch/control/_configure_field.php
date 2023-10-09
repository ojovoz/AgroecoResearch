<?php
header("Cache-Control: no-cache, must-revalidate");
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])) {
	$field_id=$_GET['id'];
	$field_name=$_GET['fname'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./../css/w3.css">
<script src="./../includes/processingjs/processing.min.js"></script>
<script>
function loadingTimeout(){
	setTimeout(function(){ document.getElementById("loading").innerHTML="<br>"; },4000);
}
</script>
<title>Agroeco Research</title>
</head>
<body style="background-color: #FFFFFF" onload="loadingTimeout()"><br>
<div align="center" id="loading" class="w3-text-green">Loading field configuration ...</div>
<script type="text/javascript">
	function getFieldName(){
		var name='<?php echo($field_name); ?>';
		return name;
	}
</script>
<div align="center"><canvas data-processing-sources="pjs/field_demo.pde pjs/button.pde"></canvas></div>
</body>
</html>
<?php
} 
?>