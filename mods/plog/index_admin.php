<?php
/*
This is the ATutor admin plog module page. It allows an admin user
to access all blog admin features through the default blog created
when LifeType/pLog was installed.
*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//////////
//Check to see if the url to plog exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="plog"';
$result = mysql_query($sql, $db);
while($row = mysql_fetch_array($result)){
	$plog_url_db = $row[1];
}
if($plog_url_db == ''){

	$msg->addInfo('PLOG_URL_ADD_REQUIRED');
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
	<iframe name="plog" id="plog" title="pLog" scrolling="yes" src="<?php echo $plog_url_db; ?>summary.php" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
	<?php 
	
	
	require (AT_INCLUDE_PATH.'footer.inc.php'); 

}?>