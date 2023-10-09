<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/init_database.php";
include_once "includes/variables.php";
include_once "includes/functions.php";
$dbh = initDB();
session_start();

if(isset($_SESSION['admin']) && $_SESSION['admin']==true && isset($_GET['id'])){
	$id=$_GET['id'];
	$m_id=$_GET['m_id'];
	if($id>=0){
		$query="SELECT log_value_text FROM log WHERE log_id=$id";
		$result = mysqli_query($dbh,$query);
		$row = mysqli_fetch_array($result,MYSQL_NUM);
	
		$samples=$row[0];
	} else {
		$samples=$_SESSION['values'];
	}
	
	$query="SELECT measurement_type, measurement_categories, measurement_range_min, measurement_range_max, measurement_units FROM measurement WHERE measurement_id=$m_id";
	$result = mysqli_query($dbh,$query);
	$row = mysqli_fetch_array($result,MYSQL_NUM);
	
	$type=$row[0];
	$categories=$row[1];
	$range_min=$row[2];
	$range_max=$row[3];
	$units=$row[4];
	
	$categories_list=explode(",",$categories);
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script>
function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }

function insertRow(type){
	var table=document.getElementById('sample_table');
    var new_row=table.rows[1].cloneNode(true);
    var len=table.rows.length;
       
    var inp1=new_row.cells[0].getElementsByTagName('input')[0];
    inp1.id += len;
    inp1.value = len;
	if(type==0){
		var inp2 = new_row.cells[1].getElementsByTagName('select')[0];
		inp2.setAttribute("onchange","checkOther(this,'')");
		inp2.value='<?php echo($categories_list[0]); ?>';
	} else {
		var inp2 = new_row.cells[1].getElementsByTagName('input')[0];
		inp2.value = '';
	}
    inp2.id += len;
    table.appendChild(new_row);
}

function insert10Rows(type){
	var table=document.getElementById('sample_table');
	
	for(var i=0;i<10;i++){
		var new_row=table.rows[1].cloneNode(true);
		var len=table.rows.length;
       
		var inp1=new_row.cells[0].getElementsByTagName('input')[0];
		inp1.id += len;
		inp1.value = len;
		if(type==0){
			var inp2 = new_row.cells[1].getElementsByTagName('select')[0];
			inp2.setAttribute("onchange","checkOther(this,'')");
			inp2.value='<?php echo($categories_list[0]); ?>';
		} else {
			var inp2 = new_row.cells[1].getElementsByTagName('input')[0];
			inp2.value = '';
		}
		inp2.id += len;
		table.appendChild(new_row);
	}
}

function deleteRow(row){
	var table = document.getElementById('sample_table');
    var rowCount = table.rows.length;
	if(rowCount>2){
		var i=row.parentNode.parentNode.rowIndex;
		document.getElementById('sample_table').deleteRow(i);
	}
}

function checkOther(select,v){
	var c=select.value;
	if(c=="-1"){
		var i=select.parentNode;
		var tx=document.createElement("INPUT");
		tx.setAttribute("class","w3-input w3-border-teal w3-text-green");
		tx.setAttribute("type","text");
		tx.setAttribute("name","other");
		tx.setAttribute("id","other");
		tx.setAttribute("value",v);
		i.appendChild(tx);
	} else {
		var i=select.parentNode;
		i.removeChild(i.childNodes[1]);
	}
}

function inArray(needle,haystack){
	var found = false;
	for (var i = 0; i < haystack.length; i++){
		if(haystack[i]==needle){
			found = true;
			break;
		}
	}
	return found;
}

