<?php
/*
This is the ATutor webcalendar module page. It allows an instructor to synchronize
the ATutor and WebCalendar databases.
*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if($_REQUEST['webcalendar_sync'] == 1){
	//populate the webcal_user table with ATutor members
	$sql = "SELECT * from ".TABLE_PREFIX."members";
	$result = mysql_query($sql,$db);
	if(!$result3 = mysql_query($sql)){
		$msg->addError('WEBCALENDAR_UPDATE_MEMBERS_FAILED');
	}else{
		$msg->addFeedback('WEBCALENDAR_UPDATE_MEMBERS_SAVED');
	}
	while ($row = mysql_fetch_array($result)){
		$sql2  = "REPLACE INTO webcal_user VALUES ('$row[1]','".md5($row[2])."','$row[6]','$row[5]','N','$row[3]')";
		$result1 = mysql_query($sql2, $db);
 	}

	//populate the webcal_group table with ATutor courses
	$sql5 = "SELECT * FROM ".TABLE_PREFIX."courses";
	$result5 = mysql_query($sql5,$db);
	while ($row = mysql_fetch_array($result5)){
		$sql2  = "REPLACE INTO webcal_group VALUES ('$row[0]','".$row[1]."','$row[6]','$row[5]')";
		$result1 = mysql_query($sql2, $db);
 	}
	if(!$result1 = mysql_query($sql)){
		$msg->addError('WEBCALENDAR_UPDATE_GROUPS_FAILED');
	}else{
		$msg->addFeedback('WEBCALENDAR_UPDATE_GROUPS_SAVED');
	}

	//populate webcal_group_users users enrolled courses (i.e. groups) 

	$sql6 = "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE approved='y'";
	$result6 = mysql_query($sql6,$db);
	while ($row = mysql_fetch_array($result6)){

		$sql8 = "SELECT login FROM ".TABLE_PREFIX."members WHERE member_id='$row[0]'";
		$result8 = mysql_query($sql8,$db);
		while($row1 = mysql_fetch_array($result8)){
			$at_login_name = $row1[0];
		}
		$sql2  = "REPLACE INTO webcal_group_user VALUES ('$row[1]','$at_login_name')";
		if(!$result7 = mysql_query($sql2)){
			$msg->addError('WEBCALENDAR_UPDATE_GROUPMEMS_FAILED');
		}else{
			$msg->addFeedback('WEBCALENDAR_UPDATE_GROUPMEMS_SAVED');
		}
 	}


}

require (AT_INCLUDE_PATH.'header.inc.php');

?>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('webcalendar_sync'); ?></p>
			<p>	<strong><?php echo $webcalendar_sync; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="webcalendar_sync" value="1">
			<input type="submit" value="<?php echo _AT('webcalendar_sync_button'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>
		
<?php 

 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>