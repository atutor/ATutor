<?php

global $savant, $_config, $_base_href;
ob_start(); 
//Get the list of entries associated with the current user


$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="webcalendar"';
$result = mysql_query($sql, $db);
while($row = mysql_fetch_array($result)){
	$webcalendar_url_db = $row[1];
}

$sql2 = "SELECT login from ".TABLE_PREFIX."members WHERE member_id ='$_SESSION[member_id]'";
$result10 = mysql_query($sql2, $db);
while($row2 = mysql_fetch_row($result10)){
	$this_login = $row2['0'];
}

$sql9  = "SELECT * from webcal_entry_user WHERE cal_login='$this_login'";
$result9 = mysql_query($sql9, $db);
$today = date("d-M-Y");

echo '<span>'._AT('today_is').': <strong><a href="'.$_base_href.'mods/calendar/">'.$today.'</a></strong></span>';
echo '<small><small> ('._AT('new_window').')</small></small>';
echo '<ul style="margin-left:-2em;">';
while($row = mysql_fetch_array($result9)){
	$today = date(Ymd);
	// Get ten approved entries associated with a user, from today onward.
 	$sql10  = "SELECT A.*, B.* FROM webcal_entry AS A,  webcal_entry_user AS B WHERE A.cal_id='".$row[0]."' AND A.cal_date >= '$today'  AND A.cal_id = B.cal_id AND  (B.cal_status = 'A' || A.cal_access = 'P') AND B.cal_login = '".$this_login."'  LIMIT 10";
 	$result10 = mysql_query($sql10, $db);
	while($row2 = mysql_fetch_assoc($result10)){
		#$this_date = split("*./4", $row2['cal_date']);
		$this_date = split(".*/4", $row2['cal_date']);
		$this_date = chunk_split($row2['cal_date'], 4, '-');
		$this_date = trim(chunk_split($this_date, 7, '-'), '-');
		echo '<li><a href="'.$webcalendar_url_db.'view_entry.php?id='.$row2['cal_id'].SEP.'date='.$row2['cal_date'].'" onclick="window.open(\' '.$webcalendar_url_db.'view_entry.php?id='.$row2['cal_id'].SEP.'date='.$row2['cal_date'].'\',\'calendarwin\',\'width=600,height=520,scrollbars=yes, resizable=yes\'); return false">'.$row2['cal_name'].'</a><br /><small><small> ('.$this_date.')</small></small></li>';
	}
}
echo '</ul>';

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('current_calendar'));
$savant->display('include/box.tmpl.php');

?>