function saveSamples(log_id){
	var error=false;
	var sampleNumbers=[];
	var sampleValues=[];
	var table = document.getElementById('sample_table');
    var rowCount = table.rows.length;
    for (var i = 1; i < rowCount; i++) {
		var row = table.rows[i];
        var inp1 = row.cells[0].childNodes[0];
		var sampleNumber=inp1.value;
		if(sampleNumber!=""){
			if(!inArray(inp1.value,sampleNumbers)){
				sampleNumbers.push(inp1.value);
			} else {
				alert("Repeated sample number in row "+i);
				error=true;
				break;
			}
<?php
if($type=="0"){
?>
			var s1 = row.cells[1].childNodes[0];
			var sampleValue=s1.value;
			if(sampleValue=="-1"){
				var inp2 = row.cells[1].childNodes[1];
				sampleValue=inp2.value;
				if(sampleValue==""){
					alert("Missing sample value in row "+i);
					error=true;
					break;
				}
			}
			sampleValues.push(sampleValue);
<?php
} else {
?>
			var inp2 = row.cells[1].childNodes[0];
			var sampleValue=inp2.value;
			if(sampleValue!=""){
				var sampleValueF=parseFloat(sampleValue);
				if(sampleValueF><?php echo($range_min); ?> && sampleValueF<<?php echo($range_max); ?>){
					sampleValues.push(inp2.value);
				} else {
					alert("Value out of range in row "+i);
					error=true;
					break;
				}
			} else {
				alert("Missing sample value in row "+i);
				error=true;
				break;
			}
<?php
}
?>
		} else {
			alert("Missing sample number in row "+i);
			error=true;
			break;
		}
	}
	if(!error){
		document.location="update_samples.php?id="+log_id+"&numbers="+sampleNumbers.toString()+"&values="+sampleValues.toString();
	}
}
</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Edit samples</h2>
<div style="height: 520px;" class="w3-text-green">
<div style="height: 510px; overflow:auto;">
<table class="w3-table w3-bordered w3-border" id="sample_table">
<tr><th class="w3-green">Sample n.</th><th class="w3-green">Value<?php if($type=="1") echo(" ($units)"); ?></th><th class="w3-green">&nbsp;</th>
<?php
	$sample_elements=explode("*",$samples);
	for($i=0;$i<sizeof($sample_elements);$i+=2){
		echo('<tr>');
		if($sample_elements[$i]==""){
			$sample_n=1;
		} else {
			$sample_n=$sample_elements[$i];
		}
		echo('<td><input class="w3-input w3-border-teal w3-text-green" type="text" name="samplen" id="samplen" value="'.$sample_n.'" onkeypress="return isNumberKey(event)"></td>');
		if($type=="0"){
			echo('<td>');
			echo('<select class="w3-select w3-text-green" name="value" onchange="checkOther(this,\''.$sample_elements[$i+1].'\')">');
			$found=false;
			for($j=0;$j<sizeof($categories_list);$j++){
				if($categories_list[$j]==$sample_elements[$i+1]){
					$selected=" selected";
					$found=true;
				} else {
					$selected="";
				}
				echo('<option value="'.$categories_list[$j].'"'.$selected.'>'.$categories_list[$j].'</option>');
			}
			if($found){
				echo('<option value="-1">Other</option>');
				echo('</select>');
			} else {
				echo('<option value="-1" selected>Other</option>');
				echo('</select>');
				echo('<input class="w3-input w3-border-teal w3-text-green" type="text" name="other" id="other" value="'.$sample_elements[$i+1].'">');
			}
			echo('</td>');
		} else {
			echo('<td><input class="w3-input w3-border-teal w3-text-green" type="text" name="value" id="samplen" value="'.$sample_elements[$i+1].'" onkeypress="return isNumberKey(event)"></td>');
		}
		echo('<td><button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="delete" name="delete" onclick="deleteRow(this)">Delete</button></td>');
		echo('</tr>');
	}
?>
</table>
</div>
</div>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="add_sample" name="add_sample" onclick="insertRow(<?php echo($type); ?>)">Add sample</button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="add_10samples" name="add_10samples" onclick="insert10Rows(<?php echo($type); ?>)">Add 10 samples</button><br><br>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="edit" name="edit" onclick="saveSamples(<?php echo($id); ?>)"><?php if($id>=0) { echo("Edit"); } else { echo("Save"); } ?></button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</div>
</body>
</html>
<?php
}
?>