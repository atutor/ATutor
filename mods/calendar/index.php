<?php
/*
This is the main ATutor webcalendar module page. It allows users to access
the course and personal calendars through courses that have the calendar enabled
*/
global $webcalendar_url_db;
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//
//$_custom_css = $_base_path . 'mods/calendar/module.css';

//////////
//Check to see if the url to webcalendar exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="webcalendar"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$webcalendar_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');

$sql="SELECT password  FROM ".TABLE_PREFIX."members WHERE login='$_SESSION[login]'";
$result = mysql_query($sql, $db);
while($row = mysql_fetch_array($result)){
	$pw =  $row[0];
}
?>
<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $webcalendar_url_db; ?>index.php','calendarwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('calendar_own_window'); ?></a></p>

<?php
if($webcalendar_url_db == ''){
	$msg->addInfo('WEBCALENDAR_URL_ADD_REQUIRED');
}else{
?>
	<iframe name="calendar" id="calendar" title="WebCalendar" scrolling="yes" src="<?php echo $webcalendar_url_db; ?>index.php" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php 
}

 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>