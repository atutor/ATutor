<?php

if($_POST['save_prefs']){
	global $addslashes,$msg;
	$private_xml = $addslashes($_POST['gcal_xml']);
	$private_html =  $addslashes($_POST['gcal_html']);
	$timezone =  $addslashes($_POST['timezone']);
	
	$sql = "REPLACE into ".TABLE_PREFIX."google_prefs SET member_id = '$_SESSION[member_id]', private_xml = '$private_xml', private_html = '$private_html', timezone='$timezone'";

	if($result = mysql_query($sql, $db)){
		$msg->addFeedback('GOOGLE_CAL_UPDATED');
	}else{
		$msg->addError('GOOGLE_CAL_UPDATE_FAILED');
	}
}


$sql = "SELECT email from ".TABLE_PREFIX."members WHERE member_id = '$_SESSION[member_id]'";
$result=mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$my_email_address = $row[0];
}


$sql="SELECT * from ".TABLE_PREFIX."google_prefs WHERE member_id = '$_SESSION[member_id]'";
$result=mysql_query($sql, $db);

while($row = mysql_fetch_assoc($result)){
	$private_xml = $row['private_xml'];
	$private_html= $row['private_html'];
	$timezone = $row['timezone'];
}

if($private_xml){
	$calendar_xml = $private_xml;
}else{
	$calendar_xml  = "http://www.google.com/calendar/feeds/".$my_email_address."/public/basic";
	$private_message = '<span style="color:red;">'._AT('google_calendar_permission').' </span>';
}

$calendar_xml = str_replace('%40','%2540',$calendar_xml);

if($private_html){
	$calendar_html = $private_html;
}else{
	$calendar_html  = "http://www.google.com/calendar/feeds/".$my_email_address."/public/basic";
}


if($timezone == ''){
	$timezone = date_default_timezone_get();
}
?>