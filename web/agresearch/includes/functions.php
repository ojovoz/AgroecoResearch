<?php

function normalize($data) {
	$data = trim($data);
	$data = addslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function validateUser($dbh, $user_alias, $user_password) {
	$ret=-1;
	$query="SELECT user_id, user_role FROM user WHERE user_alias='$user_alias' AND user_password='$user_password'";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0].",".$row[1];
	}
	return $ret;
}

function getUserRole($dbh,$user_id){
	$ret=-1;
	$query="SELECT user_role FROM user WHERE user_id=$user_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getUserNameFromId($dbh,$user_id){
	$ret="";
	$query="SELECT user_name FROM user WHERE user_id=$user_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getUsers($dbh){
	$ret=array();
	$query="SELECT user_id, user_name FROM user ORDER BY user_name";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row;
		$i++;
	}
	return $ret;
}

function getUserInitials($full_name){
	$ret="";
	$name_parts=explode(" ",$full_name);
	for($i=0;$i<sizeof($name_parts);$i++){
		$initial=strtoupper(substr($name_parts[$i],0,1));
		if($ret==""){
			$ret=$initial;
		} else {
			$ret.=$initial;
		}
	}
	return $ret;
}

function getCrops($dbh,$int){
	$ret=array();
	if($int==1){
		$query="SELECT crop_id, CONCAT(crop_name,' (',crop_variety_name,')') AS name, crop_symbol FROM crop WHERE crop_used_for_intercropping=1 ORDER BY name";
	} else if ($int==0) {
		$query="SELECT crop_id, CONCAT(crop_name,' (',crop_variety_name,')') AS name, crop_symbol FROM crop WHERE crop_used_for_intercropping=0 ORDER BY name";
	} else {
		$query="SELECT crop_id, CONCAT(crop_name,' (',crop_variety_name,')') AS name, crop_symbol FROM crop ORDER BY name";
	}
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row[0].",".$row[1].",".$row[2];
		$i++;
	}
	return $ret;
}

function getTreatments($dbh){
	$ret=array();
	$query="(SELECT treatment_id AS id, treatment_name AS treatment, ' ' AS crop1, ' ' AS crop2 FROM treatment WHERE primary_crop_id = NULL AND intercropping_crop_id = NULL) UNION (SELECT treatment_id AS id, treatment_name AS treatment, (SELECT CONCAT(crop_name,' (',crop_variety_name,')') FROM crop WHERE crop_id = treatment.primary_crop_id) AS crop1, (SELECT CONCAT(crop_name,' (',crop_variety_name,')') FROM crop WHERE crop_id = treatment.intercropping_crop_id) AS crop2 FROM treatment) ORDER BY treatment";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$t="";
		if(!is_null($row[2]) && !is_null($row[3])){
			$t=" (".$row[2]." with ".$row[3].")";
		}
		$ret[$i]=$row[0].",".$row[1].$t;
		$i++;
	}
	return $ret;
}

function getMeasurementNameFromId($dbh,$measurement_id){
	$ret="";
	$query="SELECT measurement_name, measurement_category FROM measurement WHERE measurement_id=$measurement_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0]." (".$row[1].")";
	}
	return $ret;
}

function getMeasurementNameFromIdWithUnits($dbh,$measurement_id){
	$ret="";
	$query="SELECT measurement_name, measurement_category, measurement_units FROM measurement WHERE measurement_id=$measurement_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0]." (".$row[1].", ".$row[2].")";
	}
	return $ret;
}

function getMeasurementCategories($dbh){
	$ret=array();
	$query="SELECT DISTINCT measurement_category FROM measurement ORDER BY measurement_category";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row[0];
		$i++;
	}
	return $ret;
}

function getMeasurementSubcategories($dbh){
	$ret=array();
	$query="SELECT DISTINCT measurement_subcategory FROM measurement ORDER BY measurement_subcategory";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row[0];
		$i++;
	}
	return $ret;
}

function isMultisample($dbh,$id){
	$ret=false;
	$query="SELECT measurement_has_sample_number FROM measurement WHERE measurement_id=$id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=($row[0]==1);
	}
	return $ret;
}

function getActivityCategories($dbh){
	$ret=array();
	$query="SELECT DISTINCT activity_category FROM activity ORDER BY activity_category";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row[0];
		$i++;
	}
	return $ret;
}

function fieldHasConfiguration($field_id,$dbh){
	$ret=false;
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		if($row[0]!=""){
			$ret=true;
		}
	}
	return $ret;
}

