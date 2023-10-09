<?php
header("Cache-Control: no-cache, must-revalidate");
include_once "includes/variables.php";
session_start();

$_SESSION['log_field_filter']=" ";
$_SESSION['input_log_field_filter']=" ";
$_SESSION['field']=-1;

$_SESSION['log_user_filter']=" ";
$_SESSION['input_log_user_filter']=" ";
$_SESSION['user']=-1;

$_SESSION['log_date_filter']=" ";
$_SESSION['input_log_date_filter']=" ";
$_SESSION['date1']="";
$_SESSION['date2']="";

unset($_SESSION['log_activity_filter']);
unset($_SESSION['log_measurement_filter']);
unset($_SESSION['measurement_category_filter']);

$_SESSION['max_messages']=$max_log_items_per_page;
$_SESSION['reset']=true;
$_SESSION['filter_reminder']="";

header("Location: log.php");

?>