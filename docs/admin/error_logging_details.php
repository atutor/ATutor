<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'server_configuration';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (!isset($_POST['data'])) {
	$msg->addError('NO_PROFILE_SELECTED');
	header('Location: error_logging.php');
	exit;
} // else we have a profile we can work with

if (isset($_POST['delete'])) {

	$key = substr($_POST['data'], 0, strpos($_POST['data'], ':'));
	$date = substr($_POST['data'], strpos($_POST['data'], ':') + 1);
	$dir_ = AT_CONTENT_DIR . 'logs/' . $date;
	$delete_store;
	
	if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $date . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
	
	$cnt = 0;	
	// Open a read pointer to run through each log date directory getting all the profiles
	while (($file = readdir($dir)) !== false) {
		
		if (($file == '.') || ($file == '..') || is_dir($file)) {
			continue;
		}
		
		if (strpos($file, $key) !== false) { // found a bug associated with our profile key
			$delete_store{$file} = $file;
		} else {
			$cnt++;
		}
					
	}
	closedir($dir); // clean it up
	
	// Now run through the files and unlink them all
	foreach($delete_store as $elem => $val) 
		unlink($dir_ . '/' . $elem);
		
	// remove the directory as well if there are no oother profiles in it
	if ($cnt == 0) {
		rmdir($dir_);
	}	
	
	$msg->addFeedback('LOGS_DELETED');
	header('Location: error_logging.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

if (isset($_POST['view'])) {
	// Grab all the bugs associated with this $_POST['data'] corresponding md5 key
	$key = substr($_POST['data'], 0, strpos($_POST['data'], ':'));
	$date = substr($_POST['data'], strpos($_POST['data'], ':') + 1);
	$dir_ = AT_CONTENT_DIR . 'logs/' . $date;
	$log_profiles_bugs;
		
	echo '<br/><h3>' . _AT('viewing_profile_bugs') . '</h3>';
	?>

	<br/><form name="form1" method="post" action="<?php echo 'admin/error_logging_view.php'; ?>">
	
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
		<tr>
			<th class="cat"><?php echo _AT('bug_identifier'); ?></th>
			<th class="cat"><?php echo _AT('timestamp'); ?></th>
		</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<?php
		if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $date . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
		
		// Open a read pointer to run through each log date directory getting all the profiles
		while (($file = readdir($dir)) !== false) {
		
			if (($file == '.') || ($file == '..') || is_dir($file) || (strpos($file, 'profile')	!== false)) {
				continue;
			}
		
			if (strpos($file, $key) !== false) { // found a bug associated with our profile key
				$log_profile_bugs{$file} = $file;
			}
					
		}
		closedir($dir); // clean it up		
		
		if (empty($log_profile_bugs)) { ?>
			<tr>
				<td class="row1" align="center" colspan="2"><small><?php echo _AT('none_found'); ?></small></td>
			</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
		} else {
			$count = 0;
			
			$id_cnt = 1; // give each bug an easier to understand id onscreen
 			foreach ($log_profile_bugs as $elem => $lm) {
				// construct timestamp from millis since epoch in bug identifier
				$timestamp = substr($lm, strpos($lm, '_') + 1);
				$timestamp = substr($timestamp, 0, strpos($lm, '_') + 1);
			
				$timestamp = date("F j, Y, g:i:s a", $timestamp);
			
				$str_prefix = substr($lm, 0, strpos($lm, '_'));
				echo '<tr><td class="row1" style="padding-left: 10px;"><small><label><input type="checkbox" value="'. $date . '/' . $lm . '" name="file' . $count . '" />';
				echo ''. $id_cnt . '_' . $str_prefix .'</label></small></td>';
				echo '<td class="row1" align="center"><small>' . $timestamp .'</small></td>';
				echo '</tr>';
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
				$count++;
				$id_cnt++;
			}
			
			echo '<input type="hidden" value="'. $count . '" name="count"/>';
		}
		?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" align="center" colspan="2">
				<input type="hidden" name="profile_id" value="<?php echo $key; ?>"/>
				<input type="hidden" name="profile_date" value="<?php echo $date; ?>"/>
				<br /><input type="submit" name="view" value="<?php echo _AT('view_selected_bugs'); ?>" class="button" />
			</td></tr><tr><td class="row1" align="center" colspan="2"><br /></td></tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
			<tr><td class="row1" align="center" colspan="2"><br /><input type="submit" name="view_profile" value="<?php echo 'View This Profile'; ?>" class="button" /> -
				<input type="submit" name="back" value="<?php echo _AT('back_to_main'); ?>" class="button" /><br/><br/> 				  
			</td>
		</tr>
		</table>

		</form>
	<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
		
} else if (isset($_POST['delete'])) {
	$key = substr($_POST['data'], 0, strpos($_POST['data'], ':'));
	$date = substr($_POST['data'], strpos($_POST['data'], ':') + 1);
	$dir_ = AT_CONTENT_DIR . 'logs/' . $date;
	$delete_store;
	
	if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $date . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
		
	// Open a read pointer to run through each log date directory getting all the profiles
	while (($file = readdir($dir)) !== false) {
		
		if (($file == '.') || ($file == '..') || is_dir($file)) {
			continue;
		}
		
		if (strpos($file, $key) !== false) { // found a bug associated with our profile key
			$delete_store{$file} = $file;
		}
					
	}
	closedir($dir); // clean it up
	
	// Now run through the files and unlink them all
	foreach($delete_store as $elem => $val) 
		unlink($dir_ . '/' . $elem);
		
	$msg->addFeedback('LOGS_DELETED');
	header('Location: ' . $_SERVER['PHP_SELF']);
} 	