function getFieldConfiguration($field_id,$dbh){
	$ret="";
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getPlotsAssociatedWithMeasurement($dbh,$field_id,$measurement_id){
	$ret=array();
	$unordered=array();
	$configuration=getFieldConfiguration($field_id,$dbh);
	$parts=explode(";",$configuration);
	for($i=2;$i<sizeof($parts);$i++){
		$plot=calculatePlotLabelsWithoutCrop($dbh,$field_id,($i-2));
		if(isPlotAssociatedWithTask($dbh,$parts[$i],$measurement_id,"lm") && !in_array($plot,$unordered)){
			array_push($unordered,$plot);
		} 
	}
	$target_order=array("Control","PSL","SL","PS","PL","S","L","P");
	for($i=0;$i<sizeof($target_order);$i++){
		for($j=0;$j<sizeof($unordered);$j++){
			if($unordered[$j]==$target_order[$i]){
				array_push($ret,$unordered[$j]);
			}
		}
	}
	return $ret;
}

function getFieldIdFromName($dbh,$name){
	$ret=-1;
	$query="SELECT field_id FROM field WHERE field_name='$name' ORDER BY field_id LIMIT 0,1";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getFieldNameFromId($dbh,$id){
	$ret="";
	if(substr_count($id,",")>0){
		$query="SELECT field_name, field_replication_number FROM field WHERE field_id IN($id)";
		$result = mysqli_query($dbh,$query);
		while($row = mysqli_fetch_array($result,MYSQL_NUM)){
			if($ret==""){
				$ret=$row[0]." R".$row[1];
			} else {
				$ret.=",R".$row[1];
			}
		}
	} else {
		$query="SELECT field_name, field_replication_number FROM field WHERE field_id=$id";
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			$ret=$row[0]." R".$row[1];
		}
	}
	return $ret;
}

function getFieldNameFromIdWithoutReplication($dbh,$id){
	$ret="";
	$query="SELECT field_name FROM field WHERE field_id=$id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getParentField($field_id,$dbh){
	$ret="";
	$query="SELECT parent_field_id FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getChildFields($dbh,$field_id){
	$ret=array();
	$query="SELECT field_id FROM field WHERE parent_field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		array_push($ret,$row[0]);
	}
	return $ret;
}

function getAllReplications($field_id,$dbh){
	$ret=array();
	$query="SELECT field_id FROM field WHERE parent_field_id=$field_id AND field_is_active";
	$result = mysqli_query($dbh,$query);
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		array_push($ret,$row[0]);
	}
	return $ret;
}

function getEquivalentPlots($source_field,$source_plots,$dest_field,$dbh){
	$plots_dest="";
	
	$query="SELECT field_configuration FROM field WHERE field_id=$source_field";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		
		$field_configuration_source=$row[0];
		$elements_source=explode(";",$field_configuration_source);
		$plots_source=explode(",",$source_plots);
		
		$query="SELECT field_configuration FROM field WHERE field_id=$dest_field";
		$result_dest = mysqli_query($dbh,$query);
		if($row_dest = mysqli_fetch_array($result_dest,MYSQL_NUM)){
			
			$field_configuration_dest=$row_dest[0];
			$elements_dest=explode(";",$field_configuration_dest);
		
			for($i=0;$i<sizeof($plots_source);$i++){
				$plot_source=$elements_source[$plots_source[$i]+2];
				$plot_parts_source=parseConfig($plot_source);
			
				for($j=2;$j<sizeof($elements_dest);$j++){
					$plot_dest=$elements_dest[$j];
					$plot_parts_dest=parseConfig($plot_dest);
					if($plot_parts_source[0]==$plot_parts_dest[0] && $plot_parts_source[1]==$plot_parts_dest[1] && $plot_parts_source[2]==$plot_parts_dest[2] && $plot_parts_source[3]==$plot_parts_dest[3]){
						if($plots_dest==""){
							$plots_dest=($j-2);
						} else {
							$plots_dest.=",".($j-2);
						}
						break;
					}
				}
			
			}
		}
	}
	
	return $plots_dest;
}

function parseConfig($element){
	$inner=substr($element,3,(strlen($element)-4));
	$parts=explode(",",$inner);
	return $parts;
}

function recalculateConfig($config){
	$elements=explode(";",$config);
	$included_crops=array();
	$n=0;
	$intercropping=0;
	$soil_management=0;
	$pest_control=0;
	for($i=2;$i<(sizeof($elements)-1);$i++){
		$parts=parseConfig($elements[$i]);
		if(!in_array($parts[0],$included_crops)){
			$included_crops[$n]=$parts[0];
			$n++;
		}
		if($parts[1]!=0){
			$intercropping=1;
		}
		if($parts[2]!=0){
			$soil_management=1;
		}
		if($parts[3]!=0){
			$pest_control=1;
		}
	}
	$elements[0]='F=('.$n.','.$intercropping.','.$soil_management.','.$pest_control.')';
	$ret=implode(";",$elements);
	return $ret;
}

