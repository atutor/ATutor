<?php
/*
This is the ATutor webcalendar module page. It allows an admin user
to set or edit  the URL for the webcalendar installation for ATutor and
synchronize the ATutor and WebCalendar databases

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//////////
//Check to see if the url to webcalendar exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="webcalendar"';
$result = mysql_query($sql, $db);
while($row = mysql_fetch_array($result)){
	$webcalendar_url_db = $row[1];
}
if($webcalendar_url_db == ''){

	$msg->addInfo('WEBCALENDAR_URL_ADD_REQUIRED');
	require (AT_INCLUDE_PATH.'header.inc.php');
	require (AT_INCLUDE_PATH.'footer.inc.php');

}else{

	require (AT_INCLUDE_PATH.'header.inc.php');
	$sql="SELECT *  FROM ".TABLE_PREFIX."admins WHERE login='$_SESSION[login]'";
	$result = mysql_query($sql, $db);
	
	while($row = mysql_fetch_array($result)){
		$pw =  $row[1];
	}
	
	?>
	<iframe name="calendar" id="calendar" title="WebCalendar" scrolling="yes" src="<?php echo $webcalendar_url_db; ?>week.php" height="800" width="100%" align="center" style="border:thin white solid; align:center;"></iframe>
	<?php 
	
	
	require (AT_INCLUDE_PATH.'footer.inc.php'); 

}?>