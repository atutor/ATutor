<?php
/*
This is the ATutor webcalendar module page. It allows an admin user
to set or edit  the URL for the webcalendar installation for ATutor, and
it includes the launcher, which opens webcalendar in a new window

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if($_REQUEST['saved_webcalendar_url'] == 1){
	if($_REQUEST['webcalendar_url'] == ''){
			$msg->addError('WEBCALENDAR_URL_ADD_EMPTY');
	}else{
		$webcalendar_url = addslashes(stripslashes($_REQUEST['webcalendar_url']));
		$sql = "INSERT INTO ".TABLE_PREFIX."config VALUES('webcalendar', '".$webcalendar_url."')";
		if(!$result = mysql_query($sql)){
			$msg->addError('WEBCALENDAR_URL_ADD_FAILED');
		}else{
			$msg->addFeedback('WEBCALENDAR_URL_ADD_SAVED');
		}
	}
}

if($_REQUEST['edited_webcalendar_url'] == 1){
	if($_REQUEST['webcalendar_url'] == ''){
			$msg->addError('WEBCALENDAR_URL_ADD_EMPTY');
			//	$_POST['edit_webcalendar_url'] = 1;
			
	}else{
		$webcalendar_url = addslashes(stripslashes($_REQUEST['webcalendar_url']));
		$sql = "UPDATE ".TABLE_PREFIX."config SET  value='".$webcalendar_url."' WHERE name = 'webcalendar'";
		if(!$result = mysql_query($sql)){
			$msg->addError('WEBCALENDAR_URL_ADD_FAILED');
		}else{
			$msg->addFeedback('WEBCALENDAR_URL_ADD_SAVED');
		}
	}
}

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


	$sql = "SELECT * from ".TABLE_PREFIX."admins";
	if(!$result3 = mysql_query($sql)){
		$msg->addError('WEBCALENDAR_UPDATE_ADMINS_FAILED');
	}else{
		$msg->addFeedback('WEBCALENDAR_UPDATE_ADMINS_SAVED');
	}
	while ($row = mysql_fetch_array($result3)){
		$sql3  = "REPLACE INTO webcal_user VALUES ('$row[0]','".md5($row[1])."','$row[2]','','Y','$row[3]')";
		$result4 = mysql_query($sql3, $db);
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

//////////
//Check to see if the url to webcalendar exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="webcalendar"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$webcalendar_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');

if($webcalendar_url_db == '' || $_POST['edit_webcalendar_url']){ 

?>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('webcalendar_add_url'); ?>
		</p>
			<div class="row buttons">
			<form action="<?php $_SERVER['PHP_SELF']?>" method="post">

			<?php if($_POST['edit_webcalendar_url']){ ?>
				<input type="hidden" name="edited_webcalendar_url" value="1">
			<?php }else{ ?>
				<input type="hidden" name="saved_webcalendar_url" value="1">
			<?php } ?>
			<?php if($_POST['edit_webcalendar_url']){ ?>
				<input type="text" name="webcalendar_url" value="<?php echo $webcalendar_url_db; ?>" size="80" length="150" />
				<?php }else{ ?>
				<input type="text" name="webcalendar_url" value="<?php echo $webcalendar_url; ?>" size="80" length="150" />
			
			<?php } ?>	
			<input type="submit" value="<?php echo _AT('webcalendar_save'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>

<?php }else{?>

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
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('webcalendar_location'); ?></p>
			<p>	<strong><?php echo $webcalendar_url_db; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="edit_webcalendar_url" value="1">
			<input type="submit" value="<?php echo _AT('webcalendar_edit'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>


		<div class="input-form">
		<div class="row">




<?php } 

?>

<?php 

 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>