function updateFieldConfiguration($dbh,$field_id,$config){
	$ret="";
	$yy=date('Y');
	$mm=date('m');
	$dd=date('d');
	$current_date=$yy."-".$mm."-".$dd;
	$query="SELECT user_id, field_name, field_replication_number, field_lat, field_lng FROM field WHERE field_id = $field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$user_id=$row[0];
		$field_name=$row[1];
		$field_replication_number=$row[2];
		$field_lat=$row[3];
		$field_lng=$row[4];
		if($field_replication_number==1){
			$query="INSERT INTO field (user_id, field_date_created, field_name, field_replication_number, field_lat, field_lng, field_configuration) VALUES ($user_id, '".$current_date."', '".$field_name."', 1, '".$field_lat."', '".$field_lng."', '".$config."')";
			$result = mysqli_query($dbh,$query);
			$new_field_id=mysqli_insert_id($dbh);
			$query="UPDATE field SET parent_field_id = $new_field_id WHERE field_id=$new_field_id";
			$result = mysqli_query($dbh,$query);
		} else {
			$query="SELECT field_id FROM field WHERE field_name='".$field_name."' AND field_replication_number=1 AND field_is_active=1 AND field_date_created >= '".$current_date."'";
			$result = mysqli_query($dbh,$query);
			if($row = mysqli_fetch_array($result,MYSQL_NUM)){
				$parent_field_id=$row[0];
				$query="INSERT INTO field (user_id, parent_field_id, field_date_created, field_name, field_replication_number, field_lat, field_lng, field_configuration) VALUES ($user_id, $parent_field_id, '".$current_date."', '".$field_name."', $field_replication_number, '".$field_lat."', '".$field_lng."', '".$config."')";
				$result = mysqli_query($dbh,$query);
			} else {
				$ret="You must first update ".$field_name." replication 1";
			}
		}
		if($ret==""){
			$query="UPDATE field SET field_date_final = '".$current_date."', field_is_active = 0 WHERE field_id = $field_id";
			$result = mysqli_query($dbh,$query);
		}
	}
	return $ret;
}

function calculatePlotLabels($dbh,$field_id,$plotsCSV){
	$ret="";
	
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_configuration=$row[0];
		$elements=explode(";",$field_configuration);
		$plots=explode(",",$plotsCSV);
		
		for($i=0;$i<sizeof($plots);$i++){
			$plot=$elements[$plots[$i]+2];
			$plot_parts=parseConfig($plot);
			$plot_string=getCropSymbolFromId($dbh,$plot_parts[0]);
			$plot_treatments="";
			if($plot_parts[3]!=0){
				$plot_treatments="P";
			}
			if($plot_parts[2]!=0){
				$plot_treatments.="S";
			}
			if($plot_parts[1]!=0){
				$plot_treatments.="L";
			}
			if($plot_treatments!=""){
				$plot_string=$plot_string."-".$plot_treatments;
			}
		
			if($ret==""){
				$ret=$plot_string;
			} else {
				$ret.=", ".$plot_string;
			}
		}

	}
	
	return $ret;
}

function calculatePlotLabelsLogScreen($dbh,$field_id,$plotsCSV){
	$ret="";
	
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_configuration=$row[0];
		$elements=explode(";",$field_configuration);
		$plots=explode(",",$plotsCSV);
		
		if(sizeof($plots)==(sizeof($elements)-3)){
			$ret="All plots";
		} else {
		
			for($i=0;$i<sizeof($plots);$i++){
				$plot=$elements[$plots[$i]+2];
				$plot_parts=parseConfig($plot);
				$plot_string=getCropSymbolFromId($dbh,$plot_parts[0]);
				$plot_treatments="";
				if($plot_parts[3]!=0){
					$plot_treatments="P";
				}
				if($plot_parts[2]!=0){
					$plot_treatments.="S";
				}
				if($plot_parts[1]!=0){
					$plot_treatments.="L";
				}
				if($plot_treatments!=""){
					$plot_string=$plot_string."-".$plot_treatments;
				}
		
				if($ret==""){
					$ret=$plot_string;
				} else {
					$ret.=", ".$plot_string;
				}
			}
		}
	}
	
	return $ret;
}

function calculatePlotLabelsWithoutCrop($dbh,$field_id,$plotsCSV){
	$ret="";
	
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_configuration=$row[0];
		$elements=explode(";",$field_configuration);
		$plots=explode(",",$plotsCSV);
		
		for($i=0;$i<sizeof($plots);$i++){
			$plot=$elements[$plots[$i]+2];
			$plot_parts=parseConfig($plot);
			$plot_treatments="";
			if($plot_parts[3]!=0){
				$plot_treatments="P";
			}
			if($plot_parts[2]!=0){
				$plot_treatments.="S";
			}
			if($plot_parts[1]!=0){
				$plot_treatments.="L";
			}
			if($plot_treatments!=""){
				$plot_string=$plot_treatments;
			} else {
				$plot_string="Control";
			}
		
			if($ret==""){
				$ret=$plot_string;
			} else {
				$ret.=", ".$plot_string;
			}
		}

	}
	
	return $ret;
}

