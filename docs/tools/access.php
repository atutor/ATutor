<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN);

if (isset($_POST['regenerate'])) {
	$password = strtoupper(substr(md5(rand()), 3, 8));

	$sql = "UPDATE ".TABLE_PREFIX."course_access SET `password`='$password' WHERE course_id=".$_SESSION['course_id'];
	$result = mysql_query($sql, $db);
	if (!mysql_affected_rows($db)) {
		// conflict. try again
		$password = strtoupper(substr(md5(rand()), 2, 7));
		$sql = "UPDATE ".TABLE_PREFIX."course_access SET `password`='$password' WHERE course_id=".$_SESSION['course_id'];
		$result = mysql_query($sql, $db);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['submit'])) {
	$auth = intval($_POST['auth']);

	//expiry date
	if (intval($_POST['expiry_date'])) {
		$day_expire		= intval($_POST['day_expire']);
		$month_expire	= intval($_POST['month_expire']);
		$year_expire	= intval($_POST['year_expire']);
		$hour_expire	= intval($_POST['hour_expire']);
		$min_expire		= intval($_POST['min_expire']);

		if (strlen($month_expire) == 1){
			$month_expire = "0$month_expire";
		}
		if (strlen($day_expire) == 1){
			$day_expire = "0$day_expire";
		}
		if (strlen($hour_expire) == 1){
			$hour_expire = "0$hour_expire";
		}
		if (strlen($min_expire) == 1){
			$min_expire = "0$min_expire";
		}
		$expiry_date = "$year_expire-$month_expire-$day_expire $hour_expire:$min_expire:00";
	} else {
		$expiry_date = 0;
	}

	$sql = "UPDATE ".TABLE_PREFIX."course_access SET `expiry_date`='$expiry_date', enabled=$auth WHERE course_id=".$_SESSION['course_id'];
	$result = mysql_query($sql, $db);
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

if ($system_courses[$_SESSION['course_id']]['access'] == 'public') { 
	// if this course is public, then we can't use this feature
	echo '<div class="toolcontainer">';
	$msg->printInfos('ACCESS_PUBLIC');
	echo '</div>';
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

$sql = "SELECT password, expiry_date+0 AS expiry_date, enabled FROM ".TABLE_PREFIX."course_access WHERE course_id=".$_SESSION['course_id'];
$result = mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)) {		
	$enabled = $row['enabled'];
	$password = $row['password'];
	$expiry = $row['expiry_date'];
} else {
	$enabled = 0;
	$password = strtoupper(substr(md5(rand()), 3, 8));
	$expiry = 0;
	$sql = "INSERT INTO ".TABLE_PREFIX."course_access VALUES ('$password', {$_SESSION['course_id']},'0000-00-00 00:00:00', 0)";
	$result = mysql_query($sql, $db);
}
$url = AT_BASE_HREF.'acl.php?'.$password;

?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<div class="input-form">
		<fieldset class="group_form"><legend class="group_form"><?php echo _AT('regenerate'); ?></legend>
			<div class="row">				
				<?php echo _AT('auth_access_text'); ?>
			</div>
			<div class="row">
				<?php echo _AT('url'); ?><br />
				<kbd><?php echo $url; ?></kbd>
			</div>
			<div class="row buttons">
				<input type="submit" name="regenerate" value="<?php echo _AT('regenerate'); ?>"  />
			</div>
		</fieldset>
		</div>

		<div class="input-form">
		<fieldset class="group_form"><legend class="group_form"><?php echo _AT('authenticated_access'); ?></legend>
			<div class="row">
				<?php echo _AT('authenticated_access'); ?><br />
				<input type="radio" name="auth" id="enable" value="1" <?php if($enabled) { echo 'checked="checked"'; } ?> /> <label for="enable"><?php echo _AT('enable'); ?></label> <input type="radio" name="auth" id="disable" value="0" <?php if(!$enabled) { echo 'checked="checked"'; } ?> /> <label for="disable"><?php echo _AT('disable'); ?></label> 
			</div>

			<div class="row">
				<?php echo _AT('expiry_date'); ?><br />
				<?php
					$exp_no = $exp_yes = '';

					if (intval($expiry)) {
						$exp_yes = ' checked="checked"';

						$today_day   = substr($expiry, 6, 2);
						$today_mon   = substr($expiry, 4, 2);
						$today_year  = substr($expiry, 0, 4);
						$today_hour  = substr($expiry, 8, 2);
						$today_min   = substr($expiry, 10, 2);

					} else {
						$exp_no = ' checked="checked"'; 
						$today_day	 = date('d');
						$today_mon	 = date('m');
						$today_year  = date('Y');
					}
				?>

				<input type="radio" name="expiry_date" value="0" id="expire_never" <?php echo $exp_no; ?> /> <label for="expire_never"><?php echo _AT('expire_never'); ?></label><br />

				<input type="radio" name="expiry_date" value="1" id="expire_on" <?php echo $exp_yes; ?> /> <label for="expire_on"><?php echo _AT('expire_on'); ?></label> 
				<?php
					$name = '_expire';
					require(AT_INCLUDE_PATH.'html/release_date.inc.php');
				?>
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" /> 
				<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
			</div>
			</fieldset>
		</div>
	</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>