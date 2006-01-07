<?php
/*
This is the main ATutor webcalendar module page. It allows users to access
the course and personal calendars through courses that have the calendar enabled
*/
global $webcalendar_url_db;
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// switch the comments in the two lines to switch between LifeType 1.0.2 and 1.1
//require ('sync_plog11.php');
require ('sync_plog.php');


//////////
//Check to see if the url to webcalendar exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="plog"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$plog_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('plog_sync'); ?></p>
			<p>	<strong><?php echo $plog_sync; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="plog_sync" value="1">
			<input type="submit" value="<?php echo _AT('plog_sync_button'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>


<?php
// Display the appropriate pLog screen: open in a new window, pLog login, the pLog summary screen, course blog
// Comment out or remove the summary link below if you want to hide the Public blog from users.
?>
<div id="topnavlistcontainer">
<ul id="topnavlist">
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $plog_url_db; ?>index.php?blogId=<?php echo $_SESSION['course_id']; ?>','plogwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('plog_own_window'); ?></a> </li>
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?enter_blog=1;blogId=<?php echo $_SESSION['course_id']; ?>"> <?php echo  _AT('plog_login'); ?></a> </li>
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?summary_blog=1;blogId=<?php echo $_SESSION['course_id']; ?>"> <?php echo  _AT('plog_summary'); ?></a> </li>
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?course_blog=1;blogId=<?php echo $_SESSION['course_id']; ?>"> <?php echo  _AT('plog_course'); ?></a></li>
</ul>
</div>
<?php

if($plog_url_db == ''){
	$msg->addInfo('PLOG_URL_ADD_REQUIRED');
}elseif ($_GET['enter_blog']){
?>
	<iframe name="plog" id="plog" title="pLog" scrolling="yes" src="<?php echo $plog_url_db; ?>admin.php?blogId=<?php echo $_SESSION['course_id']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php 
}elseif ($_GET['summary_blog']){
?>
	<iframe name="plog" id="plog" title="pLog" scrolling="yes" src="<?php echo $plog_url_db; ?>summary.php?blogId=<?php echo $_SESSION['course_id']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php 
}elseif ($_GET['course_blog']){
?>
	<iframe name="plog" id="plog" title="pLog" scrolling="yes" src="<?php echo $plog_url_db; ?>index.php?blogId=<?php echo $_SESSION['course_id']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php 
}else{ ?>
	<iframe name="plog" id="plog" title="pLog" scrolling="yes" src="<?php echo $plog_url_db; ?>index.php?blogId=<?php echo $_SESSION['course_id']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php
}

 require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>