function getMissingPlotLabels($dbh,$field_id,$replication_plots,$distinct_dates,$measurement_id){
	/*
	$ret=array();
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_configuration=$row[0];
		$elements=explode(";",$field_configuration);
		$missing_plots=array();
		for($i=2;$i<sizeof($elements);$i++){
			if(isPlotAssociatedWithTask($dbh,$elements[$i],$measurement_id,"lm") && !in_array(($i-2),$replication_plots)){
				array_push($missing_plots,($i-2));
			}
		}
		if(sizeof($missing_plots)>0){
			for($i=0;$i<sizeof($missing_plots);$i++){
				$plot_label=calculatePlotLabelsWithoutCrop($dbh,$field_id,$missing_plots[$i]);
				for($j=0;$j<sizeof($distinct_dates);$j++){
					$complete_plot_label=$plot_label." (".$distinct_dates[$j].")";
					array_push($ret,$complete_plot_label);
				}
			}
		}
	}
	return $ret;
	*/
}

function getAllPlots($dbh,$field_id){
	$ret="";
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_configuration=$row[0];
		$elements=explode(";",$field_configuration);
		for($i=2;$i<sizeof($elements);$i++){
			$plot=$elements[$i];
			$plot_parts=parseConfig($plot);
			$plot_string=getCropSymbolFromId($dbh,$plot_parts[0]);
			$plot_treatments="";
			if($plot_parts[3]!=0){
				$plot_treatments="P";
			}
			if($plot_parts[2]!=0){
				$plot_treatments.="S";
			}
			if($plot_parts[1]!=0){
				$plot_treatments.="L";
			}
			if($plot_treatments!=""){
				$plot_string=$plot_string."-".$plot_treatments;
			}
			if($ret==""){
				$ret=$plot_string;
			} else if($plot_string!=""){
				$ret.=", ".$plot_string;
			}
		}
	}
	return $ret;
}

function isPlotAssociatedWithTask($dbh,$plot,$id,$task){
	
	$ret=false;
	
	$plot_parts=parseConfig($plot);
	$plot_crop=$plot_parts[0];
	if($plot_parts[1]!=0){
		$plot_intercropping_crop=$plot_parts[1];
	} else {
		$plot_intercropping_crop=-1;
	}
	
	if($task=="lm"){
		if($id>=0){
			$query="SELECT log.measurement_id, measurement_x_crop_or_treatment.crop_id, measurement_x_crop_or_treatment.treatment_id FROM log, measurement_x_crop_or_treatment WHERE log_id=$id AND measurement_x_crop_or_treatment.measurement_id = log.measurement_id";
		} else {
			$id=$id*-1;
			$query="SELECT measurement.measurement_id, measurement_x_crop_or_treatment.crop_id, measurement_x_crop_or_treatment.treatment_id FROM measurement, measurement_x_crop_or_treatment WHERE measurement.measurement_id=$id AND measurement_x_crop_or_treatment.measurement_id=measurement.measurement_id";
		}
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			if($plot_crop==$row[1] || $plot_intercropping_crop==$row[1]){
				$ret=true;
			} else {
				if($row[2]>0){
					$treatment_id=$row[2];
					$query="SELECT treatment_category FROM treatment WHERE treatment_id=$treatment_id";
					$result = mysqli_query($dbh,$query);
					if($row = mysqli_fetch_array($result,MYSQL_NUM)){
						if(($plot_parts[3]!=0 && $row[0]=="Pest control") || ($plot_parts[2]!=0 && $row[0]=="Soil management")){
							$ret=true;
						}
					}
				}
			}
		} else {
			$ret=true;
		}
	} else if($task=="la"){
		if($id>=0){
			$query="SELECT log.activity_id, activity_x_crop_or_treatment.crop_id, activity_x_crop_or_treatment.treatment_id FROM log, activity_x_crop_or_treatment WHERE log_id=$id AND activity_x_crop_or_treatment.activity_id = log.activity_id";
		} else {
			$id=$id*-1;
			$query="SELECT activity.activity_id, activity_x_crop_or_treatment.crop_id, activity_x_crop_or_treatment.treatment_id FROM activity, activity_x_crop_or_treatment WHERE activity.activity_id=$id AND activity_x_crop_or_treatment.activity_id=activity.activity_id";
		}
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			if($plot_crop==$row[1] || $plot_intercropping_crop==$row[1]){
				$ret=true;
			} else {
				if($row[2]>0){
					$treatment_id=$row[2];
					$query="SELECT treatment_category FROM treatment WHERE treatment_id=$treatment_id";
					$result = mysqli_query($dbh,$query);
					if($row = mysqli_fetch_array($result,MYSQL_NUM)){
						if(($plot_parts[3]!=0 && $row[0]=="Pest control") || ($plot_parts[2]!=0 && $row[0]=="Soil management")){
							$ret=true;
						}
					}
				}
			}
		} else {
			$ret=true;
		}
	} else if($task=="ic"){
		if($id>=0){
			$query="SELECT crop_id FROM input_log WHERE input_log_id=$id";
		} else {
			$id=$id*-1;
			$query="SELECT crop_id FROM crop WHERE crop_id=$id";
		}
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			if($plot_crop==$row[0] || $plot_intercropping_crop==$row[0]){
				$ret=true;
			}
		}
	} else if($task=="it"){
		if($id>=0){
			$query="SELECT input_log.treatment_id, treatment.treatment_category FROM input_log,treatment WHERE input_log_id=$id AND treatment.treatment_id = input_log.treatment_id";
		} else {
			$id=$id*-1;
			$query="SELECT treatment_id, treatment_category FROM treatment WHERE treatment_id=$id";
		}
		$result = mysqli_query($dbh,$query);
		if($row = mysqli_fetch_array($result,MYSQL_NUM)){
			if(($plot_parts[3]!=0 && $row[1]=="Pest control") || ($plot_parts[2]!=0 && $row[1]=="Soil management")){
				$ret=true;
			}
		}
	}
	
	return $ret;			
	
}

