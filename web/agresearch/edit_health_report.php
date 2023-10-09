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
		$samples=stripslashes($_SESSION['values']);
	}
	
	$query="SELECT item, item_categories FROM health_report_item ORDER BY item";
	$result = mysqli_query($dbh,$query);
	
	$health_report_items=array();
	$health_report_categories=array();
	
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$health_report_items[$i]=$row[0];
		$health_report_categories[$i]=explode(",",$row[1]);
		$i++;
	}
		
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<title>Agroeco Research</title>
<script>
function insertRow(){
	var table=document.getElementById('sample_table');
    var new_row=table.rows[1].cloneNode(true);
    var len=table.rows.length;
       
    var inp1=new_row.cells[0].getElementsByTagName('input')[0];
    inp1.id += len;
    inp1.value = len;
	for (var i=1;i<(new_row.cells.length-1);i++){
		var inpx = new_row.cells[i].getElementsByTagName('select')[0];
		inpx.setAttribute("onchange","checkOther(this,'')");
		inpx.value='_';
		inpx.id += len;
		var parent = inpx.parentNode;
		if(parent.childNodes.length==2){
			parent.removeChild(parent.childNodes[1]);
		}
	}
    table.appendChild(new_row);
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
		tx.setAttribute("class","w3-input w3-border-teal w3-text-green w3-small");
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


function saveSamples(log_id,num_cols){
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
			
			var sampleValue="";
			for (var j = 1; j <= num_cols; j++){
				var s1 = row.cells[j].childNodes[0];
				var value=s1.value;
				if(value=="-1"){
					var inp2 = row.cells[j].childNodes[1];
					value=inp2.value;
				} 
				if(sampleValue==""){
					sampleValue=value
				} else {
					sampleValue+=";"+value;
				}
			}
			sampleValues.push(sampleValue);
		} else {
			alert("Missing sample number in row "+i);
			error=true;
			break;
		}
	}
	if(!error){
		document.location="update_health_report.php?id="+log_id+"&numbers="+sampleNumbers.toString()+"&values="+sampleValues.toString();
	}
}

</script>
</head>
<body>
<div class="w3-container w3-card-4">
<h2 class="w3-green">Edit health report</h2>
<div style="height: 520px;" class="w3-text-green">
<div style="height: 510px; overflow:auto;">
<table class="w3-table w3-bordered w3-border" id="sample_table">
<tr><th class="w3-green w3-small">Sample n.</th>
<?php
for($i=0;$i<sizeof($health_report_items);$i++){
	echo('<th class="w3-green w3-small">'.$health_report_items[$i].'</th>');
}
?>
<th class="w3-green w3-small">&nbsp;</th>
<?php
	$sample_list=explode("*",$samples);
	for($i=0;$i<sizeof($sample_list);$i+=2){
		$sample_elements=explode("#",$sample_list[$i+1]);
		echo('<tr>');
		if($sample_list[$i]==""){
			$sample_n=1;
		} else {
			$sample_n=$sample_list[$i];
		}
		echo('<td><input class="w3-input w3-border-teal w3-text-green w3-small" type="text" name="samplen" id="samplen" value="'.$sample_n.'"></td>');
		for($j=0;$j<sizeof($health_report_items);$j++){
			echo('<td>');
			echo('<select class="w3-select w3-text-green w3-small" name="value" onchange="checkOther(this,\''.$sample_elements[$j].'\')">');
			echo('<option value="_" selected> </option>');
			$found=false;
			for($k=0;$k<sizeof($health_report_categories[$j])-1;$k++){
				$category_item=stripslashes($health_report_categories[$j][$k]);
				$selected="";
				if($category_item==$sample_elements[$j]){
					$selected=" selected";
					$found=true;	
				}
				echo('<option value="'.$k.'"'.$selected.'>'.$category_item.'</option>');
			}
			if($found || $sample_elements[$j]==" " || $sample_elements[$j]==""){
				echo('<option value="-1">Other</option>');
				echo('</select>');
			} else {
				echo('<option value="-1" selected>Other</option>');
				echo('</select>');
				echo('<input class="w3-input w3-border-teal w3-text-green w3-small" type="text" name="other" id="other" value="'.$sample_elements[$j].'">');
			}
			echo('</td>');
		}
		echo('<td><button class="w3-button w3-green w3-round w3-border w3-border-green w3-medium w3-round-large" id="delete" name="delete" onclick="deleteRow(this)">Delete</button></td>');
		echo('</tr>');
	}
?>
</table>
</div>
</div>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="add_sample" name="add_sample" onclick="insertRow()">Add sample</button><br><br>
<button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" id="edit" name="edit" onclick="saveSamples(<?php echo($id.",".sizeof($health_report_items)); ?>)"><?php if($id>=0) { echo("Edit"); } else { echo("Save"); } ?></button> <button class="w3-button w3-green w3-round w3-border w3-border-green w3-large w3-round-large" onclick="javascript:window.close();">Close</button><br><br>
</div>
</body>
</html>
<?php
}
?>