function getRemainingPlots($dbh,$field_id,$plots,$id,$task){
	$ret="";
	$query="SELECT field_configuration FROM field WHERE field_id=$field_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$field_configuration=$row[0];
		$elements=explode(";",$field_configuration);
		if((sizeof($elements)-3)>sizeof($plots)){
			for($i=2;$i<sizeof($elements)-1;$i++){
				if(!in_array(($i-2),$plots) && isPlotAssociatedWithTask($dbh,$elements[$i],$id,$task)){
					if($ret==""){
						$ret=(string)($i-2);
					} else {
						$ret.=",".($i-2);
					}
				}
			}
		}
	}
	return $ret;
}

function getCropSymbolFromId($dbh,$crop_id){
	$ret="";
	$query="SELECT crop_symbol FROM crop WHERE crop_id=$crop_id";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0];
	}
	return $ret;
}

function getTotalItemsReport($dbh,$field_filter,$date_filter,$measurement_filter){
	$ret=0;
	$query="SELECT DISTINCT log.log_date AS date, field.parent_field_id AS field, log.measurement_id FROM log, field, measurement WHERE field.field_id = log.field_id AND measurement.measurement_id = log.measurement_id AND measurement.measurement_type <> 2".$field_filter.$date_filter.$measurement_filter."ORDER BY date, field";
	$result = mysqli_query($dbh,$query);
	$ret=mysqli_num_rows($result);
	return $ret;
}

function getTotalItems($dbh,$log_field_filter,$input_log_field_filter,$log_date_filter,$input_log_date_filter,$activity_filter,$measurement_filter,$crop_filter,$treatment_filter,$log_user_filter,$input_log_user_filter){
	
	$n=0;
	$m=0;
	$only_log=false;
	$only_input=false;
	
	$where="";
	if(trim($log_field_filter)!=""){
		$log_field_filter=substr($log_field_filter,5);
		$where=" WHERE ".$log_field_filter;
	}
	if(trim($log_date_filter)!=""){
		$log_date_filter=substr($log_date_filter,5);
		if($where==""){
			$where=" WHERE ".$log_date_filter;
		} else {
			$where.=" AND ".$log_date_filter;
		}
	}
	if(trim($log_user_filter)!=""){
		$log_user_filter=substr($log_user_filter,5);
		if($where==""){
			$where=" WHERE ".$log_user_filter;
		} else {
			$where.=" AND ".$log_user_filter;
		}
	}
	if($activity_filter>0){
		$only_log=true;
		$activity_filter="activity_id=".$activity_filter;
		if($where==""){
			$where=" WHERE ".$activity_filter;
		} else {
			$where.=" AND ".$activity_filter;
		}
	} else if($measurement_filter>0){
		$only_log=true;
		$measurement_filter="measurement_id=".$measurement_filter;
		if($where==""){
			$where=" WHERE ".$measurement_filter;
		} else {
			$where.=" AND ".$measurement_filter;
		}
	}
	$query="SELECT COUNT(log_id) FROM log".$where;
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$n=$row[0];
	} else {
		$n=0;
	}
	
	$where="";
	if(trim($input_log_field_filter)!=""){
		$input_log_field_filter=substr($input_log_field_filter,5);
		$where=" WHERE ".$input_log_field_filter;
	}
	if(trim($input_log_date_filter)!=""){
		$input_log_date_filter=substr($input_log_date_filter,5);
		if($where==""){
			$where=" WHERE ".$input_log_date_filter;
		} else {
			$where.=" AND ".$input_log_date_filter;
		}
	}
	if(trim($input_log_user_filter)!=""){
		$input_log_user_filter=substr($input_log_user_filter,5);
		if($where==""){
			$where=" WHERE ".$input_log_user_filter;
		} else {
			$where.=" AND ".$input_log_user_filter;
		}
	}
	if($crop_filter>0){
		$only_input=true;
		$crop_filter="crop_id=".$crop_filter;
		if($where==""){
			$where=" WHERE ".$crop_filter;
		} else {
			$where.=" AND ".$crop_filter;
		}
	} else if($treatment_filter>0){
		$only_input=true;
		$treatment_filter="treatment_id=".$treatment_filter;
		if($where==""){
			$where=" WHERE ".$treatment_filter;
		} else {
			$where.=" AND ".$treatment_filter;
		}
	}
	$query="SELECT COUNT(input_log_id) FROM input_log".$where;
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$m=$row[0];
	}
	
	if($only_log){
		$ret=$n;
	} else if($only_input){
		$ret=$m;
	} else {
		$ret=$m+$n;
	}
	
	return $ret;
}

function getFields($dbh){
	$ret=array();
	$query="SELECT field_id, field_name, field_replication_number FROM field WHERE field_is_active=1 ORDER BY field_name, field_replication_number";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row;
		$i++;
	}
	return $ret;
}

function getFieldsAllVersions($dbh){
	$ret=array();
	$query="SELECT field_id, field_name, field_replication_number FROM field ORDER BY field_name, field_replication_number";
	$result = mysqli_query($dbh,$query);
	$i=0;
	$prev_field="";
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$current_field=$row[1]."; R".$row[2];
		if($current_field!=$prev_field){
			if($prev_field!=""){
				array_push($ret,$field_row);
			}
			$field_row=$current_field.";".$row[0];
			$prev_field=$current_field;
		} else {
			$field_row.=",".$row[0];
		}
	}
	array_push($ret,$field_row);
	return $ret;
}

function getActivities($dbh){
	$ret=array();
	$query="SELECT activity_id, activity_name FROM activity ORDER BY activity_name";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row;
		$i++;
	}
	return $ret;
}

function getMeasurements($dbh){
	$ret=array();
	$query="SELECT measurement_id, measurement_name FROM measurement ORDER BY measurement_name";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row;
		$i++;
	}
	return $ret;
}

function getTreatmentColors($dbh){
	$ret=array();
	$query="SELECT treatment_color_id, color FROM treatment_color ORDER BY treatment_color_id";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row[1];
		$i++;
	}
	return $ret;
}

//mail

function decodeISO88591($string) {               
	$string=str_replace("=?iso-8859-1?q?","",$string);
  	$string=str_replace("=?iso-8859-1?Q?","",$string);
  	$string=str_replace("?=","",$string);

  	$charHex=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
       
	for($z=0;$z<sizeof($charHex);$z++) {
		for($i=0;$i<sizeof($charHex);$i++) {
      		$string=str_replace(("=".($charHex[$z].$charHex[$i])),chr(hexdec($charHex[$z].$charHex[$i])),$string);
    	}
  	}
  	return($string);
}

function parse($structure) {
	$type = array("text", "multipart", "message", "application", "audio", "image", "video", "other");
	$encoding = array("7bit", "8bit", "binary", "base64", "quoted-printable", "other");
	$ret = array();
	$parts = $structure->parts;
	for($x=0; $x<sizeof($parts); $x++) {
		$ret[$x]["pid"] = ($x+1);	
		$this_part = $parts[$x];
		if ($this_part->type == "") { $this_part->type = 0; }
		$ret[$x]["type"] = $type[$this_part->type] . "/" . strtolower($this_part->subtype);	
		if ($this_part->encoding == "") { $this_part->encoding = 0; }
		$ret[$x]["encoding"] = $encoding[$this_part->encoding];	
		$ret[$x]["size"] = strtolower($this_part->bytes);	
		if ($this_part->ifdisposition) {
			$ret[$x]["disposition"] = strtolower($this_part->disposition);	
			if (strtolower($this_part->disposition) == "attachment" || strtolower($this_part->disposition) == "inline") {
				$params = $this_part->dparameters;
				if (is_null($params)) {
					$params = $this_part->parameters;
				}
				if (!is_null($params)) {
					foreach ($params as $p) {
						if($p->attribute == "FILENAME" || $p->attribute == "NAME") {
							$ret[$x]["name"] = $p->value;	
							break;			
						}
					}
				}
			}
		} 
	}
	return $ret;
}

function decodeSubject($s) {
	$ret=$s;
	$elements=imap_mime_header_decode($s);
	if (sizeof($elements)>0) {
		if($elements[0]->charset=="utf-8") {
			$ret=utf8_decode($elements[0]->text);
		} else if ($elements[0]->charset="ISO-8859-1") {
			$ret=decodeISO88591($elements[0]->text);
		}
	}
	return $ret;
}

function checkMessages($mail_server, $mail_user, $mail_password, $dbh){
	if ($inbox = imap_open ($mail_server, $mail_user, $mail_password)) {
		$total = imap_num_msg($inbox);
		for($x=1; $x<=$total; $x++) {
			$headers = imap_header($inbox, $x);
			$structure = imap_fetchstructure($inbox, $x);
			$sections = parse($structure);
			if (isset($headers->subject)) {
				$subject = decodeSubject($headers->subject);
			} else {
				$subject = "";
			}
			if ($subject=="pA439urcjLVk6szA" && is_array($sections) && sizeof($sections)>0) {
				for($y=0; $y<sizeof($sections); $y++) {	
					$type = $sections[$y]["type"];
					$encoding = $sections[$y]["encoding"];
					$pid = $sections[$y]["pid"];
					$attachment = imap_fetchbody($inbox,$x,$pid);
					if ($type=="text/plain" || $type=="text/html") {
						if ($encoding == "base64") {
							$text = trim(utf8_decode(imap_base64($attachment)));
						} else {
							$text = trim(utf8_decode(decodeISO88591($attachment)));
						}
						
						//remove new lines!!
						$text=str_replace(array("\n\r", "\n", "\r"),'',$text);
						
						$what_log=explode("<>",$text);
						$ma_log_entry=explode("|",$what_log[0]);
						
						for($i=0;$i<sizeof($ma_log_entry);$i++){
							$ma_log_entry_part_raw=str_replace('=','',$ma_log_entry[$i]);
							$ma_log_entry_part=explode(";",$ma_log_entry_part_raw);
							if(sizeof($ma_log_entry_part)==16){
								$field_id=$ma_log_entry_part[0];
								$plots=$ma_log_entry_part[1];
								$user_id=$ma_log_entry_part[2];
								$crop_id=$ma_log_entry_part[3];
								$treatment_id=$ma_log_entry_part[4];
								$measurement_id=$ma_log_entry_part[5];
								$activity_id=$ma_log_entry_part[6];
								$date=str_replace(' ','',$ma_log_entry_part[7]);
								$number_value=$ma_log_entry_part[8];
								$units=$ma_log_entry_part[9];
								$text_value=$ma_log_entry_part[10];
								$number_of_laborers=$ma_log_entry_part[11];
								$cost=number_format($ma_log_entry_part[12],2,'.','');
								$comments=$ma_log_entry_part[13];
								$log_id=$ma_log_entry_part[14];
								$sample_number=$ma_log_entry_part[15];
							
								$query="INSERT INTO log (field_id, plots, user_id, crop_id, sample_number, treatment_id, measurement_id, activity_id, log_date, log_value_number, log_value_units, log_value_text, log_number_of_laborers, log_cost, log_comments) VALUES ($field_id, '$plots', $user_id, $crop_id, $sample_number, $treatment_id, $measurement_id, $activity_id, '$date', $number_value, '$units', '$text_value', '$number_of_laborers', '$cost', '$comments')";
								//echo($query."<br><br><br>");
								$result = mysqli_query($dbh,$query);
							}
						}
						
						$i_log_entry=explode("|",$what_log[1]);
						for($i=0;$i<sizeof($i_log_entry);$i++){
							$i_log_entry_part_raw=str_replace('=','',$i_log_entry[$i]);
							$i_log_entry_part=explode(";",$i_log_entry_part_raw);
							if(sizeof($i_log_entry_part)==16){
								$log_id=$i_log_entry_part[0];
								$field_id=$i_log_entry_part[1];
								$plots=$i_log_entry_part[2];
								$user_id=$i_log_entry_part[3];
								$crop_id=$i_log_entry_part[4];
								$treatment_id=$i_log_entry_part[5];
								$date=str_replace(' ','',$i_log_entry_part[6]);
								$age=$i_log_entry_part[7];
								$origin=$i_log_entry_part[8];
								if($origin=="null") { $origin=""; }
								$variety=$i_log_entry_part[9];
								if($variety=="null") { $variety=""; }
								$quantity=$i_log_entry_part[10];
								$units=$i_log_entry_part[11];
								if($units=="null") { $units=""; }
								$cost=number_format($i_log_entry_part[12],2,'.','');
								$material=$i_log_entry_part[13];
								if($material=="null") { $material=""; }
								$method=$i_log_entry_part[14];
								if($method=="null") { $method=""; }
								$comments=$i_log_entry_part[15];
								if($comments=="null") { $comments=""; }
							
								$query="INSERT INTO input_log (input_log_date, field_id, plots, user_id, crop_id, treatment_id, input_age, input_origin, input_crop_variety, input_quantity, input_units, input_cost, input_treatment_material, input_treatment_preparation_method, input_comments) VALUES ('$date', $field_id, '$plots', $user_id, $crop_id, $treatment_id, '$age', '$origin', '$variety', $quantity, '$units', '$cost', '$material', '$method', '$comments')";
								//echo($query."<br><br><br>");
								$result = mysqli_query($dbh,$query);
							}
						}
						
					}
				}
				$sections=NULL;
			}
			imap_delete($inbox,$x);
		}
		imap_close($inbox, CL_EXPUNGE);
	}
}

function markNotificationAsSent($dbh,$id){
	$query="UPDATE notification SET notification_sent=1 WHERE notification_id=$id";
	$result = mysqli_query($dbh,$query);
}

function parseIngredients($ingredients){
	$ret="";
	$ingredient_elements=explode("*",$ingredients);
	if(sizeof($ingredient_elements)>1){
		for($i=0;$i<sizeof($ingredient_elements);$i+=3){
			if($ret==""){
				$ret=$ingredient_elements[$i].": ".$ingredient_elements[$i+1]." ".$ingredient_elements[$i+2];
			} else {
				$ret=$ret.", ".$ingredient_elements[$i].": ".$ingredient_elements[$i+1]." ".$ingredient_elements[$i+2];
			}
		}
	} else {
		$ret=trim($ingredients);
	}
	return $ret;
}

function reverseParseIngredients($ingredients){

	$ret="";
	$ingredients=trim($ingredients);
	$ingredients = preg_replace('!\s+!', ' ', $ingredients);
	$ingredient_list=explode(",",$ingredients);
	if(sizeof($ingredient_list)>1){
		for($i=0;$i<sizeof($ingredient_list);$i++){	
			$ingredient_elements=explode(":",$ingredient_list[$i]);
			if(sizeof($ingredient_elements)==2){
				$second_part=explode(" ",trim($ingredient_elements[1]));
				if(sizeof($second_part)==2){
					$ingredient=str_replace(":","",$ingredient_elements[0]);
					$ingredient=str_replace("*","",$ingredient);
					$quantity=str_replace(":","",$second_part[0]);
					$quantity=str_replace("*","",$quantity);
					$units=str_replace(":","",$second_part[1]);
					$units=str_replace("*","",$units);
					if($ret==""){
						$ret=trim($ingredient)."*".trim($quantity)."*".trim($units);
					} else {
						$ret.="*".trim($ingredient)."*".trim($quantity)."*".trim($units);
					}
				} else {
					$ret=-1;
					break;
				}
			} else {
				$ret=-1;
				break;
			}
		} 
	} else {
		$ret=$ingredients;
	}
	return $ret;
}

function getHealthReportItems($dbh){
	$ret=array();
	$query="SELECT item FROM health_report_item ORDER BY item";
	$result = mysqli_query($dbh,$query);
	$i=0;
	while($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret[$i]=$row[0];
		$i++;
	}
	return $ret;
}

function parseHealthReportValues($dbh,$sample_values){
	$ret="";
	$problem_names=getHealthReportItems($dbh);
	$samples=explode("*",$sample_values);
	for($i=0;$i<sizeof($samples);$i+=2){
		$sample_n=$samples[$i];
		$problems=explode("#",$samples[$i+1]);
		$problem_string="";
		for($j=0;$j<sizeof($problem_names);$j++){
			if(trim($problems[$j])!=""){
				if($problem_string==""){
					$problem_string=$problem_names[$j]." - ".$problems[$j];
				} else {
					$problem_string=$problem_string.", ".$problem_names[$j]." - ".$problems[$j];
				}
			}
		}
		if($problem_string!=""){
			if($ret==""){
				$ret=$sample_n.": ".$problem_string;
			} else {
				$ret=$ret."<br>".$sample_n.": ".$problem_string;
			}
		}
	}
	return $ret;
}

function getHealthReportItemCategory($dbh,$item,$category){
	$ret="";
	$problem_names=getHealthReportItems($dbh);
	$chosen_problem=$problem_names[$item];
	$query="SELECT item_categories FROM health_report_item WHERE item='$chosen_problem'";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$categories=explode(",",$row[0]);
		$ret=$categories[$category];
	}
	return $ret;
}

function parseSampleValues($sample_values){
	$ret="";
	$elements=explode("*",$sample_values);
	/*
	if(sizeof($elements)>10){
		$n=5;
		$tail=" ...";
	} else {
		$n=sizeof($elements);
		$tail="";
	}
	*/
	$n=sizeof($elements);
	$tail="";
	for($i=0;$i<$n;$i+=2){
		if($ret==""){
			$ret=$elements[$i].":".$elements[$i+1];
		} else {
			$ret.=", ".$elements[$i].":".$elements[$i+1];
		}
	}
	return $ret.$tail;
}

function getMaxWeatherFilenameId($dbh){
	$ret=0;
	$query="SELECT MAX(weather_data_id) FROM weather_data";
	$result = mysqli_query($dbh,$query);
	if($row = mysqli_fetch_array($result,MYSQL_NUM)){
		$ret=$row[0]+1;
	}
	$ret="_".$ret;
	return $ret;
}

function getStartEndDatesFromWeatherDataFile($file){
	$ret="";
	$row=0;
	if(($f = fopen($file, "r")) !== FALSE) {
		while (($data = fgetcsv($f, 1000, "\t")) !== FALSE) {
			$row++;
			if($row==3){
				$date1=$data[0];
			}
			$last_date=$data[0];
		}
		$date2=$last_date;
		$d1 = DateTime::createFromFormat('m/d/y', $date1);
		$d1 = $d1 && $d1->format('m/d/y') === $date1;
		$d2 = DateTime::createFromFormat('m/d/y', $date2);
		$d2 = $d2 && $d2->format('m/d/y') === $date2;
		if($d1 && $d2){
			$ret=$date1.",".$date2;
		}
		fclose($f);
	}
    return $ret;
}

function my_standard_deviation(array $a, $sample = false) {
	$n = count($a);
    if ($n === 0) {
		return " ";
    }
    if ($sample && $n === 1) {
		return " ";
    }
    $mean = array_sum($a) / $n;
    $carry = 0.0;
    foreach ($a as $val) {
		$d = ((double) $val) - $mean;
        $carry += $d * $d;
    };
    if ($sample) {
           --$n;
	}
    return sqrt($carry / $n);
}
?>